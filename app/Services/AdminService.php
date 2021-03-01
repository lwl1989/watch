<?php


namespace App\Services;


use App\Library\ArrayParse;
use App\Library\Constant\Common;
use App\Library\Constant\StaticRoute;
use App\Library\Random;
use App\Models\Admin;
use App\Models\Goods\Goods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Library\RedisFacade as Redis;


class AdminService extends ServiceBasic
{
    protected $model = Admin::class;
    protected $listField = ['admin.id', 'admin.account', 'admin.role', 'admin.status', 'admin.create_time'];

    protected static $adminProfileField = [
        'admin_profile.id as profile_id',
        'admin_profile.name',
        'admin_profile.alias',
        'admin_profile.department_id',
        'admin_profile.tel',
        'admin_profile.tel_ext',
        'admin_profile.mobile',
        'admin_profile.code',
        'admin_profile.email'
    ];

    /**
     *
     * @param $role
     * @param $perm
     *
     * @return array
     */
    public static function getUserAcl($role, $perm): array
    {
        $permissions = [];

        if ($perm == '') {
            return $permissions;
        }

        if (!is_array($perm)) {
            $permissions = json_decode($perm, true);
            if (!$permissions) {
                $permissions = [];
            }
        }

        return StaticRoute::getPathFromName($role, $permissions);
    }

    public static function limit(array $conditions, int $limit = 15, int $page = 1, bool $deleted = false, int $status = -1, bool $exitsDelete = true, $version = 1): array
    {
        $query = self::_getQuery([], $deleted, $status, $exitsDelete);

        foreach ($conditions as $key => $value) {
            switch ($key) {
                case 'status':
                    $query->where('admin.' . $key, '=', $value);
                    break;

                case 'account':
                    $query->where('admin.' . $key, 'like', "%$value%");
                    break;

                case 'distance':
                    $ids = self::getNearShopIds($value['lat'], $value['lng'], $value['radius'] ?? 5000);
                    if (!empty($ids)) {
                        $query->join('admin_shop_profile', function ($query) use ($value) {
                            if (!empty($ids)) {
                                $query->whereIn('admin_shop_profile.id', $ids);
                            }
                        });
                    }
                    break;

                case 'role':
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    $query->whereIn('admin.' . $key, $value);
                    break;

                case 'address':
                    $query->where('admin_shop_profile.' . $key, 'like', "%$value%");
                    break;

                case 'shop_name':
                    $query->where('admin_shop_profile.name', 'like', "%$value%");
                    break;

                case 'shop_tel':
                    $query->where('admin_shop_profile.tel', 'like', "%$value%");
                    break;

                case 'type':
                    $query->where('admin_shop_profile.type', '=', $value);
                    break;

                case 'admin_name':
                    $query->where('admin_profile.name', 'like', "%$value%");
                    break;

                case 'admin_mobile':
                    $query->where('admin_profile.mobile', 'like', "%$value%");
                    break;

                case 'admin_email':
                    $query->where('admin_profile.email', 'like', "%$value%");
                    break;

                case 'department_id' :
                    if (!is_array($value)) {
                        $value = [$value];
                    }

                    $query->whereIn('department.id', $value);
                    break;

                case 'department':
                    $query->where('department.name', 'like', "%$value%");
                    break;
            }
        }


        $field = array_merge(['admin.*'], self::$adminProfileField, ['department.name as department_name']);

        $query
            ->join('admin_profile', 'admin.id', '=', 'admin_profile.admin_id', $conditions['isSearch'] ?? 0 == 0 ? 'left' : 'inner')
            ->leftJoin('department', function ($query) {
                $query->on('admin_profile.department_id', 'department.id');
            });

        $count = $query->count('admin.id');
        $list = $query
            ->skip(($page - 1) * $limit)
            ->orderByDesc('admin.create_time')
            ->take($limit)
            ->get($field)
            ->toArray();

        if ($version == 2) {
            return [
                'list' => $list,
                'count' => $count
            ];
        }

        return $list;
    }

    public static function changePassword(array $passwords, $adminId = 0): bool
    {
        if ($adminId == 0) {
            $adminId = Auth::id();
            $password = Admin::query()->where('id', $adminId)->first(['password']);
            if (Hash::check($passwords['oldPassword'], $password->getAttribute('password'))) {
                self::getModelInstance()::where('id', $adminId)->update(['password' => bcrypt($passwords['newPassword'])]);
                return true;
            }
        } else {
            self::getModelInstance()::where('id', $adminId)->update(['password' => bcrypt($passwords['newPassword'])]);
            return true;
        }

        return false;
    }

    public function create(): int
    {
        $account = $this->attr['account'];
        $exists = Admin::query()->where('account', $account)->first(['id', 'account']);
        if (!empty($exists)) {
            Admin::query()
                ->where('id', $exists->getAttribute('id'))
                ->update([
                    'account' => $account . '_' . Random::randomString(6)
                ]);
        }
        return parent::create();
    }

    public function createProfile(int $id, array $profile): int
    {
        self::setModel(Admin\AdminProfile::class);
        return $this->_createProfile($id, $profile);
    }

    public function createShopProfile(int $id, array $profile): int
    {
        self::setModel(Admin\AdminShopProfile::class);
        $id = $this->_createProfile($id, $profile);
        $this->setToRedisLocation($id, $profile);
        return $id;
    }

    private function _createProfile(int $id, array $profile): int
    {
        $model = self::getModelInstance();
        $model->admin_id = $id;
        foreach ($profile as $key => $value) {
            $model->$key = $value;
        }
        $saved = $model->save();

        if ($saved) {
            return intval($model->id);
        } else {
            return -1;
        }
    }

    public function updateProfile(int $id, array $profile): int
    {
        self::setModel(Admin\AdminProfile::class);
        return $this->_updateProfile($id, $profile);
    }

    public function updateShopProfile(int $id, $shopId, array $profile): int
    {
        self::setModel(Admin\AdminShopProfile::class);
        $this->setToRedisLocation($shopId, $profile);
        return $this->_updateProfile($id, $profile);
    }

    private function _updateProfile(int $id, array $profile): int
    {
        $model = self::getModelInstance();
        return $model->newQuery()->where('admin_id', $id)->update($profile);
    }

    public function getProfile(int $id, int $role): array
    {
        self::setModel(Admin\AdminProfile::class);
        $field = self::$adminProfileField;

        $model = self::getModelInstance();
        $profile = $model->newQuery()->where('admin_id', $id)->first($field)->toArray();
        if (isset($profile['edit_id'])) {
            $account = Admin::query()->find($profile['edit_id'], ['account']);
            $accountName = '';
            if (!empty($account)) {
                $accountName = $account->getAttribute('account');
            }

            $profile['updater_account'] = $accountName;
        }

        return ArrayParse::coverGet($profile);
    }


}