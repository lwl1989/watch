<?php

namespace App\Http\Controllers\Users;


use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Library\Constant\Common;
use App\Library\Random;
use App\Models\Common\Tags;
use App\Models\Content\Content;
use App\Models\Content\ContentComment;
use App\Models\Content\ContentCounts;
use App\Models\Content\Resources;
use App\Models\Content\Scene;
use App\Models\Content\SceneReply;
use App\Models\Content\Topics;
use App\Models\Question\QuestionAppoint;
use App\Models\Question\QuestionReply;
use App\Models\Question\Questions;
use App\Models\RegisterUsers\UserBind;
use App\Models\RegisterUsers\UserCoach;
use App\Models\RegisterUsers\UserCoachTags;
use App\Models\RegisterUsers\UserCounts;
use App\Models\RegisterUsers\UserFavorites;
use App\Models\RegisterUsers\UserInfo;
use App\Models\RegisterUsers\UserNotice;
use App\Models\RegisterUsers\UserOpLog;
use App\Models\RegisterUsers\UserRelations;
use App\Models\RegisterUsers\Users;
use App\Models\RegisterUsers\UserSubTags;
use App\Models\RegisterUsers\UserTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * @api               {get} /api/user/center 个人中心上半部
     * @apiGroup          用户中心
     * @apiName           个人中心上半部
     *
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *              "user":{
     *                  "id": "1",
     *                  "nickname": "休息休息",
     *                  "avatar": "https://xxxxxx/",
     *              },
     *              "tags"  :   [
     *                          {"id":"1","name":"教练标签"},//...
     *             ],
     *              "counts": {
     *                  "8":"12",
     *                  "9":"90"
     *              },
     *             "is_coach":"1" // 1认证了 2 未认证
     *    }
     *
     */
    /**
     * 个人中心
     * @param Request $request
     *
     * @return array
     */
    public function center(Request $request): array
    {
        $uid = $request->get('uid', false);
        if(!$uid) {
            $uid = Auth::id();
        }

        $info = UserInfo::query()->where('user_id', $uid)->first();
        if(!$info) {
            return ['code' => ErrorConstant::DATA_ERR, 'response' => '此账户不存在'];
        }
        $info = $info->toArray();

        $tags = UserSubTags::query()->where('user_id', $uid)->get()->toArray();

        $fansCount = UserRelations::query()->where('re_user_id', $uid)->where('status', Common::STATUS_NORMAL)->count();


        $followCount = UserRelations::query()->where('user_id', $uid)->where('status', Common::STATUS_DISABLE)->count();

        $isCoach = 0;
        $coach = UserCoach::query()->where('user_id', $uid)->where('status', Common::STATUS_NORMAL)->first();
        if ($coach) {
            $isCoach = 1;
            $coach = UserCoach::query()->where('user_id', $uid)->first();
            if($coach) {
                $info = array_merge($info, $coach->toArray());
            }
        }
        return [
            'user' => $info,
            'tags' => $tags,
            'counts' => [
                Common::USER_OP_BE_FOLLOW => $fansCount,
                Common::USER_OP_FOLLOW => $followCount
            ],
            'is_coach' => $isCoach
        ];
    }
    /**
     * @api               {get} /api/user/follows/{uid} 我关注的教练
     * @apiGroup          用户中心
     * @apiName           我关注的教练
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * [
     *     {
     *                  "user_id": "1",
     *                  "nickname": "休息休息",
     *                  "avatar": "https://xxxxxx/",
     *    },//...
     * ]
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function follows(Request $request): array
    {
        $uid = $request->route('uid');

        $relations = UserRelations::query()
            ->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->where('typ', 1)
            ->get()
            ->toArray();

        if (empty($relations)) {
            return ['coaches' => []];
        }

        $userIds = array_column($relations, 're_user_id');
        $coaches = Users::query()->select(['username', 'id', 'typ'])->whereIn('id', $userIds)->get()->toArray();
        if(!empty($coaches)) {
            $isCoaches = UserCoach::query()->whereIn('user_id', $userIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
            $isCoaches = array_column($isCoaches, null, 'user_id');
            $infos = UserInfo::query()->whereIn('user_id', $userIds)->get()->toArray();
            $infos = array_column($infos, null, 'user_id');
            foreach ($coaches as &$coach) {
                $coach['is_coach'] = 0;
                if(isset($isCoaches[$coach['id']])) {
                    $coach['is_coach'] = 1;
                    $coach  = array_merge($isCoaches[$coach['id']], $coach);
                }
                $coach['followed'] = 1;
                if(isset($infos[$coach['id']])) {
                    $coach  = array_merge($infos[$coach['id']], $coach);
                }


                unset($coach);
            }
        }

        return ['coaches' => $coaches];
    }
    /**
     * @api               {get} /api/user/follofansws/{uid} 我的粉丝列表
     * @apiGroup          用户中心
     * @apiName           我的粉丝列表
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * [
     *     {
     *                  "user_id": "1",
     *                  "nickname": "休息休息",
     *                  "avatar": "https://xxxxxx/",
     *    },//...
     * ]
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function fans(Request $request): array
    {
        $uid = $request->route('uid');

        $relations = UserRelations::query()
            ->where('re_user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->where('typ', 1)
            ->get()
            ->toArray();

        if (empty($relations)) {
            return ['coaches' => []];
        }

        $userIds = array_column($relations, 'user_id');
        $relations = UserRelations::query()
            ->where('user_id', $uid)
            ->whereIn('re_user_id', $userIds)
            ->where('status', Common::STATUS_NORMAL)
            ->where('typ', 1)
            ->get()
            ->toArray();
        $coaches = Users::query()->select(['username', 'id', 'typ'])->whereIn('id', $userIds)->get()->toArray();
        if(!empty($coaches)) {
            $isCoaches = UserCoach::query()->whereIn('user_id', $userIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
            $isCoaches = array_column($isCoaches, null, 'user_id');
            $infos = UserInfo::query()->whereIn('user_id', $userIds)->get()->toArray();
            $infos = array_column($infos, null, 'user_id');
            foreach ($coaches as &$coach) {
                $coach['is_coach'] = 0;
                if(isset($isCoaches[$coach['id']])) {
                    $coach['is_coach'] = 1;
                    $coach  = array_merge($isCoaches[$coach['id']], $coach);
                }
                if(isset($infos[$coach['id']])) {
                    $coach  = array_merge($infos[$coach['id']], $coach);
                }
                $coach['followed'] = 0;
                foreach ($relations as $relation) {
                    if($relation['re_user_id'] == $coach['id']) {
                        $coach['followed'] = 1;
                        break;
                    }
                }
                unset($coach);
            }
        }

        return ['coaches' => $coaches];
    }

    /**
     * @api               {get} /api/user/contents/{uid} 我的feed流
     * @apiGroup          用户中心
     * @apiName           我的feed流
     * @apiParam {String} sort time|hot
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * {
     *    "contents":[
     *          {
     *                "id":"1",
     *                    "title":"xxxxx",
     *                    "content":"xxxxxxxxxxxx",
     *                    "user":{
     *                          "user_id":"1",
     *                          "avatar":"",
     *                          "nickname":"xxxx"
     *                      },
     *                    "counts":{
     *                      "3":"100",
     *                      "6":"13567746",
     *                    }
     *          }
     *      ]
     * }
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function contents(Request $request): array
    {
        $uid = $request->route('uid');

        $relations = UserRelations::query()
            ->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->where('typ', 1)
            ->get()
            ->toArray();


        $relations[] = [
            'user_id'   =>  $uid,
            're_user_id'=>  $uid
        ];
//        if (empty($relations)) {
//            return ['contents' => []];
//        }

        $userIds = array_column($relations, 're_user_id');

        $contents = Content::query()
            ->whereIn('user_id', $userIds)
            ->where('status', Common::STATUS_NORMAL)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $contents = UserInfo::getUserInfoWithList($contents);
        $contents = ContentCounts::getContentsCounts($contents);

        $sort = $request->query('sort');
        if($sort != '' && $sort == 'hot') {
            uasort($contents, function ($a, $b) {
                return (($a['counts']['3']+$a['counts']['6']) > ($b['counts']['3']+$b['counts']['6'])) ? -1 : 1;
            });
        }
        return [
            'contents' => $contents
        ];
    }

    /**
     * @api               {get} /api/user/questions/{uid} 用户发布的问题
     * @apiGroup          用户中心
     * @apiName           用户发布的问题
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * {
     *    "questions":[
     *          {
     *                "id":"1",
     *                    "title":"xxxxx",
     *                    "content":"xxxxxxxxxxxx",
     *                    "user":{
     *                          "user_id":"1",
     *                          "avatar":"",
     *                          "nickname":"xxxx"
     *                      },
     *                    "counts":{
     *                      "3":"100",
     *                      "6":"13567746",
     *                    }
     *          }
     *      ]
     * }
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function questions(Request $request): array
    {
        $uid = $request->route('uid');
        $questions = Questions::query()
            ->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->orderBy('create_time', 'desc')
            ->get()
            ->toArray();

        $contents = UserInfo::getUserInfoWithList($questions);
        $contents = ContentCounts::getContentsCounts($contents);

        return [
            'questions' => $contents
        ];
    }

    /**
     * @api               {get} /api/user/answer/{uid} 用户回答的问题（针对提问）
     * @apiGroup          用户中心
     * @apiName           用户回答的问题（针对提问）
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * {
     *    "questions":[
     *          {
     *                "id":"1",
     *                    "title":"xxxxx",
     *                    "content":"xxxxxxxxxxxx",
     *                    "user":{
     *                          "user_id":"1",
     *                          "avatar":"",
     *                          "nickname":"xxxx"
     *                      },
     *                    "counts":{
     *                      "3":"100",
     *                      "6":"13567746",
     *                    },
     *                  "reply":{//回答信息或者空对象},
     *                  "replied":"1" //是否回答
     *          }
     *      ]
     * }
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function myAnswer(Request $request): array
    {
        $uid = $request->route('uid');
        $relations = QuestionAppoint::query()
            ->where('answer_id', $uid)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $contents = [];
        if (!empty($relations)) {
            $questionIds = array_column($relations, 'question_id');
            $questions = Questions::query()->whereIn('id', $questionIds)->get()->toArray();
            $contents = UserInfo::getUserInfoWithList($questions);
            $contents = ContentCounts::getContentsCounts($contents);

            $replies = QuestionReply::query()->whereIn('question_id', $questionIds)->where('user_id', $uid)->where('status', Common::STATUS_NORMAL)->get()->toArray();
            $replies = array_column($replies, null, 'question_id');
            foreach ($questions as &$question) {
                $question['replied'] = '0';
                $question['reply'] = new \stdClass();
                if (isset($replies[$question['id']])) {
                    $question['replied'] = '1';
                    $question['reply'] = $replies[$question['id']];
                }
                unset($question);
            }
        }

        return [
            'questions' => $contents
        ];
    }

    /**
     * @api               {post} /api/user/follow 关注
     * @apiGroup          用户操作
     * @apiName           关注
     *
     * @apiParam {String} user_id
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function follow(Request $request): array
    {
        $sudId = $request->input('user_id');
        if (!$sudId) {
            return ['code' => ErrorConstant::PARAMS_ERROR];
        }

        $userId = Auth::id();
        $exists = UserRelations::query()->where('user_id', $userId)->where('re_user_id', $sudId)
            ->where('typ', 1)->first();
        if (!$exists) {
            UserRelations::query()->insert([
                'user_id' => $userId,
                'typ' => 1,
                're_user_id' => $sudId
            ]);

            UserOpLog::query()->insert([
                'user_id' => $userId,
                'op_typ_id' => $sudId,
                'typ' => Common::USER_OP_FOLLOW
            ]);
            UserOpLog::query()->insert([
                'user_id' => $sudId,
                'op_typ_id' => $userId,
                'typ' => Common::USER_OP_BE_FOLLOW
            ]);
            UserCounts::incrementOrCreate($userId, Common::USER_OP_FOLLOW);
            UserCounts::incrementOrCreate($sudId, Common::USER_OP_BE_FOLLOW);
        } else {
            $exists = $exists->toArray();
            if ($exists['status'] == Common::STATUS_DISABLE) {
                UserRelations::query()->where('id', $exists['id'])->update([
                    'status' => Common::STATUS_NORMAL
                ]);
            }
        }

        return [];
    }


    /**
     * @api               {post} /api/user/unfollow 取消关注
     * @apiGroup          用户操作
     * @apiName           取消关注
     *
     * @apiParam {String} user_id
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function unFollow(Request $request): array
    {
        $sudId = $request->input('user_id');
        if (!$sudId) {
            return ['code' => ErrorConstant::PARAMS_ERROR];
        }

        $userId = Auth::id();
        $exists = UserRelations::query()->where('user_id', $userId)->where('re_user_id', $sudId)
            ->where('typ', 1)->first();
        if (!$exists) {
            return [];
        } else {
            $exists = $exists->toArray();
            if ($exists['status'] == Common::STATUS_NORMAL) {
                UserRelations::query()->where('id', $exists['id'])->update([
                    'status' => Common::STATUS_DISABLE
                ]);
                UserCounts::incrementOrCreate($userId, Common::USER_OP_FOLLOW, -1);
                UserCounts::incrementOrCreate($sudId, Common::USER_OP_BE_FOLLOW, -1);
            }
        }
        return [];
    }

    /**
     * @api               {get} /api/user/comments/:uid 我的评论
     * @apiGroup          内容操作
     * @apiName           我的评论
     *
     * @apiParam {String} content
     * @apiParam {String} cid  文章id
     * @apiParam {String} pid  上一层评论id，第一层为0
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     */
    /**
     * @param Request $request
     * @return array
     */
    public function comments(Request $request): array
    {
        $uid = $request->route('uid');
        //todo: 第二级评论回复不展示
        $comment = ContentComment::query()->where('user_id', $uid)->where('parent_id', 0)->get()->toArray();
        $result = [];
        if (!empty($comment)) {
            $contentIds = array_column($comment, 'content_id');

            $contents = Content::query()->whereIn('id', $contentIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
            $contents = ContentCounts::getContentsCounts($contents);
            $contents = UserInfo::getUserInfoWithList($contents);
            $contents = array_column($contents, null, 'id');
            foreach ($comment as $item) {
                $result[] = [
                    'comment' => $item,
                    'content' => $contents[$item['content_id']]
                ];
            }
        }

        return $result;
    }

    public function forgeUser(Request $request): array
    {
        $tags = Tags::query()->get()->toArray();
        foreach ($tags as $tag) {
            for ($i = 0; $i < 2; $i++) {
                $openId = Random::randomUuid();
                $id = Users::query()->insertGetId([
                    'username' => $openId,
                    'password' => '',
                    'typ' => 1,
                    'status' => 1
                ]);
                UserBind::query()->insert([
                    'user_id' => $id,
                    'typ' => 3,
                    'open_id' => $openId
                ]);
                UserInfo::query()->insert([
                    'user_id' => $id,
                    'nickname' => Random::randomString(rand(10, 20)),
                    'gender' => rand(1, 2),
                    'city' => 'Changsha',
                    'province' => 'HuNan',
                    'country' => 'China',
                    'avatar' => 'https://wx.qlogo.cn/mmopen/vi_32/bVfMeCPxSQsfBRc1XFHiaAn7DwbEE4iczf6rhSnj6LYDROgDW78ia0WC6I8IkVhJibicQrsiaGd3YXVUWcf8iaXGI35UQ/132',
                ]);

                $ucId = UserCoach::query()->insertGetId([
                    'glory' => '', 'real_name' => Random::randomString(rand(10, 20)), 'job' => '教练', 'desc' => '', 'intro' => '',
                    'courses' => '', 'services' => ''
                ]);

                UserCoachTags::query()->insert([
                    'tag_id' => $tag['id'],
                    'coach_id' => $ucId
                ]);
            }
        }

        return [];
    }


    /**
     * @api               {get} /api/user/topics/{uid} 我参与的话题
     * @apiGroup          用户中心
     * @apiName           我参与的话题
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * [
     *      "topics":
     *     {
     *                  "id": "1",
     *                  "title": "休息休息",
     *                  "follow": "https://xxxxxx/",
     *                  "followed":"1"
     *    },//...
     * ]
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function topics(Request $request): array
    {
        $uid = $request->route('uid');

        $relations = UserTopic::query()
            ->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->get()
            ->toArray();

        if (empty($relations)) {
            return ['topics' => []];
        }

        $topicIds = array_column($relations, 'topic_id');
        $topics = Topics::query()->whereIn('id', $topicIds)->get()->toArray();

        foreach ($topics as &$topic) {
            $topic['followed'] = 1;
            unset($topic);
        }
        return ['topics' => $topics];
    }

    /**
     * @api               {get} /api/user/scenes/{uid} 我参与的场景
     * @apiGroup          用户中心
     * @apiName           我参与的场景
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * [
     *      "scenes":
     *     {
     *                  "user_id": "1",
     *                  "name": "休息休息",
     *                  "avatar": "https://xxxxxx/",
     *                  "user_opinion":"dasds",
     *                  "self_value":"xxxx", //我的意见
     *
     *    },//...
     * ]
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function scenes(Request $request): array
    {
        $uid = $request->route('uid');

        $scenesReply = SceneReply::query()
            ->select(['scene_id', 'value'])
            ->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->get()
            ->toArray();

        if (empty($scenesReply)) {
            return ['scenes' => []];
        }

        $scenes = Scene::query()->whereIn('id', array_column($scenesReply, 'scene_id'))
            ->where('status', Common::STATUS_NORMAL)->get()->toArray();
        if (empty($scenes)) {
            return ['scenes' => []];
        }
        $sceneReCount = SceneReply::query()->whereIn('scene_id', array_column($scenes, 'id'))->where('status', Common::STATUS_NORMAL)
            ->where('status', Common::STATUS_NORMAL)->selectRaw('scene_id,count(*) as count')->groupBy('scene_id')->get()->toArray();
        $sceneReCount = array_column($sceneReCount, 'count', 'scene_id');
        $scenesReply = array_column($scenesReply, 'value', 'scene_id');

        foreach ($scenes as &$scene) {
            $scene['reply_count'] = 0;
            if (isset($sceneReCount[$scene['id']])) {
                $scene['reply_count'] = $sceneReCount[$scene['id']];
            }
            $scene['self_value'] = $scenesReply[$scene['id']];
            unset($topic);
        }
        return ['scenes' => $scenes];
    }


    /**
     * @api               {get} /api/user/coaches/{uid} 我关注的教练
     * @apiGroup          用户中心
     * @apiName           我关注的教练
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * [
     *     {
     *                  "join_time":"2020-04-20 12:12:12",
     *                  "tags"  :   [
     *                          {"id":"1","name":"教练标签"},//...
     *                  ],
     *                    "intro":"dsadasdsa",
     *                    "desc":"dasfghfdsfgdfgfhdg",
     *                    "user":{
     *                          "user_id":"111",
     *                          "nickname":"dsadsad",
     *                          "avatar":"erfgh"
     *                      },
     *                    "followed":"0" //0未关注  1已关注
     *
     *     }, //......
     */
    /**
     * @param Request $request
     *
     * @return array
     */
    public function coaches(Request $request): array
    {
        $uid = $request->route('uid');
        $coachIds = UserRelations::query()->where('user_id', $uid)->where('status', Common::STATUS_NORMAL)
            ->where('typ', 1)->get()->toArray();
        if (empty($coachIds)) {
            return ['coaches' => []];
        }
        $userIds= array_column($coachIds, 're_user_id');
        $coaches = UserCoach::query()->whereIn('user_id', $coachIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
        if (empty($coaches)) {
            return ['coaches' => []];
        }
        $coachIds = array_column($coaches, 'id');
        //$userIds = array_column($coaches, 'user_id');
        $coachTags = UserCoachTags::query()->whereIn('coach_id', $coachIds)->get()->toArray();
        $tagsIds = array_column($coachTags, 'tag_id');
        $tags = Tags::query()->whereIn('id', $tagsIds)->get()->toArray();
        $tags = array_column($tags, null, 'id');
        $userInfo = UserInfo::query()->whereIn('user_id', $userIds)->get()->toArray();
        $userInfo = array_column($userInfo, null, 'user_id');
        foreach ($coaches as &$coach) {
            $coach['tags'] = [];
            foreach ($coachTags as $coachTag) {
                if ($coachTag['coach_id'] == $coach['id']) {
                    if (isset($tags[$coachTag['tag_id']])) {
                        $coach['tags'][] = $tags[$coachTag['tag_id']];
                    }
                }
            }
            $coach['user'] = $userInfo[$coach['user_id']];
            $coach['followed'] = 1;
            unset($coach);
        }
        return [
            'coaches' => $coaches
        ];
    }

    /**
     * @api               {get} /api/user/favorites 我的收藏
     *
     * @apiGroup          用户中心
     * @apiName           我的收藏
     *
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "contents": [
     *          {
     *              'content':{
     *              "id": "1",
     *              "name": "真人秀",
     *              "sort": "1",
     *              "status": "1",
     *              "create_time": "1588069984",
     *              "update_time": "1588069984"
     *              },
     *              'user':{//userinfo}
     *          }
     *          ,//...
     *      ],
     *   }
     */
    /**
     * @param Request $request
     * @return array
     */
    public function favorites(Request $request): array
    {
        $uid = $request->route('uid');
        $favorites = UserFavorites::query()->where('user_id', $uid)->get()->toArray();
        if (empty($favorites)) {
            return [
                'contents' => []
            ];
        }

        $result = Content::query()->whereIn('id', array_column($favorites, 'obj_id'))->where('status', Common::STATUS_NORMAL)->get()->toArray();
        $result = UserInfo::getUserInfoWithList($result);
        $result = ContentCounts::getContentsCounts($result);
        $result = Resources::getResources($result);

        return [
            'contents' => $result
        ];
    }

    /**
     * @api               {get} /api/user/notices 我的通知
     *
     * @apiParam {String} page
     * @apiParam {String} limit
     * @apiGroup          用户中心
     * @apiName           我的通知
     *
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "contents": [
     *          {
     *                   'op':'点赞了你的文章',
     *                  'name':"文章标题",
     *                  "user":{//userinfo 操作人}
     *          }
     *          ,//...
     *      ],
     *   }
     */
    /**
     * @param Request $request
     * @return array
     */
    public function notices(Request $request): array
    {
        $uid = $request->route('uid');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 15);
        $notices = UserNotice::query()->where('user_id', $uid)
            ->where('status', Common::STATUS_NORMAL)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->get()->toArray();
        if (empty($notices)) {
            return [
                'contents' => []
            ];
        }

        $contentIds = [];
        $questionIds = [];
        $sceneIds = [];
        $userIds = [];
        foreach ($notices as $notice) {
            switch ($notice['typ']) {
                case Common::CONTENT_CONTENT:
                    $contentIds[] = $notice['obj_id'];
                    break;
                case Common::CONTENT_QUESTION:
                    $questionIds[] = $notice['obj_id'];
                    break;
                case Common::CONTENT_SCENE:
                    $sceneIds[] = $notice['obj_id'];
                    break;
                case Common::CONTENT_USER:
                    $userIds[] = $notice['obj_id'];
                    break;
            }
        }


        $results = [];
        if (!empty($contentIds)) {
            $contents = Content::query()->whereIn('id', $contentIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
            foreach ($contents as &$content) {
                foreach ($notices as $notice) {
                    if ($notice['obj_id'] == $contents['id'] and $notice['typ'] == Common::CONTENT_CONTENT) {
                        switch ($notice['op_type']) {
                            case Common::USER_OP_BE_ZAN:
                            case Common::USER_OP_ZAN:
                                $request[] = [
                                    'user_id' => $notice['op_user_id'],
                                    'op' => '点赞了你的文章',
                                    'name' => $content['name']
                                ];
                                break;
                            case Common::USER_OP_FAVORITES:
                            case Common::USER_OP_BE_FAVORITES:
                                $request[] = [
                                    'user_id' => $notice['op_user_id'],
                                    'op' => '收藏了你的文章',
                                    'name' => $content['name'],
                                    'time' => $notice['create_time']
                                ];
                        }
                    }
                }
            }
        }

        if (!empty($sceneIds)) {
            $contents = Scene::query()->whereIn('id', $contentIds)->get()->toArray();
            foreach ($contents as &$content) {
                foreach ($notices as $notice) {
                    if ($notice['obj_id'] == $contents['id'] and $notice['typ'] == Common::CONTENT_SCENE) {
                        switch ($notice['op_type']) {
                            case Common::USER_OP_REPLY_SCENE:
                                $request[] = [
                                    'user_id' => $notice['op_user_id'],
                                    'op' => '参与了你的话题',
                                    'name' => $content['name'],
                                    'time' => $notice['create_time']
                                ];
                                break;
                        }
                    }
                }
            }
        }

        if (!empty($questionIds)) {
            $contents = Questions::query()->whereIn('id', $contentIds)->get()->toArray();
            foreach ($contents as &$content) {
                foreach ($notices as $notice) {
                    if ($notice['obj_id'] == $contents['id'] and $notice['typ'] == Common::CONTENT_QUESTION) {
                        switch ($notice['op_type']) {
                            case Common::USER_OP_BE_QUESTION:
                            case Common::USER_OP_ANSWER:
                            case Common::USER_OP_QUESTION:
                                $request[] = [
                                    'user_id' => $notice['op_user_id'],
                                    'op' => '回答了你的提问',
                                    'name' => $content['title'],
                                    'time' => $notice['create_time']
                                ];
                                break;
                        }
                    }
                }
            }
        }

        if (!empty($userIds)) {
            $userIds = array_unique($userIds);
            foreach ($userIds as $userId) {
                foreach ($notices as $notice) {
                    if ($userId == $notice['obj_id'] and $notice['typ'] == Common::CONTENT_USER) {
                        $request[] = [
                            'user_id' => $notice['op_user_id'],
                            'op' => '关注了你',
                            'name' => '',
                            'time' => $notice['create_time']
                        ];
                    }
                }
            }
        }

        $results = UserInfo::getUserInfoWithList($results);
        uasort($results, function ($a, $b) {
            return ($a['time'] > $b['time']) ? -1 : 1;
        });
        return [
            'contents' => $results
        ];
    }
}