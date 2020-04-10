<?php

namespace App\Admin\Controllers;

use App\Models\Profession;
use App\Models\Squad;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SquadController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '班级管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Squad());

        $grid->column('id', __('ID'));
        $grid->column('profession.full_name', __('所属专业'));
        $grid->column('name', __('班级名称'));
        $grid->column('info', __('备忘'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name','班级名称');
            $filter->like('profession.full_name','所属专业');
        });

        $grid->disableColumnSelector();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Squad::findOrFail($id));

//        $show->field('id', __('ID'));
        $show->field('name', __('班级名称'));
        $show->field('info', __('备忘'));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });;

        $show->profession('所属专业信息', function ($profession) {
            $profession->setResource('/admin/professions');

            $profession->full_name('专业全称');
            $profession->intro('专业简介');

            $profession->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableList();
                    $tools->disableDelete();
                });;

        });

        $show->studentSquads('班级学生', function ($students)  use ($id){

            $students->resource('/admin/student-squads');

            $students->column('student.name', '学生名称');
            $students->column('squad.name', '班级名称')->label();

            $students->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('name', '学生名称');
            });

            $students->disableExport();
            $students->disableColumnSelector();

            $students->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();
//                $actions->disableDelete();
            });

        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Squad());

        $form->select('profession_id', __('所属专业'))
            ->rules('required')
            ->options(Profession::all()->pluck('full_name', 'id'));
        $form->text('name', __('班级名称'))
            ->rules('required');
        $form->text('info', __('备忘'));

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });

        return $form;
    }
}
