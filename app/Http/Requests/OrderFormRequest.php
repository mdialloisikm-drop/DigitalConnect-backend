<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_offer_id' => 'required|exists:service_offers,id',
            'requirements' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'service_offer_id.required' => 'L\'offre est requise.',
            'service_offer_id.exists' => 'L\'offre sélectionnée n\'existe pas.',
            'requirements.max' => 'Les exigences ne doivent pas dépasser 2000 caractères.',
            'attachments.array' => 'Les pièces jointes doivent être un tableau.',
            'attachments.max' => 'Vous ne pouvez joindre que 5 fichiers maximum.',
            'attachments.*.file' => 'Chaque pièce jointe doit être un fichier valide.',
            'attachments.*.mimes' => 'Les fichiers doivent être de type: pdf, doc, docx, jpg, jpeg, png, zip, rar.',
            'attachments.*.max' => 'Chaque fichier ne doit pas dépasser 10Mo.',
        ];
    }
}
