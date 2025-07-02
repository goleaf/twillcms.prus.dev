<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Fields\Select;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use App\Models\Category;

class CategoryController extends BaseModuleController
{
    protected $moduleName = 'categories';

    protected function setUpController(): void
    {
        $this->enableReorder();
    }

    public function getForm(TwillModelContract $model): Form
    {
        $form = parent::getForm($model);

        $form->add(
            Input::make()->name('title')->label('Category Title')->required()
        );

        $form->add(
            Input::make()->name('description')->label('Description')
        );

        $form->add(
            Select::make()
                ->name('parent_id')
                ->label('Parent Category')
                ->placeholder('Select parent category (optional)')
                ->options($this->getParentCategories($model))
        );

        $form->add(
            Input::make()->name('color_code')->label('Category Color')
        );

        $form->add(
            Input::make()->name('icon')->label('Category Icon')
        );

        $form->add(
            Input::make()->name('sort_order')->label('Sort Order')->type('number')
        );

        $form->add(
            Input::make()->name('view_count')->label('View Count')->type('number')
        );

        $form->add(
            Input::make()->name('meta_description')->label('Meta Description')
        );

        $form->add(
            Input::make()->name('meta_keywords')->label('Meta Keywords')
        );

        return $form;
    }

    protected function additionalIndexTableColumns(): TableColumns
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            Text::make()->field('title')->title('Category')
        );

        $table->add(
            Text::make()->field('description')->title('Description')
        );

        $table->add(
            Text::make()->field('posts_count')->title('Posts')
        );

        $table->add(
            Text::make()->field('view_count')->title('Views')
        );

        return $table;
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

    protected function getParentCategories($model = null)
    {
        $query = Category::where('published', true);
        
        // If editing, exclude self to prevent circular references
        if ($model && $model->exists) {
            $query->where('id', '!=', $model->id);
        }
        
        return $query->pluck('title', 'id')->toArray();
    }
}
