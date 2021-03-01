<?php

namespace App\Library\Constant;


class Common
{
    //1 发布数 3 被评论数 4评论数 5 点赞数 6 被点赞数 7回答数 8 关注  9 被关注  10 问题数 11 被提问题数 12 发布场景 13 回复场景
    const USER_OP_RELEASE = 1;
    const USER_OP_BE_COMMENT = 3;
    const USER_OP_COMMENT = 4;
    const USER_OP_ZAN = 5;
    const USER_OP_BE_ZAN = 6;
    const USER_OP_ANSWER = 7;
    const USER_OP_FOLLOW = 8;
    const USER_OP_BE_FOLLOW = 9;

    const USER_OP_RELEASE_SCENE = 12;
    const USER_OP_REPLY_SCENE = 13;

    const USER_OP_FAVORITES = 12;
    const USER_OP_BE_FAVORITES = 13;

    const USER_OP_QUESTION = 10;
    const USER_OP_BE_QUESTION = 11;

    //1 锦囊 2素材 3随记
    const CONTENT_TYPE_BAG = 1;
    const CONTENT_TYPE_MATERIAL = 2;
    const CONTENT_TYPE_NOTES = 3;


    const CONTENT_CONTENT = 1;
    const CONTENT_SCENE = 2;
    const CONTENT_QUESTION = 3;
    const CONTENT_USER = 4;
    //共用
    const DELETED = 1;
    const NO_DELETE = 0;
    //能共用就共用
    const STATUS_NORMAL = 1;
    const STATUS_DISABLE = 2;
    //管理員權限
    const ADMIN_ROLE_SHOP = 1;
    const ADMIN_ROLE_EMPLOYEE = 2;
    const ADMIN_ROLE_MANAGER = 3;


    const ROLE_MAP = [
        self::ADMIN_ROLE_SHOP => '特約商店',
        self::ADMIN_ROLE_EMPLOYEE => '縣府員工',
        self::ADMIN_ROLE_MANAGER => '管理員'
    ];

    public static function getRoleNumber(string $role): int
    {
        $arr = array_reverse(self::ROLE_MAP);
        return isset($arr[$role]) ? $arr[$role] : 0;
    }

    const PERMISSION_MAPPING_ADMIN = [

    ];
    const PERMISSION_MAPPING_SHOP = [
        //        'exchange' => [
        //            'name' => '好禮兌換',
        //            'actions' => [
        //                [
        //                    'en' => 'goods',
        //                    'name' => '商品兌換',
        //                    'vue' => 'goods/list',
        //                ],
        //                [
        //                    'en' => 'record',
        //                    'name' => '兌換記錄查詢',
        //                    'vue' => 'goods/record',
        //                ],
        //            ]
        //        ]
    ];

    const PERMISSION_MAPPING = array();

}