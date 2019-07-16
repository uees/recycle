<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get("/form", function () {
    return '<form method="post" enctype="multipart/form-data" action="/readfile">
    <div class="form-group">
        <label for="exampleInputFile">File Upload</label>
        <input type="file" name="file" class="form-control" id="exampleInputFile">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>';
});

$router->post('/readfile', function (\Illuminate\Http\Request $request) {
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        if ($file->isValid()) {
            $realPath = $file->getRealPath(); // 获取上传后的临时路径
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀
            if ($entension == 'xlsx') {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($realPath);
                $sheetData = $spreadsheet->getSheetByName('成品流水账')->ToArray();
                dd($sheetData);
                return 'success';
            }
            return '必须上传 .xlsx 文件';
        }
        return '文件无效';
    }
    return '文件字段必填';
});