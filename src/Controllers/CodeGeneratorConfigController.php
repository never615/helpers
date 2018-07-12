<?php
/**
 * Copyright (c) 2018. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\Admin\Helpers\Controllers;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Helpers\Model\CodeGeneratorConfig;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class CodeGeneratorConfigController extends Controller
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
            $content->header("Configs");
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
            $content->header("Configs");
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
            $content->header("Configs");
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
        return Admin::grid(CodeGeneratorConfig::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name()->editable();
            $grid->model_namespace()->editable();
            $grid->controller_namespace()->editable();
            $grid->model_path()->editable();
            $grid->controller_path()->editable();
            $grid->route_path()->editable();
//            $grid->permission_seeder_path()->editable();
//            $grid->menu_seeder_path()->editable();
//            $grid->run_seeder_command()->editable();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(CodeGeneratorConfig::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text("name");
            $form->text("model_namespace");
            $form->text("controller_namespace");
            $form->text("model_path");
            $form->text("controller_path");
            $form->text("migration_path");
            $form->text("route_path");
            $form->text("permission_seeder_path");
            $form->text("menu_seeder_path");
            $form->text("run_seeder_command");

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        });
    }
}
