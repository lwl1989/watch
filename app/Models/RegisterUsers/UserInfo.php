<?php

namespace App\Models\RegisterUsers;

use App\Library\Constant\Common;
use App\Models\Model;


/**
 * Class UserInfo
 * @package App\Models\RegisterUsers
 * @author  author  李文龙 <liwenlong@inke.cn>
 */
class UserInfo extends Model
{
    public $table = 'user_info';


    public $timestamps = false;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';


    public static function getUserInfoWithList(array $list, string $userIdKey = 'user_id'): array
    {
        if (empty($list)) {
            return $list;
        }
        if (!isset($list[0][$userIdKey])) {
            return $list;
        }
        $userIds = array_column($list, $userIdKey);
        $users = UserInfo::query()->whereIn('user_id', $userIds)->get()->toArray();
        $coaches = UserCoach::query()->whereIn('user_id', $userIds)->where('status', Common::STATUS_NORMAL)->get(['id'])->toArray();
        $users = array_column($users, null, 'user_id');
        $coaches = array_column($coaches, null, 'user_id');
        foreach ($list as &$item) {
            $item['user'] = $users[$item['user_id']];
            $item['user']['is_coach'] = isset($coaches[$item['user_id']]) ? "1" : "0";
            unset($item);
        }
        return $list;
    }

    public static function getUserInfoWithId($userId): array
    {
        $users = UserInfo::query()->where('user_id', $userId)->first();
        if (!$users) {
            return [];
        }
        $user = $users->toArray();

        $userCoach = UserCoach::query()->where('user_id', $userId)
            ->where('status', Common::STATUS_NORMAL)
            ->first();

        $user['is_coach'] = 0;
        $user['coach'] = new \stdClass();
        if (!$userCoach) {
            $user['is_coach'] = 1;
            $user['coach'] = $userCoach->toArray();
        }

        return $user;
    }
}