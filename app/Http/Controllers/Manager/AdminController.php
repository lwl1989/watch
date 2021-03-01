<?php
namespace App\Http\Controllers\Manager;

use App\Exceptions\ErrorConstant;
use App\Http\Controllers\HomeController;
use App\Library\ArrayParse;
use App\Library\Constant\Common;
use App\Library\Constant\Message;
use App\Library\Constant\User;
use App\Library\Message\FcmMessage;
use App\Library\StrParse;
use App\Models\Admin;
use App\Models\CountryCode;
use App\Models\Department\Department;
use App\Models\Goods\Goods;
use App\Models\RegisterUsers\Users;
use App\Services\AdminService;
use App\Services\Department\DepartmentService;
use App\Services\RegisterUsers\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class AdminController extends HomeController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
    }

    /**
     * username
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|string
     */
    public function info()
    {
        $userId = Auth::id();
        $admin = new AdminService();
        $account = $admin->getOne($userId);
        return [
            'username' => StrParse::parseJsonDecode(session('user'))['account'] ?? 'admin',
            'adminId' => $userId,
            'role'=>isset($account['role'])?$account['role']:'0'
        ];
    }

    /**
     * check username
     * @param Request $request
     * @return array
     */
    public function checkUsername(Request $request)
    {
        $exists = ['has' => false,'has_code'=>false];
        $username = trim($request->input('u', ''));

        if(!empty($username)) {
            $admin = Admin::query()
                ->where('account', '=', $username)
                ->first(['account']);
            if (!is_null($admin)) {
                $exists['has'] = true;
            }
        }

        $code = trim($request->input('code', ''));
        if(!empty($code)) {
            $profile = Admin\AdminShopProfile::query()
                ->where('exchange_code', $code)
                ->first(['id']);
            if(!is_null($profile)) {
                $exists['has_code'] = true;
            }
        }

        return $exists;
    }

    /**
     * 返回權限數組
     * @return array
     */
    public function perm()
    {
        return [
            strval(Common::ADMIN_ROLE_EMPLOYEE)=>array_values(Common::PERMISSION_MAPPING),
            strval(Common::ADMIN_ROLE_MANAGER)=>array_values(Common::PERMISSION_MAPPING_ADMIN),
            strval(Common::ADMIN_ROLE_SHOP)=>array_values(Common::PERMISSION_MAPPING_SHOP)
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function select(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $conditions = $this->_buildRoleCondition($request);

        $filed = $request->input('field', false);
        if ($filed !== false) {
            $fields = explode(',', $filed);
            if (is_array($fields)) {
                AdminService::setSelfListField($fields);
            }
        }

        $admin = AdminService::limit($conditions, $limit, $page, false, -1, true, 2);

		if (count($admin['list']) > 0) {
			foreach ($admin['list'] as &$v) {
				if ($v['role'] == 3) {
					$v['department_name'] = '';
				}
			}
		}

        return [
            'admin' => $admin['list'],
            'count' => $admin['count']
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        try {
            $account = ArrayParse::checkParamsArray(['account','password', 'role', 'status', 'permissions'], $request->input());
            if ($account['role'] == Common::ADMIN_ROLE_SHOP) {
                $profile = ArrayParse::checkParamsArray([
                    'name', 'exchange_code', 'address', 'self_url', 'facebook_url', 'area', 'type',
                    'tel', 'tel_ext', 'mobile', 'email','lat','lng','cover', 'is_accept_gold', 'code'
                ], $request->input());

                if ($profile['type'] == Common::SHOP_TYPE_TEAM) {
                    if(empty($profile['mobile']) or empty(UsersService::userExistsByMobile('', ltrim($profile['mobile'],'0')))) {
                        return ['code'=> ErrorConstant::USER_MOBILE_NOT_EXITS];
                    }
                }

                //加上管理者id
                $profile['edit_id'] = Auth::id();
            } else {
            	if($this->checkAccountExist($account['account'])){
		            return ['code'=> -200];
	            }
                $profile = ArrayParse::checkParamsArray([
                    'name', 'alias', 'department_id', 'tel', 'tel_ext', 'code', 'mobile', 'email'],
                    $request->input()
                );

            	//code轉化
	            $turn_code = HomeController::turnCountryCode($profile['code']);
	            $profile['code'] = $turn_code['code'];

                if ($account['role'] != Common::ADMIN_ROLE_EMPLOYEE) {
                    $profile['department_id'] = 0;
                }

                $profile['mobile'] = ltrim($profile['mobile'], '0');
                if ($this->checkMobileExist($profile['mobile'], $profile['code'])) {
                    return ['code'=> -200]; //表示手機號已經被設置過
                }
            }
        } catch (\Exception $exception) {
            return ['code' => $exception->getCode(),'response'=>$exception->getMessage()];
        }

        $time = date('Y-m-d H:i:s', time());
        $account['permissions'] = json_encode($account['permissions']);
        $account['create_time'] = $account['update_time'] = $time;
        $admin = new AdminService();

        $account['password'] = bcrypt($account['password']);

        foreach ($account as $key => $value) {
            $admin->setAttr($key, $value);
        }
        DB::beginTransaction();
        try {
            $id = $admin->create();
            if ($account['role'] == Common::ADMIN_ROLE_SHOP) {
                $admin->createShopProfile($id, $profile);
            } else {
                $admin->createProfile($id, $profile);
            }
            DB::commit();

            /**! 發送通知消息 !**/
            if (stripos($account['permissions'], 'message_activity') !== false && $account['status'] == Common::STATUS_NORMAL) {
                $this->sendNoticeMsg(
                    $account,
                    $profile['code'],
                    $profile['mobile'],
                    Message::COMMON_EVENT_BIND_ADMIN_WITH_USER
                );
            }

            return ['code' => $id >= 0 ? 0 : 1, 'id' => $id, 'create_time' => $time, 'update_time' => $time];
        }catch (\Exception $exception){
            DB::rollBack();
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response'=>$exception->getMessage()];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id == 0) {
            return [];
        }

        /**! Fcm通知管理員刪除 !**/
        $admins = Admin::query()
            ->join('admin_profile', 'admin_profile.admin_id', '=', 'admin.id')
            ->whereIn('admin.id', $id)
            ->whereIn('admin.role', [Common::ADMIN_ROLE_EMPLOYEE, Common::ADMIN_ROLE_MANAGER])
            ->get(['admin_profile.code', 'admin_profile.mobile'])
            ->toArray();
        $codes = array_filter(array_unique(array_column($admins, 'code')));
        $mobiles = array_filter(array_column($admins, 'mobile'));
        $this->sendNoticeMsg(Common::ADMIN_ROLE_MANAGER, $codes, $mobiles, Message::COMMON_EVENT_UNBIND_ADMIN_WITH_USER);

        /**! 刪除商品 !**/
        $shopIds = Admin::query()
            ->join('admin_shop_profile', 'admin_shop_profile.admin_id', '=', 'admin.id')
            ->whereIn('admin.id', $id)
            ->where('admin.role', Common::ADMIN_ROLE_SHOP)
            ->get(['admin_shop_profile.id'])
            ->toArray();
        $shopIds = array_column($shopIds, 'id');
        if (count($shopIds) > 0) {
            /**! 删除Shop经纬度信息 !**/
            Redis::select(1);
            Redis::pipeline(function($pipe) use ($shopIds) {
                foreach ($shopIds as $shopId) {
                    $pipe->zrem('shop:location', $shopId);
                }
            });
            Redis::select(0);

            Goods::query()
                ->whereIn('shop_id', $shopIds)
                ->update(['deleted' => Common::DELETED]);
        }

        $admin = new AdminService();
        return ['row' => $admin->delete($id)];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request): array
    {
        $id = $request->input('id', 0);
        if ($id == 0) {
            return [];
        }

        try {
            $account = ArrayParse::arrayCopy(['account', 'role', 'status', 'permissions', 'status'], $request->input());

            if (!isset($account['role'])) {
//                $account = Admin::find($id)->toArray();
//                unset($account['id'], $account['create_time'], $account['update_time'], $account['password']);
            }
            if (isset($account['permissions'])) {
                $account['permissions'] = json_encode($account['permissions']);
            }

            $shopId = $request->post('shop_id', 0);
            if ($account['role'] == Common::ADMIN_ROLE_SHOP) {
                /**! 下架所有商品 !**/
                if ($account['status'] == Common::STATUS_DISABLE) {
                    Goods::query()
                        ->where('shop_id', $shopId)
                        ->where('status', Common::GOODS_ON_SALE_STATUS)
                        ->where('deleted', Common::NO_DELETE)
                        ->update([
                            'offline_time' => $date = date('Y-m-d H:i:s'),
                            'update_time' => $date,
                            'status' => Common::GOODS_OFF_SALE_STATUS,
                            'recommend' => Common::GOODS_RECOMMEND_OFF,
                            'recommend_icon' => Common::GOODS_RECOMMEND_OFF
                        ]);
                }

                $profile = ArrayParse::arrayCopy([
                    'name', 'exchange_code', 'address', 'self_url', 'facebook_url', 'area',
                    'tel', 'tel_ext', 'mobile', 'email','lat','lng','cover', 'is_accept_gold', 'code'
                ], $request->input());

                //加上管理者id
                $profile['edit_id'] = Auth::id();
            } else {
                $profile = ArrayParse::arrayCopy(['name', 'alias', 'department_id', 'tel', 'tel_ext', 'code', 'mobile', 'email',
                    'lat', 'lng', 'address'], $request->input());
                if ($account['role'] != Common::ADMIN_ROLE_EMPLOYEE) {
                    $profile['department_id'] = 0;
                }

	            $turn_code = HomeController::turnCountryCode($profile['code']);
	            $profile['code'] = $turn_code['code'];

                $profile['mobile'] = $updateMobile = ltrim($profile['mobile'], '0');
                $adminProfile = Admin\AdminProfile::query()
                    ->where('admin_id', $id)
                    ->first(['mobile','code'])
                    ->toArray();
                if ($hasChange = ($adminProfile['mobile'] != $updateMobile)) {
                    if ($this->checkMobileExist($updateMobile, $profile['code'])) {
                        return ['code' => -200]; //表示手機號已經被設置過
                    }
                }

                unset($updateMobile);
            }
        } catch (\Exception $exception) {
            return ['code' => $exception->getCode()];
        }

        $admin = new AdminService();
        unset($account['password']);
        $account['update_time'] = date('Y-m-d H:i:s');
        foreach ($account as $key => $value) {
            $admin->setAttr($key, $value);
        }

        if (!empty($profile)) {
            if ($account['role'] == Common::ADMIN_ROLE_SHOP) {
                $admin->updateShopProfile($id, $shopId, $profile);
            } else {
                $admin->updateProfile($id, $profile);

                /**! 有更新綁定手機號、並且有活動管理權限 !**/
                if ((isset($hasChange) && $hasChange === true) ||
                    (stripos($account['permissions'], 'message_activity') !== false && $account['status'] == Common::STATUS_NORMAL)
                ) {
                    $this->sendNoticeMsg(
                        $account,
                        $profile['code'],
                        ltrim($profile['mobile'], '0'),
                        Message::COMMON_EVENT_BIND_ADMIN_WITH_USER
                    );
                }

                /**! 有更新綁定手機號或者沒有了活動權限發送解綁 !**/
                if ((isset($hasChange) && $hasChange === true) ||
                    stripos($account['permissions'], 'message_activity') === false ||
                    $account['status'] != Common::STATUS_NORMAL
                ) {
                    if (isset($adminProfile)) {
                        $this->sendNoticeMsg(
                            Common::ADMIN_ROLE_MANAGER,
                            $adminProfile['code'],
                            ltrim($adminProfile['mobile'], '0'),
                            Message::COMMON_EVENT_UNBIND_ADMIN_WITH_USER
                        );
                    }
                }
            }
        }
        $admin->setModel(Admin::class);
        return ['row' => $admin->update($id)];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function get(Request $request): array
    {
        $id = $request->input('id', 0);
        if ($id == 0) {
            return [];
        }

        $admin = new AdminService();
        $account = $admin->getOne($id);
        if (!empty($account['permissions']) and !is_array($account['permissions'])) {
            $account['permissions'] = json_decode($account['permissions']);
        }
        if (empty($account)) {
            return [];
        }
        $profile = $admin->getProfile($account['id'], $account['role']);
        $country = CountryCode::query()
	        ->where('code', $profile['code'])
	        ->first(['id']);
        if (!empty($country)) {
	        $country = $country->toArray();
	        $profile['code'] = $country['id'];
        }
        unset($profile['id']);

        return ['data' => array_merge($account, $profile)];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function changeAdminPassword(Request $request): array
    {
        $adminId = $request->get('admin_id', 0);
        $oldPass = $request->get('oldPassword', '');

        $admin = Admin::query()
            ->where('id', '=', $adminId)
            ->first(['password']);
        if (is_null($admin) || !Hash::check($oldPass, $admin->getAttribute('password'))) {
            return ['code' => -1];
        }

        try {
            $params = ArrayParse::checkParamsArray(['admin_id', 'newPassword', 'confirmPassword'], $request->input());
        } catch (\Exception $exception) {
            return ['code' => $exception->getCode()];
        }

        if ($params['newPassword'] != $params['confirmPassword']) {
            return ['code' => ErrorConstant::PARAMS_ERROR];
        }

        if ($oldPass === $params['newPassword']) {
	        return ['code' => -2];
        }

        return ['code' => AdminService::changePassword($params, $params['admin_id']) ? 0 : ErrorConstant::SYSTEM_ERR];
    }
    /**
     * @param Request $request
     * @return array
     */
    public function changePassword(Request $request): array
    {
        try {
            $params = ArrayParse::checkParamsArray(['oldPassword', 'newPassword', 'confirmPassword'], $request->input());
        } catch (\Exception $exception) {
            return ['code' => $exception->getCode()];
        }

        if ($params['newPassword'] != $params['confirmPassword']) {
            return ['code' => ErrorConstant::PARAMS_ERROR];
        }

        return ['code' => AdminService::changePassword($params) ? 0 : ErrorConstant::SYSTEM_ERR];
    }

    /**
     * @param Request $request
     * @return array
     */
    private function _buildRoleCondition(Request $request): array
    {
        $conditions = [];
        $query = $request->query();
        $pass = ['limit', 'page'];

        foreach ($query as $key => $value) {
            if (in_array($key, $pass)) {
                continue;
            }

            switch ($key) {
                case 'profile' :
                    if (isset($query['profileValue'])) {
                        $conditions[$value] = $query['profileValue'];
                    }
                    continue 2;

                case 'type' :
                    if (isset($query['typeValue']) && $query['typeValue'] != 0) {
                        $conditions[$value] = $query['typeValue'];
                    }
                    continue 2;

                default :
                    $conditions[$key] = $value;
                    break;
            }
        }

        if (!isset($conditions['role'])) {
            $conditions['role'] = [Common::ADMIN_ROLE_EMPLOYEE, Common::ADMIN_ROLE_MANAGER];
        }

        if (Auth::user()->getAttribute('role') === Common::ADMIN_ROLE_EMPLOYEE) {
	        $units = DepartmentService::adminEmployeeBusinessUnit();
	        $units = array_column($units, 'id');

            $conditions['department_id'] = $units;
        }

        return $conditions;
    }

    /**
     * @param $mobile
     * @param $code
     * @return bool
     */
    private function checkMobileExist($mobile, $code)
    {
    	if (!empty($mobile)) {
		    $has = Admin\AdminProfile::query()
			    ->join('admin', 'admin_profile.admin_id', 'admin.id')
			    ->where('admin_profile.code', $code)
			    ->where('admin_profile.mobile', $mobile)
			    ->where('admin.deleted', Common::NO_DELETE)
			    ->first(['admin.id']);

		    if (!is_null($has)) {
			    return true;
		    }

		    return false;
	    }

        return false;
    }

	private function checkAccountExist($account)
	{
		$has = Admin::query()
			->where('account', $account)
			->first(['id']);
		if (!is_null($has)) {
			return true;
		}

		return false;
	}

    private function sendNoticeMsg($account, $code, $mobile, $msgType)
    {
        if (!is_array($code)) {
            $code = [$code];
        }

        if (!is_array($mobile)) {
            $mobile = [$mobile];
        }

        if ($account['role'] != Common::ADMIN_ROLE_SHOP) {
            $users = Users::query()
                ->whereIn('code', $code)
                ->whereIn('mobile', $mobile)
                ->get(['fcm_token', 'device_type'])
                ->toArray();
            if (count($users) > 0) {
                $platformTokens = [];

                foreach ($users as $user) {
                    if ($user['fcm_token'] == '') {
                        continue;
                    }

                    $platformTokens[$user['device_type']][] = $user['fcm_token'];
                }

                if (count($platformTokens) > 0) {
                    $tokens = [];

                    foreach ([User::USERS_DEVICE_TYPE_ANDROID, User::USERS_DEVICE_TYPE_IOS] as $platform) {
                        if (!isset($platformTokens[$platform])) {
                            continue;
                        }

                        $tokens[] = [
                            'platform' => $platform,
                            'token' => $platformTokens[$platform]
                        ];
                    }

                    FcmMessage::sendSystemMessage($msgType, $tokens);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     * @throws \Overtrue\EasySms\Exceptions\NoGatewayAvailableException
     */
//    public function changePass(Request $request) : array
//    {
//        $sms = new SmsManager(config('sms'));
//        return $sms->send('+8613352019331',[
//            'content'  => '[TTPush]您的驗證碼：'.$request->get('type'),
//            'template' => 'SMS_001',
//            'data' => [
//                'code' => 6379
//            ],
//        ]);
//    }
}