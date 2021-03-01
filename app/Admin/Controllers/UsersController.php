<?php
/**
 * Created by inke.
 * User: liwenlong@inke.cn
 * Date: 2020/7/28
 * Time: 19:59
 */

namespace App\Admin\Controllers;


use App\Admin\Actions\Content\Disable;
use App\Admin\Models\Users;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends AdminController
{

    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return "用户列表";
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Users());

        $grid->column('id', 'ID')->sortable();
        $grid->column('nickname', '昵称');
        $grid->column('create_time', '注册时间');
        $grid->column('update_time', '更改时间');
        $grid->disableCreateButton();
        $grid->disableExport();
        //$grid->disableActions();
        $grid->column('status', '状态')->display(function ($permission) {
            //->->as(function ($permission) {
            return $permission == 1 ? '正常' : '禁用';
        })->filter([
            0 => '禁用',
            1 => '正常'
        ]);;
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
        $show = new Show(Users::findOrFail($id));

        $show->field('id', "用户id");
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
        $form = new Form(new Users());
        $form->display('id', 'ID');
        $form->display('create_time', '注册时间');
        $form->display('update_time', '更改时间');

        return $form;
    }
}