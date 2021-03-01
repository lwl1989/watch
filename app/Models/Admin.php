<?php
namespace App\Models;


use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\HasPermissions;

class Admin extends Administrator
{
    protected $table = 'admin_users';
    use  HasPermissions;
}