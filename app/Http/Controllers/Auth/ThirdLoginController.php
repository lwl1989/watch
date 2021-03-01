<?php

namespace App\Http\Controllers\Auth;


use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Library\Auth\Encrypt;
use App\Library\Auth\RedisTokenCache;
use App\Library\Constant\Common;
use App\Library\Constant\User;
use App\Library\Message\FcmMessage;
use App\Library\Random;
use App\Library\Rsa\Generator;
use App\Library\Rsa\Provider;
use App\Library\Sms\Sms;
use App\Models\RegisterUsers\UserProfile;
use App\Models\RegisterUsers\Users;
use App\Models\RegisterUsers\UserThird;
use App\Services\RegisterUsers\UsersService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\RedisFacade as Redis;
use Ramsey\Uuid\Uuid;

class ThirdLoginController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function login(Request $request): array
    {
        $source = $request->post('source', User::USER_THIRD_FB);
        $sourceId = $request->post('source_id');
        $uuid = $request->post('device_uuid', Uuid::uuid4()->toString());
        $type = $request->post('device_type', User::MOBILE_DEVICE_ANDROID);

        if (!in_array($source, User::USER_THIRDS)) {
            return ['code' => ErrorConstant::PARAMS_LOST, 'response' => 'Not support this source'];
        }

        if (empty($sourceId)) {
            return ['code' => ErrorConstant::PARAMS_LOST, 'response' => 'token is null(source_id)'];
        }

        $priKey = Redis::get('login:third:'.$uuid);
        if (empty($priKey)) {
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => 'get key first'];
        }
        try {
            Redis::del('login:third:'.$uuid);
            $rsa = new Provider(['private_key' => $priKey]);
            $sourceId = $rsa->decodePublicEncode($sourceId);
            if($sourceId == '') {
                return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => 'decode error'];
            }
        } catch (\Exception $exception) {
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => 'decode error'];
        }

        $user = UserThird::query()->where('source', '=', $source)
            ->where('source_id', '=', $sourceId)
            ->join('users',function($query){
                $query->on('users.id','=','user_third.user_id')
                    ->where('users.deleted', '=', Common::NO_DELETE)
                    ->where('users.status', '=', Common::STATUS_NORMAL);
            })
            ->first(['user_third.user_id','users.*']);
        if (empty($user)) {
            return ['next_step' =>  'register'];
        }
        $user = $user->toArray();

        $result = Encrypt::getLoginResult(['uid' => $user['id'], 'device_uuid' => $uuid]);

        if (UsersService::checkClientHasLogin($user['id'], $user['device_uuid'])) {
            $result['actions'] = [
                //    '/user/kick'
            ];
            $info = [
                'old_uuid' => $user['device_uuid'],
                'old_type' => $user['device_type'],
                'fcm_token' => $user['fcm_token'], //舊設備FCM
                'kick_time' => time(),
                'uuid' => $uuid,
                'type' => $type
            ];
            //Redis::set('kick:' . $user['id'], json_encode($info));
            FcmMessage::sendTickMessage($user['fcm_token'], $info);
        } else {
            UsersService::loginLog($user['id'], $uuid, $request->getClientIp());
            $result['actions'] = [];
        }
        Users::query()->where('id', $user['id'])->update(['login_token'=>$result['token']]);
        return $result;
    }

    /**
     * 發送綁定驗證碼
     * @param Request $request
     * @return array
     */
    public function getPublicKey(Request $request): array
    {
        $deviceUuid = $request->post('device_uuid');

        $generator = new Generator();
        $key = $generator->getPublicKey();
        $priKey = $generator->getPrivateKey();
        if(empty($key) or empty($priKey)) {
            return ['code'=>ErrorConstant::SYSTEM_ERR, 'response'=>'get key error'];
        }
        Redis::setex('login:third:'.$deviceUuid, 3600, $priKey);

        return ['key' => $key ];
    }

    /**
     * 執行綁定操作 無論如何都是先登入後綁定
     * @param Request $request
     * @return array
     */
    public function bind(Request $request): array
    {
        $source = $request->post('source', User::USER_THIRD_FB);
        $sourceId = $request->post('source_id');
        $un = $request->post('is_cancel',false);
        if($un === false) {
            return ['code'=>ErrorConstant::PARAMS_LOST, 'response' => 'is_cancel lost'];
        }

        if($un == '0') {
            $avatar = $request->post('avatar', '');
            return $this->_bind($source, $sourceId, $avatar);
        }else{
            return $this->_unBind($source, $sourceId);
        }
    }



    /**
     * 取消執行綁定操作
     * @param $source
     * @param $sourceId
     * @return array
     */
    private function _unBind($source, string $sourceId): array
    {
        UserThird::query()->where('source','=',$source)
            ->where('source_id','=',$sourceId)
            ->delete();
        RedisTokenCache::clearAttr();
        return [];
    }

    /**
     * @param $source
     * @param string $sourceId
     * @param string $avatar
     * @return array
     */
    private function _bind($source,string $sourceId, string $avatar = ''): array
    {
        $uid = Auth::id();
        UserThird::query()->insertGetId([
            'user_id' => $uid,
            'source' => $source,
            'source_id' => $sourceId
        ]);
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = Auth::user();
        if (!empty($avatar) and $user->getAttributeValue('avatar') == '') {
            UserProfile::query()
                ->where('user_id', '=', Auth::$uid)
                ->update([
                    'avatar' => $avatar
                ]);
        }
        RedisTokenCache::clearAttr();
        return [];
    }
}