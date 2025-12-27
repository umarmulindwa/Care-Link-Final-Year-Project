<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use App\Models\SupportRequest;
use App\Models\StaffProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Mail\Support\SupportRequestEmail;
use App\Mail\Support\SupportReplyEmail;
use App\Models\SupportFiles;
use App\Models\SupportResponse;
use App\Models\User;
use App\Models\Video;


class SupportController extends Controller
{
    public function supportRequest()
    {
        return Inertia::render(
            'Support/SupportRequest'
        );
    }
    public function submitSupportRequest(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required'],
            'email' => 'required',
            'issue' => 'required',
            'issueType' => 'required',
            'detailedDescription' => 'required'
        ]);




        //getting all super Admins
        $admins = User::select('email', 'name')->permission('s_admin')->get();

        // $issueDetails = json_decode($request->issueDetails);


        // Save the issue in the db
        $supportRequest = SupportRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'issue_category' => $request->issueType,
            'issue' => $request->issue,
            'description' => $request->detailedDescription,
        ]);

        //saving files

        if ($request->file('files') != null && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $supportFile) {
                $storedFile = $this->storeFile('/uploads/supportFiles/', $supportFile, 'Support Document');
                SupportFiles::create([
                    'filepath' => $storedFile->file_path,
                    'support_requests_id' => $supportRequest->id,
                    'submittedBy' => $request->name
                ]);
            }
        }


        // sending support request email to all  super admins
        if (count($admins) > 0) {
            $issueDetails = [
                'name' => $request->name,
                'email' => $request->email,
                'issue_category' => $request->issueType,
                'issue' => $request->issue,
                'description' => $request->detailedDescription,
            ];
            foreach ($admins as $admin) {
                //checking whether the staff is on Leave
                $staff = StaffProfile::where('email', $admin->email)->first();
            
                try {
                    $subject =  "New Support Request.";
                    $mail = new SupportRequestEmail($admin->name, $issueDetails, $admin->name);
                    globalSendEmail($staff, $subject, $mail, null, false, $supportRequest->id, [
                        "personal_id" => $staff->email,
                        "action" => env('APP_URL') . "/supportCenter",
                        "description" => "A new support request has been submitted by " . auth()->user()->name . " Kindly action the request",
                        "submitted_by" => auth()->user()->name,
                        "title" => $subject,
                        "profile_photo_path" => null
                    ]);
                } catch (\Exception $ex) {
                }
            }

            return apiResponse('success');
        }
    }

    public function supportCenter()
    {

        $generalSupportRequests =  SupportRequest::where('closed', 0)->where('issue_category', 'General Support')->with(['responses', 'files'])->latest()->get();
        $accountProfileRequests =  SupportRequest::where('closed', 0)->where('issue_category', 'Account and Profile')->with(['responses', 'files'])->latest()->get();
        $systemVideos = Video::with(['views'])->latest()->get();


        return Inertia::render(
            'Support/SuperAdminSupportDashboard',
            [
                'generalRequests' => $generalSupportRequests,
                'accountProfileRequests' => $accountProfileRequests,
                'systemVideos' => $systemVideos,
            ]
        );
    }

    public function uploadSystemVideo(Request $request)
    {
        $videoPath = null;

        //saving video files
        if (!is_null($request->file('videoFile'))) {
            $videoFile = $request->file('videoFile');
            $name = $videoFile->getClientOriginalName();
            // $videoPath = $videoFile->storeAs('/uploads/systemVideos', time() . '_' . $name);
            $storedFile = $this->storeFile("/uploads/systemVideos/", $videoFile, 'System Video');
            $videoPath = $storedFile->file_path;
        }

        $videoId = $request->has('id') ? $request->id : null;

        $savedVideo = Video::updateOrCreate(['id' => $videoId], [
            "video_title" => $request->videoTitle,
            "video_path" => $videoPath,
            "embed_url" => $request->videoURL,
            "location" => $request->videoLocation,
            "about_the_video" => $request->videDescription,
            "uploaded_by" => auth()->user()->name
        ]);

        return apiResponse('success');
    }

    public function submitSupportResponse(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required'],
            'response' => 'required',
            'support_request_id' => 'required',
            'name' => 'required',
            'email' => 'required',
        ]);

        //support issue
        $supportIssue = SupportRequest::select('issue')->where('id', $request->support_request_id)->first();
        $supportDetails = [
            'origin_name' => $request->name,
            'admin_name' => auth()->user()->name,
            'issue' => $supportIssue->issue,
            'response' => $request->response
        ];
        //saving response
        $supportResponse = SupportResponse::create([
            'support_request_id' => $request->support_request_id,
            'name' => $request->name,
            'email' => $request->email,
            'title' => $request->title,
            'body' => $request->response,
            'response_by' => auth()->user()->name,
        ]);

        //saving files


        if ($request->file('files') != null && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $supportFile) {
                $storedFile = $this->storeFile('/uploads/supportFiles/', $supportFile, 'Support Document');
                SupportFiles::create([
                    'filepath' => $storedFile->file_path,
                    'support_requests_id' => $supportResponse->id,
                    'submittedBy' => auth()->user()->name
                ]);
            }
        }

        //send mail to the originator
     
        try {
            $subject =  $request->title;
            $mail = new SupportReplyEmail($supportDetails, $request->title);
            $staff = StaffProfile::where('email', $request->email)->first();

            if($staff != null){
                globalSendEmail($staff, $subject, $mail, null, false, $supportResponse->id, [
                    "personal_id" => $staff->email,
                    "action" => env('APP_URL') . "/support",
                    "description" => "A new support response has been submitted by " . auth()->user()->name ,
                    "submitted_by" => auth()->user()->name,
                    "title" => $subject,
                    "profile_photo_path" => null
                ]);
            }else{
                //TODO: send response email for non unicef staff
            }
          
        } catch (\Exception $ex) {
        }

        return apiResponse('success');
    }

    public function closeSupportRequest(Request $request)
    {
        $supportRequests = SupportRequest::where('email', $request->email)->get();

        foreach ($supportRequests as $req) {
            $req->closed = true;
            $req->save();
        }
        return response()->json('success', 200);
    }

    //storing files
    function storeFile($path, $file)
    {
        $filen = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
        $filename = $path . $filen . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->put($filename, File::get($file));
        return (object)["filename" => $file->getClientOriginalName(), "file_path" => $filename];
    }
}
