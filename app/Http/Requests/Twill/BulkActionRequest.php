<?php

namespace App\Http\Requests\Twill;

use A17\Twill\Http\Requests\Admin\Request;

class BulkActionRequest extends Request
{
    public function rulesForCreate()
    {
        return $this->rulesForUpdate();
    }

    public function rulesForUpdate()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:posts,id',
            'publish' => 'sometimes|boolean',
            'action' => 'sometimes|string|in:publish,unpublish,delete,restore',
        ];
    }

    public function rulesForBulkDelete()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer',
        ];
    }

    public function rulesForPublish()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:posts,id',
            'publish' => 'required|boolean',
        ];
    }

    public function rulesForRestore()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer',
        ];
    }

    public function rulesForReorder()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:posts,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => __('validation.required', ['attribute' => 'IDs']),
            'ids.array' => __('validation.array', ['attribute' => 'IDs']),
            'ids.min' => __('validation.min.array', ['attribute' => 'IDs', 'min' => 1]),
            'ids.*.required' => __('validation.required', ['attribute' => 'ID']),
            'ids.*.integer' => __('validation.integer', ['attribute' => 'ID']),
            'ids.*.exists' => __('validation.exists', ['attribute' => 'ID']),
            'publish.boolean' => __('validation.boolean', ['attribute' => 'publish']),
            'action.in' => __('validation.in', ['attribute' => 'action']),
        ];
    }
}
