<?php

namespace App\Http\Controllers\Resource;


use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Services\OssService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        return $this->cover($request);
    }

    /**
     *  '/static/user/avatar', '/static/content/cover', '/static/content/images', '/static/content/videos'
     * @api               {post} /api/static/user/avatar 上传
     * @apiGroup          用户操作
     * @apiName           上传文件
     *
     * @apiDescription   同样的路由还有 '/static/user/avatar', '/static/content/cover', '/static/content/images', '/static/content/videos' 都是用于文件上传
     * @apiParam {File} file
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "name": "u=53064636,2739699047&fm=74&app=80&f=PNG&size=f121,140.jpeg",
     *      "path": "static/user/avatar/9/9/b/5ea82ae60eeeb.jpeg",
     *      "url": "http://liuliu-static.oss-cn-beijing.aliyuncs.com/static/user/avatar/9/9/b/5ea82ae60eeeb.jpeg",
     *     "type": "image/jpeg"
     *   }
     */
    public function cover(Request $request)
    {
        $path = $request->getRequestUri();

        if (strpos($path, 'api/') === 1) {
            $path = substr($path, 4);
        }

        $allow = [
            '/static/user/avatar', '/static/content/cover', '/static/content/images', '/static/content/videos'
        ];

        if (!in_array($path, $allow)) {
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => 'path it\'s not allow'];
        }
        // 自動計算文件的md5
        // Storage::putFile('photos', new File('/path/to/photo'));

        // 手動指定文件名...
        // Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');
        //通過path獲取路徑

        $file = $request->file('file');

        $md5 = md5(time() . $file->getClientOriginalName());
        $path = sprintf('%s/%s/%s/%s', $path, $md5[0], $md5[1], $md5[2]);

        //if ($file->isValid()) {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 擴展名
        $realPath = $file->getRealPath();   //臨時文件的絕對路徑
        $type = $file->getClientMimeType();     // image/jpeg
        $path = substr($path, 1) . '/' . uniqid() . '.' . $ext;
        OssService::publicUpload('liuliu-static', $path, $realPath);


        //        if ($type !== 'image/png' || $type !== 'image/jpg' || $type !== 'image/jpeg') {
        //            Storage::disk('public')->put($path, file_get_contents($realPath));
        //        } else {//application/vnd.ms-excel
        //            //手動指定驅動爲public
        //            $path = Storage::disk('public')->put($path, $file);
        //        }

        $sfsHost = env('SFS_URL', null);
        $url = OssService::getPublicObjectURL('liuliu-static', $path);
        //        if (empty($sfsHost)) {
        //            $url = $request->getSchemeAndHttpHost() . Storage::url($path);
        //        } else {
        //            $url = rtrim($sfsHost, '/') . Storage::url($path);
        //        }
        //}

        return ['name' => $originalName, 'path' => $path, 'url' => $url, 'type' => $type];

    }
}