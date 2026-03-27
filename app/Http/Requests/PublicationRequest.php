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
            'journal'   => 'nullable|string|max:255',
            'firstpage' => 'required|string|max:10',
            'lastpage'  => 'required|string|max:10',
            'doi'       => 'nullable|string|max:255',
            'issn'      => 'required|string|max:32',
            'isbn'      => 'nullable|string|max:32',
            'bibtex_id' => 'nullable|string|max:255',
            'keywords'  => 'nullable|string',
            'abstract'  => 'nullable|string',
        ];
    }
}