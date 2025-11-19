<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectFormRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after:today',
            //'status' => 'nullable|in:open,in_progress,completed,cancelled,en_attente',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max par fichier
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
            'category_id.required' => 'La catégorie est requise.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'title.required' => 'Le titre est requis.',
            'description.required' => 'La description est requise.',
            'budget.numeric' => 'Le budget doit être un nombre.',
            'budget.min' => 'Le budget doit être positif.',
            'duration.required' => 'La durée du projet est requise.',
            'duration.integer' => 'La durée doit être un nombre entier.',
            'duration.min' => 'La durée doit être au minimum 1 jour.',
            'deadline.date' => 'La date limite doit être une date valide.',
            'deadline.after' => 'La date limite doit être dans le futur.',
            'status.in' => 'Le statut doit être: open, in_progress, completed, en_attente ou cancelled.',
            'skills.array' => 'Les compétences doivent être un tableau.',
            'skills.*.exists' => 'Une ou plusieurs compétences sélectionnées n\'existent pas.',
            'attachments.array' => 'Les pièces jointes doivent être un tableau.',
            'attachments.*.file' => 'Chaque pièce jointe doit être un fichier valide.',
            'attachments.*.max' => 'Chaque fichier ne doit pas dépasser 10 MB.',
        ];
    }
}
