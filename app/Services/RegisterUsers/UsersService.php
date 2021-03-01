<?php

namespace App\Services\RegisterUsers;
use App\Models\RegisterUsers\UserOpLog;
use App\Services\ServiceBasic;
use App\Models\RegisterUsers\Users;
use Illuminate\Database\Eloquent\Model;

class UsersService extends ServiceBasic
{
    protected $model = Users::class;

    /**
     * 記錄日誌
     * @param $userId
     * @param string $uuid
     * @param string $ip
     * @param string $mobile
     */
    public static function loginLog($userId, string $uuid, string $ip, string $mobile = '')
    {
        $userLog = new Class extends Model
        {
            protected $table = 'user_login_log';
            const CREATED_AT = 'create_time';
            const UPDATED_AT = 'update_time';
        };

        $userLog = new $userLog();
        $userLog->user_id = $userId;
        $userLog->mobile = $mobile;
        $userLog->uuid = $uuid;
        $userLog->ip = $ip;
        $userLog->save();
    }

    /**
     * 行為日誌記錄
     * @param $userId
     * @param $opType
     * @param string $opMsg
     * @param int $adminId
     */
    public static function actionLog($userId, $opType, $opMsg = '', $adminId = 0)
    {
        UserOpLog::query()
            ->insert([
                'op_id' => $userId,
                'ad_id' => $adminId,
                'op_type' => $opType,
                'op_ins' => $opMsg,
                'create_time' => date('Y-m-d H:i:s')
            ]);
    }


    public static function limit(array $conditions, int $limit = 15, int $page = 1, bool $deleted = false, int $status = 1, bool $exitsDelete = true): array
    {
        $query = self::_getQuery($conditions, $deleted, $status, $exitsDelete);

        $field = array_merge(['users.*'], ['user_profile.*']);

        $list = $query->skip(($page - 1) * $limit)
            ->join('user_profile', function ($query) {
                $query->on('users.id', '=', 'user_profile.user_id');
            })
            ->take($limit)
            ->orderByDesc('users.id')
            ->get($field)
            ->toArray();

        return $list;
    }

    public function getOne($id): array
    {
        $user = Users::query()
            ->where('id',$id)
            ->first();

        $profile = UserProfile::query()->where('user_id', $id)->first();

        if (empty($user) || empty($profile)) {
            return [];
        }
        $obj = array_merge($user->toArray(), $profile->toArray());
        $obj['id'] = $id;

        return $obj;
    }

    public static function count(array $conditions, bool $deleted = false, int $status = 1,bool $exitsDelete=true): int
    {
        $query = self::_getQuery($conditions, $deleted, $status);

        $count = $query->join('user_profile', function ($query) {
            $query->on('users.id', '=', 'user_profile.user_id');
        })->count();

        return intval($count);
    }

    public function update($id): int
    {
        $model = self::getModelInstance()->newQuery();

		return $model->newQuery()
            ->join('user_profile', function ($model) {
                $model->on('users.id', '=', 'user_profile.user_id');
            })
            ->where('users.id', $id)
            ->update($this->attr);
    }

}