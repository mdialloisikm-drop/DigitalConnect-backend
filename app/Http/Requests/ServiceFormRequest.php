<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceFormRequest extends FormRequest
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
            'description' => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
            // ✅ Le freelance ne peut plus définir le statut
            // 'status' => 'nullable|in:published,archived',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            // Validation des offres : soit 1 offre, soit 3 offres
            'offers' => 'required|array|min:1|max:3',
            'offers.*.title' => 'required|in:Starter,Standard,Advanced',
            'offers.*.delivery_days' => 'required|integer|min:1',
            'offers.*.number_of_revisions' => 'nullable|integer|min:0',
            'offers.*.price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Validation personnalisée : Vérifier la cohérence des offres
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $offers = $this->input('offers', []);
            $offerCount = count($offers);

            // Vérifier qu'il y a soit 1 offre, soit 3 offres
            if ($offerCount !== 1 && $offerCount !== 3) {
                $validator->errors()->add('offers', 'Vous devez fournir soit 1 offre, soit 3 offres (Starter, Standard, Advanced).');
            }

            // Si 3 offres, vérifier qu'elles ont les bons titres
            if ($offerCount === 3) {
                $titles = array_column($offers, 'title');
                $expectedTitles = ['Starter', 'Standard', 'Advanced'];

                if (array_diff($expectedTitles, $titles) || array_diff($titles, $expectedTitles)) {
                    $validator->errors()->add('offers', 'Pour 3 offres, vous devez fournir Starter, Standard et Advanced.');
                }
            }

            // Vérifier l'unicité des titres
            $titles = array_column($offers, 'title');
            if (count($titles) !== count(array_unique($titles))) {
                $validator->errors()->add('offers', 'Les titres des offres doivent être uniques.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'description.required' => 'La description est requise.',
            'categorie_id.required' => 'La catégorie est requise.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'images.array' => 'Les images doivent être un tableau.',
            'images.*.image' => 'Chaque fichier doit être une image.',
            'images.*.mimes' => 'Les images doivent être de type: jpeg, png, jpg ou gif.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 2Mo.',

            'offers.required' => 'Au moins une offre est requise.',
            'offers.array' => 'Les offres doivent être un tableau.',
            'offers.min' => 'Vous devez fournir au moins 1 offre.',
            'offers.max' => 'Vous ne pouvez pas fournir plus de 3 offres.',
            'offers.*.title.required' => 'Le titre de l\'offre est requis.',
            'offers.*.title.in' => 'Le titre doit être: Starter, Standard ou Advanced.',
            'offers.*.delivery_days.required' => 'Le délai de livraison est requis.',
            'offers.*.delivery_days.integer' => 'Le délai de livraison doit être un nombre entier.',
            'offers.*.delivery_days.min' => 'Le délai de livraison doit être au minimum 1 jour.',
            'offers.*.number_of_revisions.integer' => 'Le nombre de révisions doit être un nombre entier.',
            'offers.*.number_of_revisions.min' => 'Le nombre de révisions doit être positif.',
            'offers.*.price.required' => 'Le prix est requis.',
            'offers.*.price.numeric' => 'Le prix doit être un nombre.',
            'offers.*.price.min' => 'Le prix doit être positif.',
        ];
    }
}
