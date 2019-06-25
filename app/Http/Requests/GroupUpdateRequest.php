<?php

namespace App\Http\Requests;

use Auth;
use App\Exception\NotAuthorizedException;
use Illuminate\Foundation\Http\FormRequest;

class GroupUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user()->isAdmin()){
            return true;
        } else {
            throw new NotAuthorizedException();
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:255',
            'page' => 'integer|min:0',
        ];
    }
}