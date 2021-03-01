<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/4/28
 * Time: 18:22
 */

namespace App\Http\Controllers\Common;


use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Library\Constant\Common;
use App\Models\Common\Tags;
use App\Models\RegisterUsers\UserSubTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagsController extends Controller
{
    /**
     * @api               {get} /api/user/tags 选择标签页面
     * @apiGroup          用户操作
     * @apiName           获取tag列表
     *
     * @apiParam {String} code
     * @apiParam {String} encryptedData
     * @apiParam {String} iv
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "talk": [ {
     * "id": "1",
     * "name": "真人秀",
     * "sort": "1",
     * "status": "1",
     * "create_time": "1588069984",
     * "update_time": "1588069984"
     * }],
     * "write": [ {
     *  "id": "1",
     *  "name": "写",
     *  "sort": "1",
     *  "status": "1",
     *  "create_time": "1588069984",
     *  "update_time": "1588069984"
     * }],
     *      "my_tags": [{
     *                  //...
     *              }],
     *   }
     */
    public function getList(): array
    {
        $tags = Tags::query()->where('status', Common::STATUS_NORMAL)->orderBy('sort', 'desc')->get();

        $myTags = UserSubTags::query()
            ->where('user_id', Auth::id())
            ->where('status', Common::STATUS_NORMAL)
            ->orderBy('create_time', 'desc')->get();

        if (is_array($tags) and is_array($myTags)) {
            $myTagsT = array_column($myTags, 'tag_id');

            foreach ($tags as $index => $tag) {
                if (in_array($tag['id'], $myTagsT)) {
                    unset($tags[$index]);
                }
            }


            $tags = array_values($tags);
        }

        $talk = [];
        $write = [];
        foreach ($tags as $tag) {
            if ($tag['typ'] == 1) {
                $talk[] = $tag;
            } else {
                $write[] = $tag;
            }
        }

        return ['talk' => $talk, 'write' => $write, 'my_tags' => $myTags];
    }

    /**
     * @api               {get} /api/tags 获取所有频道标签
     * @apiGroup          内容获取
     * @apiName           获取所有频道标签
     *
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "talk": [ {
     * "id": "1",
     * "name": "真人秀",
     * "sort": "1",
     * "status": "1",
     * "create_time": "1588069984",
     * "update_time": "1588069984"
     * }],
     * "write": [ {
     *  "id": "1",
     *  "name": "写",
     *  "sort": "1",
     *  "status": "1",
     *  "create_time": "1588069984",
     *  "update_time": "1588069984"
     * }],
     *   }
     */
    public function getAll(): array
    {
        $tags = Tags::query()->where('status', Common::STATUS_NORMAL)->orderBy('sort', 'desc')->get();

        $talk = [];
        $write = [];
        foreach ($tags as $tag) {
            if ($tag['typ'] == 1) {
                $talk[] = $tag;
            } else {
                $write[] = $tag;
            }
        }

        return ['talk' => $talk, 'write' => $write];
    }

    /**
     * @api               {get} /api/user/menu 用户菜单（导航）
     * @apiGroup          用户操作
     * @apiName           获取用户菜单
     *
     * @apiParam {String} code
     * @apiParam {String} encryptedData
     * @apiParam {String} iv
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "tags": [ {
     * "id": "1",
     * "name": "真人秀",
     * "sort": "1",
     * "status": "1",
     * "create_time": "1588069984",
     * "update_time": "1588069984"
     * }],
     *   }
     */
    public function getMenu(): array
    {
        $subTags = UserSubTags::query()
            ->where('user_id', Auth::id())
            ->where('status', Common::STATUS_NORMAL)
            ->orderBy('create_time', 'desc')->get()->toArray();
        if (!empty($subTags)) {
            $myTags = Tags::query()->where('status', Common::STATUS_NORMAL)
                ->orderBy('sort', 'desc')
                ->limit(10)->get();
        } else {
            $myTags = Tags::query()->where('status', Common::STATUS_NORMAL)
                ->whereIn('id', array_column($subTags, 'tag_id'))
                ->orderBy('sort', 'desc')
                ->limit(10)->get();
        }

        array_unshift($myTags, [
            'name' => '推荐',
            'id' => -2,
            'sort' => 9999998,
            'status' => Common::STATUS_NORMAL,
            'create_time' => '0000-00-00 00:00:00',
            'update_time' => '0000-00-00 00:00:00'
        ]);
        array_unshift($myTags, [
            'name' => '关注',
            'id' => -1,
            'sort' => 9999999,
            'status' => Common::STATUS_NORMAL,
            'create_time' => '0000-00-00 00:00:00',
            'update_time' => '0000-00-00 00:00:00'
        ]);
        return $myTags;
    }

    /**
     * @api               {post} /api/user/tag/sub  用户选择标签
     * @apiGroup          用户操作
     * @apiName           用户选择标签
     *
     * @apiParam {Array} tag_ids
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     */
    public function subTag(Request $request)
    {
        $tagIds = $request->input('tag_ids', []);
        if (empty($tagIds) || !is_array($tagIds)) {
            return ['code' => ErrorConstant::PARAMS_ERROR, 'ids is null'];
        }

        $uid = Auth::id();
        $existsId = UserSubTags::query()->where('user_id', $uid)->whereIn('tag_id', $tagIds)->get(['tag_id'])->toArray();
        $notExistsId = UserSubTags::query()->where('user_id', $uid)->whereNotIn('tag_id', $tagIds)->get(['tag_id'])->toArray();

        if (!empty($notExistsId)) {
            UserSubTags::query()->where('user_id', $uid)->whereIn('tag_id', array_column($notExistsId, 'tag_id'))->delete();
        }

        if (!empty($existsId)) {
            $insertIds = array_diff($tagIds, array_column($existsId, 'tag_id'));
            foreach ($insertIds as $tagId) {
                UserSubTags::query()->insert([
                    'user_id' => $uid,
                    'tag_id' => $tagId,
                    'sub_time' => date('Y-m-d H:i:s')
                ]);
            }
        }
        return [];
    }
}