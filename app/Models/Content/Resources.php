<?php


namespace App\Models\Content;


use App\Library\Constant\Common;
use App\Models\Model;

class Resources extends Model
{
    public $table = 'content_resources';

    protected $fillable = ['content_id'];
    public $timestamps = true;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    public static function getResources(array $list) : array
    {
        if (empty($list)) {
            return $list;
        }

        $listIds = array_column($list, 'id');

        $ccs = Resources::query()->whereIn('content_id', $listIds)->where('status', Common::STATUS_NORMAL)->get()->toArray();
        // var_dump($ccs);
        foreach ($list as &$item) {
            $item['resources'] = [];
            foreach ($ccs as $c) {
                if($c['content_id'] == $item['id']) {
                    $item['resources'][] = $item;
                }
            }
            unset($item);
        }

        return $list;
    }
}