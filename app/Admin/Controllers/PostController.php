<?php

namespace App\Admin\Controllers;

use \App\Models\Post;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Category;
use Illuminate\Support\Str;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

class PostController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Post';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Post());
        $grid->column('image')->image('', '100', '100');
        $grid->title();
        $grid->column('category.name', 'Category');
        $grid->description();

        $grid->released()->bool()->filter([
            0 => 'No',
            1 => 'Yes'
        ]);

        auth()->user()->roles()->first()->slug == 'administrator'
        ? $grid
        : $grid->model()->where('user_id', auth()->id());

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
        $show = new Show(Post::findOrFail($id));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Post());

        $form->text('title');
        $form->textarea('description');
        $form->select('category_id', 'Category')
            ->options(Category::pluck('name', 'id'));
        $form->file('image')->uniqueName();

        $states = [
            'on'  => ['value' => 1, 'text' => 'Yes', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'No', 'color' => 'danger'],
        ];

        $form->switch('released', 'Published')
            ->states($states);

        $form->hidden('user_id');
        $form->hidden('slug');

        $form->saving(function (Form $form) {
            $form->user_id = auth()->id();
            $form->slug = Str::slug($form->title);
        });

        return $form;
    }

    public function show($id, Content $content)
    {
        $post = Post::where('id', $id)->first();

        abort_unless(auth()->id() == $post->user_id, 403);

        return $content
            ->title($this->title())
            ->description($this->description['show'] ?? trans('admin.show'))
            ->body($this->detail($id));
    }

     /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $post = Post::where('id', $id)->first();

        abort_unless(auth()->id() == $post->user_id, 403);

        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form()->edit($id));
    }
}
