<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgendaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'banner' => 'nullable|image|max:2048',
            'begin_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:begin_at',
            'type' => 'required|in:project,activity',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('Even Title is required'),
            'begin_at.required' => __('Even Begin at is required'),
            'end_at.required' => __('Even End Date is required'),
            'type.required' => __('Even Type is required'),
            'after_or_equal' => __('Even End Date must be after or equal to Even Begin at'),
        ];
    }
}
