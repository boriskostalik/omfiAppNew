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
            'issue_id'  => 'required|exists:issues,id',
            'type'      => 'required|in:Article,Book,Booklet,Inbook,Incollection,Inproceedings,Manual,Mastersthesis,Misc,Phdthesis,Proceedings,Techreport,Unpublished',
            'title'     => 'required|string',
            'title_eng' => 'nullable|string',
            'actualyear'=> 'nullable|string|max:12',
            'firstpage' => 'required|string|max:10',
            'lastpage'  => 'required|string|max:10',
            'doi'       => 'nullable|string|max:255',
            'isbn'      => 'nullable|string|max:32',
            'keywords'  => 'nullable|array',
            'keywords.*' => 'string|max:255',
            'abstract'  => 'nullable|string',
        ];
    }
}