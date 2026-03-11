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
            'entered_by'   => 'required|integer',
            'issue_id'     => 'required|exists:issues,id',
            'type'         => 'required|in:Article,Book,Booklet,Inbook,Incollection,Inproceedings,Manual,Mastersthesis,Misc,Phdthesis,Proceedings,Techreport,Unpublished',
            'title'        => 'required|string',
            'title_eng'    => 'nullable|string',
            'actualyear'   => 'nullable|string|max:12',
            'month'        => 'nullable|string|max:20',
            'journal'      => 'nullable|string|max:255',
            'booktitle'    => 'nullable|string|max:255',
            'publisher'    => 'nullable|string|max:127',
            'series'       => 'nullable|string|max:64',
            'edition'      => 'nullable|string|max:255',
            'chapter'      => 'nullable|string|max:10',
            'institution'  => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'school'       => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:127',
            'howpublished' => 'nullable|string|max:255',
            'firstpage'    => 'required|string|max:10',
            'lastpage'     => 'required|string|max:10',
            'doi'          => 'nullable|string|max:255',
            'url'          => 'nullable|string|max:255',
            'issn'         => 'required|string|max:32',
            'isbn'         => 'nullable|string|max:32',
            'mesc'         => 'nullable|string|max:50',
            'bibtex_id'    => 'nullable|string|max:255',
            'namekey'      => 'nullable|string|max:255',
            'crossref'     => 'nullable|string|max:255',
            'keywords'     => 'nullable|string',
            'note'         => 'nullable|string',
            'abstract'     => 'nullable|string',
        ];
    }
}