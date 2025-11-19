<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'message_type' => 'required|in:text,voice,file',
            'content' => 'required_if:message_type,text|string|max:5000',
            'voice_file' => 'required_if:message_type,voice|file|mimes:mp3,wav,ogg,m4a,webm|max:10240',
            'file' => 'required_if:message_type,file|file|max:20480',
        ];
    }
}
