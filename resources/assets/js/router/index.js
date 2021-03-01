import Vue from 'vue'
import Router from 'vue-router'


import Notice from '../components/admin/NoticeComponent'
import Admin from '../components/admin/AdminIndexComponent'
import AdminDetail from '../components/admin/AdminDetailComponent'
import UsersDetail from '../components/content/UserDetailComponent'
import Company from '../components/admin/CompanyComponent'
import CompanyDetail from '../components/admin/CompanyDetailComponent'

import Home from '../components/CommonComponent'
import ChangePass from '../components/admin/ChangePassComponent'

import UserComponent from '../components/content/UserComponent'

import Deparment from '../components/deparment/DeparmentIndex'
import DeparmentGroup from '../components/deparment/DeparmentGroup'
import DepartmentGroupMember from '../components/deparment/DepartmentGroupMember'
import DepartmentGroupSearchAdd from '../components/deparment/DepartmentGourpSearchAdd'

import GoldAccount from '../components/gold/GoldAccount'
import GoldSend from '../components/gold/GoldSend'
import GoldExport from '../components/gold/GoldExport'
import GoldRecyclePerson from '../components/gold/GoldRecyclePerson'
import GoldExternal from '../components/gold/GoldExternal'

import Goods from  '../components/goods/GoodsListComponent'
import GoodsDetail from '../components/goods/GoodsDetailComponent'
import GoodsCategoryList from '../components/goods/GoodsCategoryListComponent'
import PreferentialList from '../components/goods/PreferentialListComponent'
import PreferentialUsers from '../components/goods/PreferentialUsersComponent'
import GoodsRecord from  '../components/goods/GoodsRecordComponent'
import GoodsRecordDetail from  '../components/goods/GoodsRecordDetailComponent'

import MessageList from '../components/message/MessageList'
import MessageActivity from '../components/message/MessageActivity'
import MessageSetting from '../components/message/MessageSetting'
import MessageQuestion from '../components/message/MessageQuestion'
import ActivityUserDetail from '../components/message/MessageActivityUserDetailComponent'
import MessageReadMember from '../components/message/MessageReadGroupMember'
import MessageSatisfaction from '../components/message/MessageSatisfactionDetail'

import Cookies from '../tools/vue-cookies';


import AD from '../components/content/AdComponent'
import APPS from '../components/content/AppComponent'
import Versions from '../components/content/VersionsComponent'
import Banner from '../components/content/BannerComponent'
import AppWelCome from '../components/content/WelcomeComponent'
import Password from '../components/content/PasswordComponent'

import MessageActivityOnlineList from '../components/message/MessageActivityOnlineList'
import MessageActivityOfflineList from '../components/message/MessageActivityOfflineList'
import PersonDetailComponent from '../components/gold/PersonDetailComponent'
import QuestionnaireRecovery from '../components/message/QuestionnaireRecovery'

import TaxNotice from '../components/tax/TaxNoticeComponent'
import TaxLicence from '../components/tax/TaxLicenceComponent'

Vue.use(Router);

let routes = [
    {
        path: '/',
        redirect:'/notice',
        component: Home,
        name:'首頁',
        hidden: true,
        sort:0,
        children: [
            { path: 'notice', component: Notice, name: '通知',hidden:true, canOpen:true},

        ]
    },
    {
        path: '/',
        component: Home,
        name: '帳號管理',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:1,
        children: [
            { path: 'admins', component: Admin, name: '縣府員工' ,hidden: true},
            { path: 'department', component: Deparment, name: '縣府單位',hidden: true },
            { path: 'company', component: Company, name: '特約商店' ,hidden: true},
        ]
    },
    {
        path: '/',
        component: Home,
        name: '資料維護',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:2,
        children: [
            { path: 'users', component: UserComponent, name: '臺東縣民' ,hidden: true},
            { path: 'department/group', component: DeparmentGroup, name: '縣民群組' ,hidden: true},
            { path: 'content/ad', component: AD, name: '廣告活動' ,hidden: true},
            { path: 'content/banner', component: Banner, name: '置頂公告' ,hidden: true},
            { path: 'content/welcome', component: AppWelCome, name: '歡迎頁' ,hidden: true},
            { path: 'content/app', component: APPS, name: 'App小舖' ,hidden: true},
            { path: 'content/password', component: Password, name: '驗證碼查詢' ,hidden: true},
            { path: 'content/versions', component: Versions, name: '版本管理' ,hidden: true},
        ]
    },
    {
        path: '/',
        component: Home,
        name: '事件管理',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:3,
        children: [
            { path: 'message/setting', component: MessageSetting, name: '推播設定' ,hidden: true},
            { path: 'message/list', component: MessageList, name: '推播訊息' ,hidden: true},
            //{ path: 'message/auto', component: MessageAuto, name: '自動推播訊息' ,hidden: true},
            { path: 'message/question', component: MessageQuestion, name: '建立問卷' ,hidden: true},
            { path: 'message/activity', component: MessageActivity, name: '建立活動',hidden: true },
        ]
    },
    {
        path: '/',
        component: Home,
        name: '臺東金幣',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:4,
        children: [
            { path: 'gold/account', component: GoldAccount, name: '金幣帳戶' ,hidden: true},
            { path: 'gold/send', component: GoldSend, name: '金幣發放' ,hidden: true},
            { path: 'gold/recycle', component: GoldRecyclePerson, name: '個人金幣' ,hidden: true},
            { path: 'gold/export', component: GoldExport, name: '產生報表' ,hidden: true},
            { path: 'gold/external', component: GoldExternal, name: '管理外部應用程式' ,hidden: true},
        ]
    },
    {
        path: '/',
        component: Home,
        name: '好禮兌換',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:5,
        children: [
            { path: 'goods/list', component: Goods, name: '商品兌換' ,hidden: true},
            { path: 'goods/record', component: GoodsRecord, name: '兌換記錄查詢' ,hidden: true},
            { path: 'goods/category/list',component: GoodsCategoryList, name: '類別維護' ,hidden: true},
            { path: 'goods/preferential', component: PreferentialList, name: '數位縣民優惠', hidden: true}
            //{ path: 'oBike/voucher', component: OBike, name: 'oBike優惠券' ,hidden: true},
        ]
    },
    {
        path: '/',
        component: Home,
        name: '繳稅通知',
        iconCls: 'el-icon-d-caret',
        hidden: true,
        sort:6,
        children: [
            { path: 'tax/notice', component: TaxNotice, name: '通知紀錄' ,hidden: true},
            { path: 'tax/licence', component: TaxLicence, name: '車牌資料設定' ,hidden: true},
        ]
    },

    { path: '/edit/goods/:id', component: GoodsDetail, name: '商品編輯' ,hidden: true, canOpen:true},
    { path: '/edit/admin/:id', component: AdminDetail, name: '用戶編輯' ,hidden: true, canOpen:true},
    { path: '/edit/company/:id', component: CompanyDetail, name: '商店編輯' ,hidden: true, canOpen:true},
    { path: '/edit/users/:id', component: UsersDetail, name: '臺東縣民編輯' ,hidden: true, canOpen:true},
    { path: '/changePass', component: ChangePass, name: '修改密碼' ,hidden: true, canOpen:true},
    { path: '/edit/record', component: GoodsRecordDetail, name: '兌換排行榜', hidden: true, canOpen:true},

    /**! 用戶活動報名列表 !**/
    { path: '/activity/online/:id', component: MessageActivityOnlineList, name: '活動報名列表' ,hidden: true, canOpen:true},
    { path: '/activity/offline/:id', component: MessageActivityOfflineList, name: '現場活動報名列表' ,hidden: true, canOpen:true},
    { path: '/activity/online/:userId/:activityName/:mobile/:name/:sex/:offlineCreateTime/:activityId', component: ActivityUserDetail, name: '報名表' ,hidden: true, canOpen:true},

	/**! 問卷回收列表 !**/
    { path: '/message/questionnaire/:id', component: QuestionnaireRecovery, name: '問卷回收列表' ,hidden: true, canOpen:true},
    /** 消息已讀成員列表 **/
    { path: '/message/readGroupMembers/:msgId/:groupId', component: MessageReadMember, name: '消息已讀成員列表', hidden: true, canOpen:true},
    /** 消息已讀成員列表 **/
    { path: '/message/satisfaction/:msgId/:surveyId', component: MessageSatisfaction, name: '滿意度回饋清冊', hidden: true, canOpen:true},
    /**
     * 個人金幣回收查看個人金幣頁面
     */
    { path: '/gold/person/recycle/:id', component: PersonDetailComponent, name: '個人金幣列表' ,hidden: true, canOpen:true},

    /** 數位縣民優惠使用列表 **/
    { path: '/goods/preferentialUsers/:id', component: PreferentialUsers, name: '數位縣民優惠使用記錄', hidden:true, canOpen:true },

    /** 縣民群組名單頁 **/
    { path: '/departmentGroup/members/:id', component: DepartmentGroupMember, name: '群組名單' ,hidden: true, canOpen:true}
];

let router = Cookies.getCookie('router');
router = decodeURIComponent(router);

if(router == null || router === '') {
    router = sessionStorage.getItem('router');
}

if(router == null || router === '') {
    window.location.href = '/logout';
}

try {
    router = JSON.parse(router);
    if(router == null || router === '') {
        window.location.href = '/logout';
    }
}catch (e) {
    window.location.href = '/logout';
}

let routeIndex = 0;
routes.forEach((r,k)=>{
    if (!("redirect" in r)) {
        if ("children" in r && r.children.length > 0) {
            r.children.forEach((item) => {
                //等于0時也匹配
                if (router.indexOf(item.path) >= 0) {
                    r.hidden = false;
                    item.hidden = false;
                }
            });
        }
        
        if (!r.hidden) {
            r.sort = ++routeIndex;
        }
    }
});

let myRouter = new Router({routes});

export default myRouter;