<template>
    <div id="app">
        <el-row class="home-header-every">
            <el-col :span="3">
                <div class="home-header">
                    <div class="home-span" @click="goToIndex">
                        <span>TTPush</span>
                    </div>
                </div>
            </el-col>
            <el-col :span="16">
                <el-menu
                        :default-active="$route.path"
                        unique-opened
                        router
                        mode="horizontal"
                        class="el-button--primary-menu"
                        background-color="#1D8CE0"
                        text-color="#ffffff"
                        active-text-color="#ffffff"
                >
                    <template v-for="(item) in $router.options.routes" v-if="!item.hidden">
                        <el-submenu :index="item.sort+''">
                            <template slot="title">
                                {{item.sort}}. {{item.name}}
                            </template>
                            <template v-for="child in item.children">
                                <el-menu-item :index="'/'+child.path" :key="child.path" v-if="!child.hidden" @click="goRouter('/'+child.path)">
                                    {{child.name}}
                                </el-menu-item>
                            </template>
                        </el-submenu>
                    </template>
                </el-menu>
            </el-col>
            <el-col :span="5">
                <div class="home-header">
                    <div class="home-icon">
                        <el-dropdown split-button type="primary" @click="handleClick" @command="handleCommand">
                            歡迎 {{ username }} 登入
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="logout">登出</el-dropdown-item>
                                <el-dropdown-item command="changePass">修改密碼</el-dropdown-item>
                                <el-dropdown-item command="loadHandBook1" v-if="role==3">教育訓練手冊(管理員)</el-dropdown-item>
                                <el-dropdown-item command="loadHandBook2" v-if="role>1">教育訓練手冊(縣府員工)</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-row>
            <el-col :span="24">
                <div class="home-header-breadcrumb">
                    <el-breadcrumb separator="/" class="breadcrumb-inner">
                        <el-breadcrumb-item :to="{ path: '/notice' }">TTPush</el-breadcrumb-item>
                        <el-breadcrumb-item v-for="(item, index) in $route.matched" :key="index">
                            {{ item.name }}
                        </el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
            </el-col>
        </el-row>
        <div style="padding:0 20px 20px;">
            <el-row>
                <el-col :span="24">
                    <router-view v-on:refresh="openRefresh" v-on:success="openSuccess"
                                 v-on:warning="openWarning"></router-view>
                </el-col>
            </el-row>
        </div>

        <change-admin-password ref="ChangePassComment"></change-admin-password>
    </div>
</template>

<script>
    import ChangeAdminPassword from './admin/ChangeAdminPasswordComponent'
    import Tools from '../tools/vue-common-tools'

    export default {
        data() {
            return {
                menuIndex:0,
                username: '',
                adminId:0,
                role: 1,
                dialogTableVisible: false,
            }
        },

        components: {ChangeAdminPassword},

        created() {
            axios.get('/admin/info').then((res) => {
                this.username = res.data.response.username;
                this.adminId = res.data.response.adminId;
                this.role = res.data.response.role
            });
        },

        methods: {
            handleClick() {
                this.dialogTableVisible = true;
            },

            handleCommand(command) {
                if (command === 'logout') {
                    this.logout();
                } else if (command === 'loadHandBook1'){
                    Tools.OpenNewWindow('/file/02教育訓練手冊(管理員).pdf','教育訓練手冊(管理員)',800,1024)
                } else if (command === 'loadHandBook2'){
                    Tools.OpenNewWindow('/file/03教育訓練手冊(縣府員工).pdf','教育訓練手冊(縣府員工)',800,1024)
                }else {
                    this.$refs.ChangePassComment.doOpenPasswordPage(this.adminId);
                }
            },

            goToIndex() {
                this.$router.push({path: '/notice'})
            },

            logout() {
                this.$confirm('確定要登出嗎？', '提示', {
                    confirmButtonText: '確定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    window.location.href = '/logout';
                }).catch(() => {

                });
            },

            openSuccess(callback, message) {
                if (typeof(message) === 'undefined') {
                    message = '操作成功'
                }
                this.openDialog('success', callback, message);
            },

            openWarning(callback, message) {
                if (typeof(message) === 'undefined') {
                    message = '操作失敗，請檢查'
                }
                this.openDialog('warning', callback, message);
            },

            openDialog(type, callback, message) {
                this.$message({
                    type: type,
                    message: message
                });
                if (typeof(callback) === 'function') {
                    callback();
                }
            },

            openRefresh(message, callback) {
                let h = this.$createElement;
                this.$msgbox({
                    title: '提示',
                    message: h('p', null, [
                        h('span', null, message)
                    ]),
                    showCancelButton: true,
                    confirmButtonText: '確定',
                    cancelButtonText: '取消',
                    beforeClose: (action, instance, done) => {
                        if (action === 'confirm') {
                            callback();
                            done();
                        } else {
                            done();
                        }
                    },
                }).then(action => {

                    //執行完畢
                    //console.log(action);
                }).catch(e => {
                    //執行異常
                    //console.log(e)
                });
            },
            goRouter(router){
                // if(router === window.location.hash.substr(1)) {
                //     window.location.reload(true);
                // }
                //this.$router.push({path: router})
            }
        }
    }
</script>

<style>
    *{margin:0;padding:0}
    .el-button--primary-menu {
        padding: 0;
        margin: 0;
        height: 60px;
    }

    .el-button--primary {
        background: #1D8CE0;
    }

    .el-button--primary-menu i {
        color: #ffffff;
    }

    .home-header-every {
        width: 100%;
        height: 60px;
        padding: 0;
        margin: 0 auto;
        background: #1D8CE0;
    }

    .home-header {
        width: 100%;
        height: 50px;
        padding: 5px 0;
        background: #1D8CE0;
    }

    .home-span {
        float: left;
        cursor: pointer;
    }

    .home-span span {
        line-height: 50px;
        font-size: 24px;
        color: #FFF;
        margin-left: 20px;
    }

    .home-icon {
        float: right;
        margin-right: 20px;
        line-height: 50px;
        cursor: pointer;
    }

    .home-icon i {
        color: #FFF;
    }

    .home-header-menu {
        background: #eff1f6;
    }

    .menu-expanded {
        width: 230px;
        float: left;
    }

    .content-container {
        float: left;
    }

    .home-header-breadcrumb {
        height: 20px;
        padding: 20px;
        background: #FFF;
    }

    .home-header-router {
        width: 100%;
        padding-left: 20px;
        padding-right: 20px;
        background: #FFF;
    }

    .el-menu {
        border-radius: 0;
    }

    .el-breadcrumb__item {
        line-height: 20px;
    }
</style>
