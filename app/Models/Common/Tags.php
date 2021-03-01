<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/4/28
 * Time: 18:21
 */

namespace App\Models\Common;


use App\Models\Model;

class Tags extends Model
{
    public $table = 'tags';


    public $timestamps = true;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}