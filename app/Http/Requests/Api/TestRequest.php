<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class TestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }



    public function save()
    {
        try {
            $item = null;
            $supp_files = [];
            if (isset($this->invoice)) {
                $item = (object) $this->invoice;
                $str = $this->invoice['tmp'];
                $str = str_replace('invoice_temp_files', 'invoice_files', $this->invoice['tmp']);
                Storage::disk('public')->move($this->invoice['tmp'], $str);
                $item->permanentPath = $str;
            }
            if (isset($this->supporting_documents)) {
                $supp_files = $this->supporting_documents;
                foreach ($supp_files as $key => $value) {
                    $itemAR = (object) $value;
                    $str = $value['tmp'];
                    $str = str_replace('invoice_temp_files', 'invoice_files', $value['tmp']);
                    Storage::disk('public')->move($value['tmp'], $str);
                    $item->permanentPath = $str;
                    $supp_files[$key] = $item;
                }
            }
            return array('data' => $this->all(), 'user' => auth()->user(), 'invoice' => $item, 'supp_files' => $supp_files);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(), 403);
        }
    }
}
