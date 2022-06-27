<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\Category;
use Illuminate\Support\Str;
use Encore\Admin\Controllers\AdminController;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->id('ID');
        $grid->name();
        $grid->slug();

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
        $show = new Show(Category::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category());

        $form->text('name', 'Name')
            ->placeholder('Input category')
            ->creationRules(['required', 'min:5', "unique:categories"])
            ->updateRules(['required', 'min:5', "unique:categories,name,{{id}}"]);

        $form->hidden('slug');

        $form->saving(function (Form $form) {
            $form->slug = Str::slug($form->name);
        });

        return $form;
    }
}
