<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExerciceUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name'      => ['required', 'string', 'max:255'],
      'logo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
      'slogan'    => ['required', 'string', 'max:255'],
      'year'      => ['required', 'string', 'max:255'],
    ];
  }

  public function attributes(): array
  {
    return [
      'name'    => __('Mandate Name'),
      'logo'    => __('Mandate Logo'),
      'slogan'  => __('Mandate Slogan'),
      'year'    => __('Mandate Year'),
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => __('Mandate name is required'),
      'name.unique' => __('This mandate name is already taken'),
      'year.required' => __('Mandate year is required'),
      'year.unique' => __('A mandate already exist for this year'),
    ];
  }
}
