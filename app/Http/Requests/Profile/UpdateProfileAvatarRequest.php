<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;

class UpdateProfileAvatarRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'file',
                'max:5120',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!$value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('O arquivo enviado é inválido.');
                        return;
                    }

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg', 'heic', 'heif', 'avif', 'jfif'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = strtolower((string) $value->getMimeType());

                    $isAllowedExtension = in_array($extension, $allowedExtensions, true);
                    $isImageMime = str_starts_with($mimeType, 'image/');

                    if (!$isAllowedExtension && !$isImageMime) {
                        $fail('O avatar deve ser uma imagem válida.');
                    }
                },
            ],
        ];
    }
}
