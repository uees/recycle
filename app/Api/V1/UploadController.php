<?php

namespace App\Api\V1;

use App\Models\Shipment;
use App\Models\EnteringWarehouse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class UploadController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->hasFile('xlsx')) {
            $file = $request->file('xlsx');
            if ($file->isValid()) {
                $realPath = $file->getRealPath();
                $entension = $file->getClientOriginalExtension(); //上传文件的后缀
                if ($entension == 'xlsx') {
                    $reader = new Xlsx();
                    $spreadsheet = $reader->load($realPath);
                    $sheetData = $spreadsheet->getActiveSheet()->ToArray();
                    dd($sheetData);
                    return;
                }
            }
        }
    }
}
