<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MobileApp\DeleteApkRequest;
use App\Http\Requests\MobileApp\UploadApkRequest;
use App\Http\Controllers\Controller;
use App\Models\MobileDownload;
use App\Models\MobileUpload;

class MobileController extends Controller
{
    public function uploadApk(UploadApkRequest $request)
    {
        return $request->upload();
    }

    public function deleteApk(DeleteApkRequest $deleteApkRequest)
    {
        return $deleteApkRequest->delete();
    }

    public function getDownloadData()
    {
        $data = MobileUpload::latest()->limit(50)->get();
        return apiResponse($data);
    }

    public function getLatestDownloadData()
    {
        $data = MobileUpload::latest()->first();
        return apiResponse($data);
    }

    public function downloadApk()
    {
        try {

            $formerVersion = request()->query('version');

            if (is_null($formerVersion)) {
                $country_office = env("VITE_COUNTRY_OFFICE", "") . " DOPS";
                $data = MobileUpload::latest()->first();

                $mediaItems = $data->getMedia('mobileuploads')->last();
                if ($mediaItems == null) {
                    return apiErrorResponse("Not File Found");
                }

                MobileDownload::create([
                    'user_id' => auth()->user()->id,
                    'file_id' => $data->id
                ]);
                return response()->file($mediaItems->getPath(), [
                    'Content-Type' => 'application/vnd.android.package-archive',
                    'Content-Disposition' => 'attachment; filename="UNICEF ' . $country_office . '.apk"',
                ]);
            } else {
                $country_office = env("VITE_COUNTRY_OFFICE", "") . " DOPS";
                $data = MobileUpload::latest()->first();

                if($data != null){
                    if($data->version == $formerVersion){
                        return apiErrorResponse(['message'=>'Application is up to date.','status'=>'OK']);
                    }
                }

                $mediaItems = $data->getMedia('mobileuploads')->last();
                if ($mediaItems == null) {
                    return apiErrorResponse("Not File Found");
                }

                MobileDownload::create([
                    'user_id' => auth()->user()->id,
                    'file_id' => $data->id
                ]);
                return response()->file($mediaItems->getPath(), [
                    'Content-Type' => 'application/vnd.android.package-archive',
                    'Content-Disposition' => 'attachment; filename="UNICEF ' . $country_office . '.apk"',
                ]);
            }
        } catch (\Throwable $th) {
            return apiErrorResponse('Mobile Downloads Fail: ' . $th->getMessage(), ['error' => $th->getMessage()]);
        }
    }
}
