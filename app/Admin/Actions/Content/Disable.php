<?php

namespace App\Admin\Actions\Content;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Disable extends RowAction
{
    public $name = '禁用/启用';

    public function handle(Model $model)
    {
        // $model ...
        if($model->status == 0) {
            $model->status = 1;
        }else{
            $model->status = 0;
        }
        $model->save();

        return $this->response()->success('操作成功')->refresh();
    }

}