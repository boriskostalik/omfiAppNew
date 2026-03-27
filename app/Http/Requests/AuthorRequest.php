<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
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
            'cleanname' => 'nullable|string|max:255'
        ];
    }
}