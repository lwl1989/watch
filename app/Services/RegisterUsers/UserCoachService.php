<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/5/3
 * Time: 14:31
 */

namespace App\Services\RegisterUsers;


use App\Library\Constant\Common;
use App\Models\RegisterUsers\UserCoach;
use App\Models\RegisterUsers\UserCoachTags;
use App\Models\RegisterUsers\UserCounts;
use App\Services\ServiceBasic;

class UserCoachService extends ServiceBasic
{
    protected $model = UserCoach::class;

    public function getOne($id, bool $isUserId = true): array
    {
        $result = [
            'fans_count' => 0
        ];
        if ($isUserId) {
            $coach = UserCoach::query()->where('user_id', $id)->first();
        } else {
            $coach = UserCoach::query()->where('id', $id)->first();
        }

        if (empty($coach)) {
            return $result;
        }
        $uid = $coach->user_id;

        $coach = $coach->toArray();
        $tags = UserCoachTags::query()->where('coach_id', $coach['id'])->get()->toArray();
        $coach['tags'] = $tags;
        $fansCount = UserCounts::query()->firstOrCreate([
            'user_id' => $uid,
            'typ' => Common::USER_OP_BE_FOLLOW
        ], [
            'counts' => 1
        ]);
        $coach = array_merge($coach, $result);
        $coach['fans_count'] = $fansCount->counts;

        return $coach;
    }
}