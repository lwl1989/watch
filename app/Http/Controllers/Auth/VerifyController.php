<?php
namespace App\Http\Controllers\Auth;

use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Services\VerifyCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Ramsey\Uuid\Uuid;

class VerifyController extends Controller
{
    /**
     * 獲取圖形驗證碼
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function get(Request $request) : array
    {
        if (strpos($request->path(), 'api/') !== false) {
            $deviceUUid = $request->post('device_uuid', Uuid::uuid4()->toString());
        } else {
            $deviceUUid = Cookie::get('device_uuid');
            if (empty($deviceUUid)) {
                $deviceUUid = Uuid::uuid4()->toString();
                Cookie::queue('device_uuid', $deviceUUid);
            }
        }

        $base64 = VerifyCodeService::generate($deviceUUid);

        return ['data'=>[
            'device_uuid'   =>  $deviceUUid,
            'content'       =>  $base64
        ]];
    }

    /**
     * 驗證圖形驗證碼
     * @param Request $request
     * @return array
     */
    public function verify(Request $request) : array
    {
        $uuid = $request->post('device_uuid', Cookie::get('device_uuid'));
        if(!VerifyCodeService::verify($uuid, $request->post('verify'))) {
            return ['code'  =>  ErrorConstant::DATA_ERR, 'response' =>  'verify error'];
        }

        return [];
    }

}