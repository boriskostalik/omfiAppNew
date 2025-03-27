<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules()
    {
        return [
            'surname' => 'required|string|max:255',
            'von' => 'nullable|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'url' => 'nullable|url|max:255',
            'institute' => 'nullable|string|max:255',
            'specialchars' => [
                'required', 
                Rule::in([true, false, 0, 1, '0', '1'])
            ],
            'cleanname' => 'nullable|string|max:255'
        ];
    }
}