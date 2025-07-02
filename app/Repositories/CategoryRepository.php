<?php

namespace App\Repositories;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Category;

class CategoryRepository extends ModuleRepository
{
    use HandleRevisions, HandleSlugs, HandleTranslations;

    public function __construct(Category $model)
    {
        $this->model = $model;
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
                        // Generate slug from name if not provided
                        $name = $fields['name'][$locale] ?? '';
                        $slugData['value'] = str_replace(' ', '-', strtolower($name));
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
