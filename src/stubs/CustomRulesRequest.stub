<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CustomRulesRequest extends FormRequest
{
    /**
     * This method is the core of this class. It will call the other methods dynamically
     *
     * @return  Array
     */
    public function rules(): Array
    {
        $method = "validateTo" . Str::ucfirst($this->route()->getActionMethod());

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $method = "authorizeTo" . Str::ucfirst($this->route()->getActionMethod());

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return true;
    }

    /**
     * Configuration for exception handler
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
