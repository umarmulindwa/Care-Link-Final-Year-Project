<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaceFormController extends Controller
{
    //

    public function createFace()
    {
        try {
            //code...
            return apiResponse('Success');
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse('Error: ' . $th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }
    public function listFace()
    {
        try {
            //code...
            return apiResponse('Success');
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse('Error: ' . $th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }
    public function reportsFace()
    {
        try {
            //code...
            return apiResponse('Success');
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse('Error: ' . $th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }
}
