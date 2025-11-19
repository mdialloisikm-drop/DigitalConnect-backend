<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectTaskFormRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:basse,moyenne,haute,urgente',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la tâche est requis.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'order.integer' => 'L\'ordre doit être un nombre entier.',
            'order.min' => 'L\'ordre doit être positif.',
            'status.in' => 'Le statut doit être: pending, in_progress ou completed.',
            'priority.in' => 'La priorité doit être: basse, moyenne, haute ou urgente.',
        ];
    }
}
