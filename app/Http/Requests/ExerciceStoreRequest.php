<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExerciceStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name'      => ['required', 'string', 'max:255', 'unique:exercices,name'],
      'logo'      => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
      'slogan'    => ['required', 'string', 'max:255'],
      'year'      => ['required', 'string', 'max:255', 'unique:exercices,year'],
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
      'logo.required' => __('Mandate logo is required'),
    ];
  }
}
