<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UserLoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ];
    }

    public function authenticate(): void
    {
        if (!auth()->attempt($this->only('email', 'password'))) {
            throw new HttpResponseException(response()->json([
                'data' => [
                    'errors' => [
                        ["message" => 'email or password invalid']
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST));
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $firstMessage = array_values($validator->errors()->toArray())[0][0];
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
