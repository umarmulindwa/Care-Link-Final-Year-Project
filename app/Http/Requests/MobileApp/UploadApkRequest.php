<?php

namespace App\Http\Requests\MobileApp;

use App\Models\MobileUpload;
use Illuminate\Foundation\Http\FormRequest;

class UploadApkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->id ? [
            "version" => "required",
            "comment" => "required",
        ] : [
            "version" => "required",
            "comment" => "required",
            "apk" => "required"
        ];
    }

    public function upload()
    {
        try {
            $filePath = "";
            $size = "";
            $data = $this->all();
            // if ($this->hasFile('file')) {
            //     $size = $this->humanFilesize($this->file->getSize());
            //     $name = 'unicef-' . time() . '.' . $this->file->getClientOriginalExtension();
            //     $this->file->move(public_path('/apk/'), $name);
            //     $filePath = public_path('/apk/') . $name;
            // } else {
            //     $mobileUpload = MobileUpload::find($this->id);
            //     $filePath = $mobileUpload->file_path;
            //     $size = $mobileUpload->size;
            // }

            $filePath = public_path() . '/storage/' . $data['apk']['tmp'];




            $mobileUpload = MobileUpload::updateOrCreate(["id" => $this->id], [
                "version" => $this->version,
                "comment" => $this->comment,
                "file_path" => "none",
                "size" => "staged"
            ]);

            $mobileUpload->addMedia($filePath)->toMediaCollection('mobileuploads');
            if ($mobileUpload->getMedia('mobileuploads')->last() != NULL) {
                $mobileUpload->update([
                    "file_path" => $mobileUpload->getMedia('mobileuploads')->last()->getFullUrl(),
                    "size" => $this->humanFilesize($mobileUpload->getMedia('mobileuploads')->last()->size)
                ]);
            }

            return apiResponse($data);
        } catch (\Throwable $th) {
            report($th);
            return apiErrorResponse($th->getMessage(), ['error' => $th->getTrace()]);
        }
    }

    private function humanFilesize($bytes, $dec = 2)
    {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
