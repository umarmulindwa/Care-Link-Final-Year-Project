<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\PrivateChat;
use App\Models\GeneralChat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;


class ChatController extends Controller
{
    
    public function index()
    {
        $currentUser = Auth::user();
        $generalChat = GeneralChat::get();
        $privateChats = PrivateChat::where('sent_by_email', $currentUser->email)->orWhere('to_email', $currentUser->email)->get();
        $allUsers = User::whereNot('email', $currentUser->email)->latest()->get();
        return   \Inertia\Inertia::render('Chat/MainPage', [
            'generalChat' => $generalChat,
            'privateChats' => $privateChats,
            'allUsers' => $allUsers
        ]);
    }
    public function generalChat(Request $request)
    {

    
        $currentUser = Auth::user();
        $theGeneralChat = GeneralChat::create([
            'user_id' => $currentUser->id,
            'title' => $request->title,
            'message' => $request->message,
            'sent_by_name' => $currentUser->name,
            'sent_by_email' => $currentUser->email,
        ]);



        //saving media files
        if ($request->file('files') != null && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $chatFile) {
                $theGeneralChat->addMedia($chatFile)->toMediaCollection('chat_files');
            }
        }

        //send Notification
        Notification::create([
            'user_id' => $currentUser->id,
            'personal_id' => $currentUser->email,
            'submitted_by_name' => $currentUser->name,
            'submitted_by_email' => $currentUser->email,
            'to_name' => 'All',
            'to_email' => 'All',
            'title' => 'New message from ' . $currentUser->name,
            'message' => $this->truncateString($theGeneralChat->message),
            'profile_photo_path' => $request->profilePicUrl
        ]);
    }
    public function generalChatm(Request $request)
    {

        $currentUser = User::where('email',$request['email'])->first();

        $theGeneralChat = GeneralChat::create([
            'user_id' => $currentUser->id,
            'title' => "Mobile",
            'message' => $request['message'],
            'sent_by_name' => $currentUser->name,
            'sent_by_email' => $currentUser->email,
        ]);

    
        //send Notification
        Notification::create([
            'user_id' => $currentUser->id,
            'personal_id' => $currentUser->email,
            'submitted_by_name' => $currentUser->name,
            'submitted_by_email' => $currentUser->email,
            'to_name' => 'All',
            'to_email' => 'All',
            'title' => 'New message from ' . $currentUser->name,
            'message' => $this->truncateString($theGeneralChat->message),
        ]);
    }
    public function privateChat(Request $request)
    {
        $currentUser = Auth::user();
        $toUser = User::where('email', $request->messageToEmail)->first();
        $theGeneralChat = PrivateChat::create([
            'user_id' => $currentUser->id,
            'to_user_id' => $toUser->id,
            'title' => $request->title,
            'message' => $request->message,
            'sent_by_name' => $currentUser->name,
            'sent_by_email' => $currentUser->email,
            'to_name' => $toUser->name,
            'to_email'  => $toUser->email,
        ]);

        //saving media files
        if ($request->file('files') != null && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $chatFile) {
                $theGeneralChat->addMedia($chatFile)->toMediaCollection('chat_files');
            }
        }

        //send Notification
        Notification::create([
            'user_id' => $currentUser->id,
            'personal_id' => $toUser->email,
            'submitted_by_name' => $currentUser->name,
            'submitted_by_email' => $currentUser->email,
            'to_name' => $theGeneralChat->to_name,
            'to_email' => $theGeneralChat->to_email,
            'title' => 'New message from ' . $currentUser->name,
            'message' => $this->truncateString($theGeneralChat->message),
            'profile_photo_path' => $request->profilePicUrl
        ]);
    }

    private function truncateString($string, $length = 30, $append = '...')
    {
        if (strlen($string) > $length) {
            return substr($string, 0, $length) . $append;
        }
        return $string;
    }
}