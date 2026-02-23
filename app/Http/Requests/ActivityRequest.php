<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $rules = [
      'name'        => ['required', 'string', 'max:255'],
      'banner'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
      'place'       => ['required', 'string', 'max:255'],
      'latitude'    => ['nullable', 'numeric'],
      'longitude'   => ['nullable', 'numeric'],
      'description' => ['required', 'string'],
      'annexes.*'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
    ];

    // Make picOr required only during creation (POST request)
    if ($this->isMethod('POST')) {
      $rules['banner'] = ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'];
    }

    return $rules;
  }

  public function attributes(): array
  {
    return [
      'name'   => __('Activity name'),
      'banner'    => __('Activity Banner'),
      'place'  => __('Activity place'),
      'description' => __('Activity description')
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => __('Activity name is required'),
      'place.required' => __('Activity place is required'),
      'description.required' => __('Activity description is required'),
    ];
  }
}
