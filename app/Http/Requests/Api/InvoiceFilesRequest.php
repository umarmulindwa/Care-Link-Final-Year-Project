<?php

namespace App\Http\Requests\Api;

use App\Models\Config;
use Helpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class InvoiceFilesRequest extends FormRequest
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
            // true,
            // 'file'=>'required|max:5000|mimes:pdf'
        ];
    }

    public function saveFile($storeFolder)
    {
        try {
            //code...
            set_time_limit(0);
            $this->validate(
                [
                    'file' => 'required'
                ]
            );

            if ($this->hasFile('file')) {
                $fileName = isset($this->file_name) ? $this->file_name : "Uploaded_file";
                $fileDetail = storeFile("$storeFolder/" . $this->path, $this->file, $fileName);


                return apiResponse((object)['tmp' => $fileDetail->path, 'originalName' => $fileDetail->name]);
            } else {
                return apiErrorResponse(['message' => 'failed to save'], 403);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse($th->getMessage(), ['error' => $th->getTrace()]);
        }
    }
    public function removeFileFile()
    {
        try {
            $this->validate(
                [
                    'tmp' => 'required'
                ]
            );
            return apiResponse(deleteFile($this->tmp));
        } catch (\Throwable $th) {
            return apiErrorResponse($th->getMessage(), ['error' => $th->getTrace()]);
        }
    }


    public function saveEfris($storeFolder)
    {
        try {
            //code...
            $this->validate(
                [
                    'file' => 'required'
                ]
            );

            if ($this->hasFile('file')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($this->file);

                $text = $pdf->getText();
                $output = $this->validateEfris($text);

                if ($output['status'] == 'failed') {
                    return apiErrorResponse($output);
                }

                $fileDetail = storeFile("$storeFolder/" . $this->path, $this->file, 'SP-EFRIS_');


                return (object)['tmp' => $fileDetail->path, 'originalName' => $fileDetail->name, "fileContent" => $output];
            } else {
                return response()->json(['message' => 'failed to save'], 403);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th], 403);
        }
    }

    private function validateEfris($text)
    {
        try {
            // look for UNICEF TIN in the pdf file
            $config = Config::first();
            $unicefTinNumber  = $config->unicef_tin_number;



            $isEFRISValid = true;

            if (strpos($text, $unicefTinNumber) == false) {
                $isEFRISValid = false;
                return array("message" => "EFRIS INVOICE IS INVALID", "status" => "failed", "content" => compact('isEFRISValid', 'unicefTinNumber'));
            }



            $pdfText = explode("<br />", nl2br($text));
            $efrisInx = null;
            for ($i = 0; $i < count($pdfText); $i++) {
                $str =  strpos($pdfText[$i], 'Fiscal Document Number:');
                if ($str == false) {
                    continue;
                } else {
                    $efrisInx = $i;

                    break;
                }
            }
            // "\nNet Amount:\t2,610,169.49\t",

            $netAmount = 0;
            for ($i = 0; $i < count($pdfText); $i++) {
                $str =  strpos($pdfText[$i], 'Net Amount:');
                if ($str == false) {
                    continue;
                } else {
                    $netAmount = (float) preg_replace('/[^0-9.]/', "", $pdfText[$i]);

                    break;
                }
            }
            $grossAmounts = 0;
            for ($i = 0; $i < count($pdfText); $i++) {
                $str =  strpos($pdfText[$i], 'Gross Amount:');
                if ($str == false) {
                    continue;
                } else {
                    $grossAmounts = (float) preg_replace('/[^0-9.]/', "", $pdfText[$i]);
                    // $grossAmounts = number_format($grossAmounts,2);
                    break;
                }
            }

            $vatRealAmount = null;
            for ($i = 0; $i < count($pdfText); $i++) {
                $str =  strpos($pdfText[$i], 'VAT-Standard');
                if ($str == false) {
                    continue;
                } else {
                    $r = explode("\t", $pdfText[$i]);
                    $vatRealAmount = (float) preg_replace('/[^0-9.]/', "", $r[(count($r) - 3)]);


                    break;
                }
            }

            $exciseDutyAmount = null;
            for ($i = 0; $i < count($pdfText); $i++) {
                $str =  strpos($pdfText[$i], 'Excise Duty');
                if ($str == false) {
                    continue;
                } else {
                    $r = explode("\t", $pdfText[$i]);
                    $exciseDutyAmount = (float) preg_replace('/[^0-9.]/', "", $r[(count($r) - 3)]);


                    break;
                }
            }



            $buyerTin =  $pdfText[17];
            $buyerName =  $pdfText[18];
            $buyerTin =  $pdfText[17];
            $sellerTin =  $pdfText[3];
            $sellerName =  $pdfText[4];
            // $efrisNumber = $pdfText[14];
            // $vatAmount = $vatAmountInx == null ? 000 : $pdfText[$vatAmountInx];
            $efrisNumber = $efrisInx == null ? 000 : $pdfText[$efrisInx];

            $buyerTin = str_replace(array("\n", "\t", "Name:", "TIN:"), "", $buyerTin);
            $buyerName = str_replace(array("\n", "\t", "Name:", "TIN:"), "", $buyerName);
            $sellerTin = str_replace(array("\n", "\t", "Name:", "TIN:"), "", $sellerTin);
            $sellerName = str_replace(array("\n", "\t", "Legal Name:", "TIN:"), "", $sellerName);
            $efrisNumber = str_replace(array("\n", "\t", "Fiscal Document Number:", "TIN:"), "", $efrisNumber);
            // $vatAmount = str_replace(array("\n", "\t", "Tax Amount:", "TIN:", ','), "", $vatAmount);
            // $vatAmount = number_format($vatAmount,3);
            $vatAmount = $vatRealAmount;

            return array("message" => "Successful", "content" => compact('grossAmounts', 'vatRealAmount', 'exciseDutyAmount', 'netAmount', 'isEFRISValid', 'sellerTin', 'sellerName', 'buyerTin', 'buyerName', 'efrisNumber', 'pdfText', 'vatAmount'), "status" => "OK");
        } catch (\Throwable $th) {
            //throw $th;
            return array("message" => "Failed: " . $th->getMessage(), "status" => "failed");
        }
    }
}
