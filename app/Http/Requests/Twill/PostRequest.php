<?php

namespace App\Http\Requests\Twill;

use A17\Twill\Http\Requests\Admin\Request;

class PostRequest extends Request
{
    public function rulesForCreate()
    {
        return [
            'title' => 'required|array',
            'title.*' => 'required|string|max:200',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'content.*' => 'nullable|string',
        ];
    }

    public function rulesForUpdate()
    {
        return $this->rulesForCreate();
    }

    public function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('admin.title')]),
            'title.*.required' => __('validation.required', ['attribute' => __('admin.title')]),
            'title.*.string' => __('validation.string', ['attribute' => __('admin.title')]),
            'title.*.max' => __('validation.max.string', ['attribute' => __('admin.title'), 'max' => 200]),
            'description.*.string' => __('validation.string', ['attribute' => __('admin.description')]),
            'description.*.max' => __('validation.max.string', ['attribute' => __('admin.description'), 'max' => 500]),
            'content.*.string' => __('validation.string', ['attribute' => __('admin.content')]),
        ];
    }
}
