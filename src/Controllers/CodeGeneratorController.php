<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Controllers;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Helpers\Model\CodeGenerator;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

/**
 * 自动生成代码的记录
 * Class CodeGeneratorController
 *
 * @package Encore\Admin\Helpers\Controllers
 */
class CodeGeneratorController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header("Records");
            $content->body($this->grid()->render());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header("Records");
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header("Records");
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return \Encore\Admin\Grid
     */
    protected function grid()
    {
        return Admin::grid(CodeGenerator::class, function (Grid $grid) {
            $grid->id('ID')->sortable();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(CodeGenerator::class, function (Form $form) {
            $form->displayE('id', 'ID');


            $form->displayE('created_at', trans('admin.created_at'));
            $form->displayE('updated_at', trans('admin.updated_at'));
        });
    }
}
