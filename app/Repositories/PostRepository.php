<?php

namespace App\Repositories;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleNesting;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Post;

class PostRepository extends ModuleRepository
{
    use HandleBlocks, HandleFiles, HandleMedias, HandleNesting, HandleRevisions, HandleSlugs, HandleTranslations;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function afterSave(TwillModelContract $model, array $fields): void
    {
        // Handle category relationships
        $this->updateBrowser($model, $fields, 'categories');

        parent::afterSave($model, $fields);
    }

    public function getFormFields(TwillModelContract $object): array
    {
        $fields = parent::getFormFields($object);

        // Handle browser fields for categories
        $fields['browsers']['categories'] = $this->getFormFieldsForBrowser($object, 'categories');

        return $fields;
    }

    /**
     * Override to fix slug handling issue with missing "value" key
     */
    public function beforeSaveHandleSlugs(TwillModelContract $object, array $fields): array
    {
        if (isset($fields['slugs'])) {
            foreach ($fields['slugs'] as $locale => &$slugData) {
                if (is_array($slugData) && ! isset($slugData['value'])) {
                    // If slug is provided directly, wrap it in expected format
                    if (isset($slugData['slug'])) {
                        $slugData['value'] = $slugData['slug'];
                    } else {
                        // Generate slug from title if not provided
                        $title = $fields['title'][$locale] ?? '';
                        $slugData['value'] = str_replace(' ', '-', strtolower($title));
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Override to properly handle translation data format for Twill
     */
    public function prepareFieldsBeforeCreate(array $fields): array
    {
        // Transform the languages array to include 'value' key that Twill expects
        if (isset($fields['languages']) && is_array($fields['languages'])) {
            $transformedLanguages = [];
            foreach ($fields['languages'] as $locale => $data) {
                $transformedLanguages[] = array_merge($data, ['value' => $locale]);
            }
            $fields['languages'] = $transformedLanguages;
        }

        return parent::prepareFieldsBeforeCreate($fields);
    }

    /**
     * Override to properly handle translation data format for Twill
     */
    public function prepareFieldsBeforeUpdate(?TwillModelContract $object, array $fields): array
    {
        // Transform the languages array to include 'value' key that Twill expects
        if (isset($fields['languages']) && is_array($fields['languages'])) {
            $transformedLanguages = [];
            foreach ($fields['languages'] as $locale => $data) {
                $transformedLanguages[] = array_merge($data, ['value' => $locale]);
            }
            $fields['languages'] = $transformedLanguages;
        }

        return parent::prepareFieldsBeforeUpdate($object, $fields);
    }

    /**
     * Override to properly handle translation data format for Twill (used by update method)
     */
    public function prepareFieldsBeforeSave(?TwillModelContract $object, array $fields): array
    {
        // Transform the languages array to include 'value' key that Twill expects
        if (isset($fields['languages']) && is_array($fields['languages'])) {
            $transformedLanguages = [];
            foreach ($fields['languages'] as $locale => $data) {
                $transformedLanguages[] = array_merge($data, ['value' => $locale]);
            }
            $fields['languages'] = $transformedLanguages;
        }

        return parent::prepareFieldsBeforeSave($object, $fields);
    }
}
