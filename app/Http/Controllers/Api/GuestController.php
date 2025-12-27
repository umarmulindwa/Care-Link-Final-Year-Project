<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuestController extends Controller
{
    public function serializeList(Request $request) {
        if($request->has('data_key')) {
            $data = collect($request->input($request->data_key))->values()->all();
        } else {
            $data = collect($request->all())->values()->all();
        }
        return response()->json($data);
    }

    public function serializeObject(Request $request) {
        return response()->json($request->all());
    }
}
