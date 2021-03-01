<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/4/28
 * Time: 18:22
 */

namespace App\Http\Controllers\Common;
//
//
use App\Http\Controllers\Controller;
//use App\Library\Constant\Common;
//use App\Models\Common\Tags;
//use App\Models\RegisterUsers\UserSubTags;
//use Illuminate\Support\Facades\Auth;
//
class CateGoryController extends Controller{}
//{
//    public function getList(): array
//    {
//        $tags = Tags::query()->where('status', Common::STATUS_NORMAL)->orderBy('sort', 'desc')->get();
//
//        $myTags = UserSubTags::query()
//            ->where('user_id', Auth::id())
//            ->where('status', Common::STATUS_NORMAL)
//            ->orderBy('create_time', 'desc')->get();
//
//        if (is_array($tags) and is_array($myTags)) {
//            $myTagsT = array_column($myTags, 'tag_id');
//
//            foreach ($tags as $index => $tag) {
//                if (in_array($tag['id'], $myTagsT)) {
//                    unset($tags[$index]);
//                }
//            }
//
//            $tags = array_values($tags);
//        }
//
//        return ['tags' => $tags, 'my_tags' => $myTags];
//    }
//
//    public function getAll(): array
//    {
//        $tags = Tags::query()->where('status', Common::STATUS_NORMAL)->orderBy('sort', 'desc')->get();
//
//
//        return ['tags' => $tags];
//    }
//}