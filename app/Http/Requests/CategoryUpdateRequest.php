<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CategoryUpdateRequest extends FormRequest
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
            'name' => ['string', 'max:50']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstMessage = $validator->errors()->first();
        throw new HttpResponseException(
            response()->json([
                'data' => [
                    'errors' => [
                        ['message' => $firstMessage,]
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST)
        );
    }
}
