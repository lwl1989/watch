<?php

namespace App\Models\RegisterUsers;

use App\Models\Model;


class UserOpLog extends Model
{
    public $table = 'user_op_log';


    public $timestamps = true;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';


}