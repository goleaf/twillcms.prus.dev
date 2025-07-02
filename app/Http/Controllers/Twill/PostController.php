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

    protected function setUpController(): void
    {
        $this->enableReorder();
        $this->enableShowImage();
    }

    public function getForm(TwillModelContract $model): Form
    {
        $form = parent::getForm($model);

        $form->add(
            Input::make()->name('title')->label('Title')->required()
        );

        $form->add(
            Input::make()->name('description')->label('Description')
        );

        $form->add(
            Wysiwyg::make()->name('content')->label('Content')
        );

        $form->add(
            Input::make()->name('excerpt_override')->label('Custom Excerpt')
        );

        $form->add(
            Medias::make()->name('cover')->label('Cover Image')
        );

        $form->add(
            Input::make()->name('featured_image_caption')->label('Image Caption')
        );

        $form->add(
            Input::make()->name('priority')->label('Priority')->type('number')
        );

        $form->add(
            Input::make()->name('view_count')->label('View Count')->type('number')
        );

        $form->add(
            Browser::make()->name('categories')->label('Categories')->modules([Category::class])
        );

        $form->add(
            Input::make()->name('meta_description')->label('Meta Description')
        );

        $form->add(
            Input::make()->name('meta_keywords')->label('Meta Keywords')
        );

        $form->add(
            BlockEditor::make()
        );

        return $form;
    }

    protected function additionalIndexTableColumns(): TableColumns
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            Text::make()->field('title')->title('Title')
        );

        $table->add(
            Text::make()->field('description')->title('Description')
        );

        $table->add(
            Text::make()->field('view_count')->title('Views')
        );

        $table->add(
            Text::make()->field('priority')->title('Priority')
        );

        return $table;
    }

    protected function formData($request)
    {
        return [
            'categories' => app()->make(\App\Repositories\CategoryRepository::class)->listAll()->toArray(),
        ];
    }

    protected function prepareFieldsBeforeCreate($fields)
    {
        // Handle meta fields
        if (isset($fields['meta_description']) || isset($fields['meta_keywords'])) {
            $fields['meta'] = [
                'description' => $fields['meta_description'] ?? '',
                'keywords' => $fields['meta_keywords'] ?? '',
            ];
            unset($fields['meta_description'], $fields['meta_keywords']);
        }

        return parent::prepareFieldsBeforeCreate($fields);
    }

    protected function prepareFieldsBeforeUpdate($object, $fields)
    {
        return $this->prepareFieldsBeforeCreate($fields);
    }

    protected function previewData($item)
    {
        $data = parent::previewData($item);
        
        // Hydrate meta fields
        if ($item->meta) {
            $data['meta_description'] = $item->meta['description'] ?? '';
            $data['meta_keywords'] = $item->meta['keywords'] ?? '';
        }

        return $data;
    }
}
