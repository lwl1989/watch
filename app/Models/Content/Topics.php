<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/5/5
 * Time: 11:12
 */

namespace App\Models\Content;


use App\Models\Model;

class Topics extends Model
{
    public $table = 'topics';


    public $timestamps = true;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}