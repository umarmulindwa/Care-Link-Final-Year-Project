<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffProfile;
use App\Models\Searchlog;

class MainSearchController extends Controller
{
    public function getSearchResults(Request $request)
    {

        $searchWithIn = $request->searchWithIn;
        $searchText = $request->searchText;
        $searchResults = [];

        if ($searchWithIn == 'Users') {
            $user = auth()->user();
            $userPermissions = $user->getAllPermissions();
            $isSuperAdmin = $userPermissions->contains(function ($item) {
                return $item->name == 's_admin';
            });
            if ($isSuperAdmin) {
                $users = StaffProfile::where('name', 'like', '%' . $searchText . '%')
                    ->OrWhere('email', 'like', '%' . $searchText . '%')
                    ->with(['user', 'staffIdentity'])->get();
                $searchResults = $users;
            } else {
                $searchResults = "You don't have permissions to access users";
            }
        }

        //logging serarch
        Searchlog::create([
            'search_text' => $searchText,
            'search_within' => $searchWithIn,
            'search_by_name' => auth()->user()->name,
            'search_by_email' => auth()->user()->email,
        ]);

        return \Inertia\Inertia::render('MainSearch/MainSearch', [
            'searchWithIn' => $searchWithIn,
            'searchText' => $searchText,
            'searchResults' => $searchResults
        ]);
    }
}
