<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Browser;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Fields\Medias;
use A17\Twill\Services\Forms\Fields\Wysiwyg;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use App\Models\Category;

class PostController extends BaseModuleController
{
    protected $moduleName = 'posts';

    /**
     * This method can be used to enable/disable defaults. See setUpController in the docs for available options.
     */
    protected function setUpController(): void
    {
        $this->enableReorder();
        $this->enableShowImage();
    }

    /**
     * See the table builder docs for more information. If you remove this method you can use the blade files.
     * When using twill:module:make you can specify --bladeForm to use a blade form instead.
     */
    public function getForm(TwillModelContract $model): Form
    {
        $form = parent::getForm($model);

        $form->add(
            Input::make()->name('title')->label('Title')->translatable()->required()
        );

        $form->add(
            Input::make()->name('description')->label('Description')->translatable()
        );

        $form->add(
            Wysiwyg::make()->name('content')->label('Content')->translatable()
        );

        $form->add(
            Medias::make()->name('cover')->label('Cover Image')
        );

        $form->add(
            Browser::make()->name('categories')->label('Categories')->modules([Category::class])
        );

        $form->add(
            BlockEditor::make()
        );

        return $form;
    }

    /**
     * This is an example and can be removed if no modifications are needed to the table.
     */
    protected function additionalIndexTableColumns(): TableColumns
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            Text::make()->field('title')->title('Title')
        );

        $table->add(
            Text::make()->field('description')->title('Description')
        );

        return $table;
    }

    /**
     * Add anything you would like to have available in your module's form view
     */
    protected function formData($request)
    {
        return [
            'categories' => app()->make(\App\Repositories\CategoryRepository::class)->listAll()->toArray(),
        ];
    }
}
