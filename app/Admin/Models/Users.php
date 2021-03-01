<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/7/28
 * Time: 20:35
 */

namespace App\Admin\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Users extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    public $table = "users";
    public function paginate()
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;

        $result = Users::query()
            ->offset($start)->limit($perPage)
            ->join('user_info', 'users.id', '=', 'user_info.user_id')->get();
        $total = Users::query()->count();
        //$movies = static::hydrate($subjects);

        $paginator = new LengthAwarePaginator($result, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

    // 获取单项数据展示在form中
    public static function findOrFail($id)
    {
        return self::query()->find($id);
    }

}