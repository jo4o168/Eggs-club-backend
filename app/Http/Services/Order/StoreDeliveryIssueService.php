<?php

namespace App\Http\Services\Order;

use App\Enum\DeliveryIssueStatus;
use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\OrderDeliveryIssue;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StoreDeliveryIssueService
{
    public function run(Order $order, array $data, User $user): OrderDeliveryIssue
    {
        $user->loadMissing('profile');
        abort_unless($user->isClient(), 403);
        abort_unless((int) $order->customer_id === (int) $user->profile->id, 403);
        abort_unless(
            (int) $order->status === OrderStatus::DELIVERED->value,
            422,
            'Registre o problema depois de receber o pedido.'
        );

        if ($order->deliveryIssue()->exists()) {
            throw ValidationException::withMessages([
                'order' => ['Este pedido já tem um chamado de pós-compra.'],
            ]);
        }

        $video = $data['video'] ?? null;
        if (! $video instanceof UploadedFile || ! $this->isVideo($video)) {
            throw ValidationException::withMessages([
                'video' => ['Envie um vídeo do kit recebido. Foto sozinha não é aceita.'],
            ]);
        }

        $photos = array_values(array_filter(
            is_array($data['photos'] ?? null) ? $data['photos'] : [],
            fn ($file) => $file instanceof UploadedFile,
        ));

        $evidence = array_merge(
            [$this->storeEvidenceFile($order, $video, 'video')],
            array_map(fn (UploadedFile $photo) => $this->storeEvidenceFile($order, $photo, 'image'), $photos),
        );

        return OrderDeliveryIssue::query()->create([
            'order_id' => $order->id,
            'customer_id' => $user->profile->id,
            'reason' => $data['reason'],
            'requested_resolution' => $data['requested_resolution'],
            'description' => trim((string) $data['description']),
            'evidence' => $evidence,
            'status' => DeliveryIssueStatus::OPEN->value,
        ]);
    }

    /**
     * @return array{url: string, mime: string, kind: string}
     */
    private function storeEvidenceFile(Order $order, UploadedFile $file, string $kind): array
    {
        $path = $file->store('delivery-issues/'.$order->id, 'public');
        $mime = (string) ($file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream');

        return [
            'url' => url(Storage::disk('public')->url($path)),
            'mime' => $mime,
            'kind' => $kind,
        ];
    }

    private function isVideo(UploadedFile $file): bool
    {
        $mime = strtolower((string) ($file->getClientMimeType() ?: $file->getMimeType() ?: ''));
        $extension = strtolower((string) $file->getClientOriginalExtension());

        return str_starts_with($mime, 'video/') || in_array($extension, ['mp4', 'webm', 'mov'], true);
    }
}
