<?php

namespace App\Models\Log;


use Illuminate\Database\Eloquent\Model;

class SqlErrorLog extends Model
{
    protected $table = 'll_sql_log';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}