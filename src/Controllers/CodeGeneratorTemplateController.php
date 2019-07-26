<?php

namespace Encore\Admin\Helpers\Controllers;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Helpers\Model\CodeGeneratorConfig;
use Encore\Admin\Helpers\Model\CodeGeneratorTemplate;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class CodeGeneratorTemplateController extends Controller
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
            $content->header("Template");
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
            $content->header("Template");
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
            $content->header("Template");
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
        return Admin::grid(CodeGeneratorTemplate::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(CodeGeneratorTemplate::class, function (Form $form) {
            $form->displayE('id', 'ID');
            $form->text("name");
            $form->textarea("model");
            $form->textarea("controller");
            $form->textarea("migration");
            $form->textarea("router");
            $form->textarea("menu");
            $form->textarea("permission");


            $form->displayE('created_at', trans('admin.created_at'));
            $form->displayE('updated_at', trans('admin.updated_at'));
        });
    }
}
