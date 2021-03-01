<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/7/28
 * Time: 19:59
 */

namespace App\Admin\Controllers;


use App\Admin\Actions\Content\Disable;
use App\Admin\Models\Contents;
use App\Admin\Models\Users;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ContentsController extends AdminController
{

    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return "内容列表";
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Contents());

        $grid->column('id', '文章id')->sortable();
        $grid->column('title', '标题');
        $grid->column('create_time', '发布时间');
        $grid->column('update_time', '更改时间');
//        $grid->column('cover', '图片')->display(function ($v) {
//            if ($v != "") {
//                $v = str_replace('http:', 'https:', $v);
//                return '<img src="' . $v . '" width=80 height=80>';
//            }
//            return '';
//        });
        $grid->disableCreateButton();
        $grid->disableExport();
        //$grid->disableActions();
        $grid->column('status', '状态')->display(function ($permission) {
            //->->as(function ($permission) {
            return $permission == 1 ? '正常' : '禁用';
        })->filter([
            0 => '禁用',
            1 => '正常'
        ]);
        //1 锦囊 2素材 3随记
        $grid->column('typ', '文章类型')->display(function ($v) {
            //->->as(function ($permission) {
            if($v == 1) {
                return '锦囊';
            }
            if($v == 2) {
                return '素材';
            }
            if($v ==3) {
                return '随记';
            }
            return '锦囊';
        })->filter([
            1 => '锦囊',
            2 => '素材',
            3 => '随记',
        ]);

        //1 图文 2 视频 3 音频
        $grid->column('template_id', '内容类型')->display(function ($v) {
            //->->as(function ($permission) {
            if($v == 1) {
                return '图文';
            }
            if($v == 2) {
                return '视频';
            }
            if($v == 3) {
                return '音频';
            }
            return '图文';
        })->filter([
            1 => '图文',
            2 => '视频',
            3 => '音频',
        ]);
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableDelete();

            $actions->add(new Disable());
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Contents::findOrFail($id));

        $show->field('id', "文章id");
        $show->field('create_time', __('Created at'));
        $show->field('update_time', __('Updated at'));

        return $show;
    }

    public function index(Content  $content)
    {
        return $content
            ->title($this->title())
            ->description('')
            ->row($this->grid());
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Contents());
        $form->display('id', '文章id');
        $form->display('create_time', '注册时间');
        $form->display('update_time', '更改时间');

        return $form;
    }
}