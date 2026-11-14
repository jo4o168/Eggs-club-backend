<?php

namespace App\Http\Requests\Order;

use App\Enum\DeliveryIssueReason;
use App\Enum\DeliveryIssueRequest;
use App\Http\Requests\BaseRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDeliveryIssueRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', Rule::enum(DeliveryIssueReason::class)],
            'requested_resolution' => ['required', 'string', Rule::enum(DeliveryIssueRequest::class)],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
            'video' => ['required', 'file', 'max:20480', 'mimes:mp4,webm,mov'],
            'photos' => ['sometimes', 'array', 'max:3'],
            'photos.*' => ['file', 'max:20480', 'mimes:jpeg,jpg,png,webp'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => 'motivo',
            'requested_resolution' => 'o que você prefere',
            'description' => 'descrição',
            'video' => 'vídeo',
            'photos' => 'fotos',
            'photos.*' => 'foto',
        ];
    }

    public function messages(): array
    {
        return [
            'requested_resolution.required' => 'Informe se prefere estorno ou o envio de outro kit.',
            'video.required' => 'Envie um vídeo do kit recebido. Foto sozinha não é aceita.',
            'video.uploaded' => 'O vídeo não chegou no servidor. Use MP4, WEBM ou MOV de até 20 MB.',
            'video.max' => 'O vídeo precisa ter no máximo 20 MB.',
            'video.mimes' => 'O vídeo precisa ser MP4, WEBM ou MOV.',
            'photos.*.mimes' => 'As fotos precisam ser JPEG, PNG ou WEBP.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $video = $this->file('video');
            if ($video instanceof UploadedFile && ! $this->isVideo($video)) {
                $validator->errors()->add('video', 'Envie um vídeo do kit recebido. Foto sozinha não é aceita.');
            }
        });
    }

    private function isVideo(UploadedFile $file): bool
    {
        $mime = strtolower((string) ($file->getClientMimeType() ?: $file->getMimeType() ?: ''));
        $extension = strtolower((string) $file->getClientOriginalExtension());

        return str_starts_with($mime, 'video/') || in_array($extension, ['mp4', 'webm', 'mov'], true);
    }
}
