<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InvoiceFilesRequest;
use App\Http\Requests\Api\TestRequest;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function submitEfrisForm(InvoiceFilesRequest $request){
        return $request->saveEfris('invoice_temp_files');
    }
    public function submitForm(TestRequest $request){
        return $request->save();
    }

    public function submitFile(InvoiceFilesRequest $request){
        set_time_limit(0);
       return $request->saveFile('invoice_temp_files');
    }

    public function removeFile(InvoiceFilesRequest $request){

        return $request->removeFileFile();
     }
}
