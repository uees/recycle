<?php

namespace App\Api\V1;

use App\Models\Shipment;
use App\Models\EnteringWarehouse;
use Illuminate\Http\Request;


class UploadController extends Controller
{
    public function handle(Request $request)
    {
        // todo 上传excel流水表，保存数据到数据库
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                $realPath = $file->getRealPath();
                $entension = $file->getClientOriginalExtension(); //上传文件的后缀
                if ($entension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->load($realPath);
                    // todo fix： excel 文件太大这里会爆内存
                    $sheetData = $spreadsheet->getSheetByName('成品流水账')->ToArray();
                    dd($sheetData);
                    return;
                }
                $this->response->errorBadRequest('必须上传 .xlsx 文件');
                return;
            }
            $this->response->errorBadRequest('文件无效');
            return;
        }
        $this->response->errorBadRequest('文件字段必填');
        return;
    }
}
