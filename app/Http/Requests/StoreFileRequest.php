<?php

namespace App\Http\Requests;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files.*' => [
                'required',
                'file',
                function($attribute, $value, $fail){
                    /** @var $value \Illuminate\Http\UploadedFile */
                    $file = File::query()->where('name', $value->getClientOriginalname())
                    ->where('created_by', Auth::id())
                    ->where('parent_id', $this->parent_id)
                    ->whereNull('deleted_at')
                    ->exists();

                    if($file){
                        $fail('File "'.$value->getClienteOriginalName() . '" already exists!');
                    }
                }
            ]
        ];
    }
}
