<?php


namespace App\Services;


use App\Library\Constant\Common;
use App\Models\Content\Content;
use App\Models\Content\ContentCounts;
use App\Models\RegisterUsers\UserInfo;

class ContentService
{

    public static function getContentListView($ids, $offset, $limit, $typ): array
    {
        $c = count($ids);

        if($c < $offset) {
            return [];
        }

     //   $ids = array_slice($ids, $offset, $limit);
        if($typ != 0) {
            $result = Content::query()
                ->whereIn('id', $ids)
                ->where('typ', $typ)
                ->where('status', Common::STATUS_NORMAL)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id','desc')
                ->get()->toArray();
        }else{
            $result = Content::query()
                ->whereIn('id', $ids)
                ->where('status', Common::STATUS_NORMAL)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id','desc')
                ->get()->toArray();
        }
        //        echo '<pre>';
        //        var_dump($result, $ids);
        $result = UserInfo::getUserInfoWithList($result);
        $result = ContentCounts::getContentsCounts($result);
        //        $userIds = array_column($result, 'user_id');
        //        $userInfos = UserInfo::query()->whereIn('user_id', $userIds)->get()->toArray();
        //        $userInfos = array_column($userInfos, null, 'user_id');
        //
        //        $mapResult = [];
        //        foreach ($result as $value) {
        //            if (isset($userInfos[$value['user_id']])) {
        //                $mapResult[] = [
        //                    'content' => $value,
        //                    'user' => $userInfos[$value['user_id']]
        //                ];
        //            }
        //        }
        return $result;
    }
}