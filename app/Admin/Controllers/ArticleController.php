<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\ArticleType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ArticleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Article';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article());
        $grid->title();
        $grid->sup_title();
        $grid->description();
        $grid->column('published')->bool();
        $grid->column('description', __('Content'))->display(function($val){
           return substr($val, 0, 300); 
        });
        
        $grid->column('image_path', __('Thumbnail'))->image('','100', '100')->display(function($val){
            if(empty($val)){
                return "No Thumbnal";
            }
            return $val;
        });
        $grid->model()->orderBy('created_at','desc');
        $grid->filter(function($filter){
           $filter->disableIdFilter();
           $filter->like('title', __('Title'));
           $filter->like('article.title', __('Category'));
        });
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
        $show = new Show(Article::findOrFail($id));
        

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Article());
        $form->select('type_id', "Select")->options((new ArticleType())::selectOptions());
        $form->text("title")->placeholder("Type in the article title");
        $form->text("sub_title")->placeholder("Type in the article sub title");
        $form->image('thumbnail', __('Thumbnail'));
        $form->text('description', __('Content'))->required();
        $states = [
            'on'=>['value'=>1, 'text'=>'publish', 'color'=>'success'],
            'off'=>['value'=>0, 'text'=>'draft', 'color'=>'default'],
            ];
            
            $form->UEditor('description','Description');
         $form->UEditor('description', __('Content'));
             $form->saving(function (Form $form) {
           
           dd($form->model()->image_path);
           if($form->model()->image_path){
           $ex = explode('.', $form->model()->image_path);
        
          if($ex[1]!="png"){
               
               admin_toastr("No way", 'warning');
              //return redirect('admin/articles/'.$form->model()->id.'/edit/');
          }
           }
        });
        return $form;
    }
}
