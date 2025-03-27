<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entered_by' => 'required|integer',
            'year' => 'required|string|max:12',
            'actualyear' => 'required|string|max:12',
            'title' => 'required|string',
            'title_eng' => 'nullable|string',
            'mesc' => 'nullable|string|max:50',
            'bibtex_id' => 'nullable|string|max:255',
            'pub_type' => 'nullable|string|max:255',
            'type' => 'nullable|in:Article,Book,Booklet,Inbook,Incollection,Inproceedings,Manual,Mastersthesis,Misc,Phdthesis,Proceedings,Techreport,Unpublished',
            'survey' => 'integer|min:0|max:1',
            'mark' => 'integer',
            'journal' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
            'url' => 'nullable|max:255',
        ];
    }
}