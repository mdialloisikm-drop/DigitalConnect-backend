<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalFormRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'cover_letter' => 'required|string|min:100',
            'proposed_amount' => 'required|numeric|min:0',
            'proposed_duration' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Le projet est requis.',
            'project_id.exists' => 'Le projet sélectionné n\'existe pas.',
            'cover_letter.required' => 'La lettre de motivation est requise.',
            'cover_letter.min' => 'La lettre de motivation doit contenir au moins 100 caractères.',
            'proposed_amount.required' => 'Le montant proposé est requis.',
            'proposed_amount.numeric' => 'Le montant proposé doit être un nombre.',
            'proposed_amount.min' => 'Le montant proposé doit être positif.',
            'proposed_duration.required' => 'La durée proposée est requise.',
            'proposed_duration.integer' => 'La durée proposée doit être un nombre entier.',
            'proposed_duration.min' => 'La durée proposée doit être au minimum 1 jour.',
            'attachments.array' => 'Les pièces jointes doivent être un tableau.',
            'attachments.max' => 'Vous ne pouvez joindre que 5 fichiers maximum.',
            'attachments.*.file' => 'Chaque pièce jointe doit être un fichier valide.',
            'attachments.*.mimes' => 'Les fichiers doivent être de type: pdf, doc, docx, jpg, jpeg, png.',
            'attachments.*.max' => 'Chaque fichier ne doit pas dépasser 5Mo.',
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // S'assurer que project_id est présent dans l'URL ou le body
        if ($this->route('project_id')) {
            $this->merge([
                'project_id' => $this->route('project_id')
            ]);
        }
    }

}
