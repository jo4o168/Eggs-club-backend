<?php

namespace App\Http\Services\Profile;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProfileAvatarService
{
    public function run(UploadedFile $avatar, Profile $profile): string
    {
        if ($profile->avatar_url) {
            $oldPath = $this->extractStoragePath($profile->avatar_url);
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $avatar->store('avatars', 'public');
        $url = url(Storage::disk('public')->url($path));

        $profile->avatar_url = $url;
        $profile->save();

        return $url;
    }

    private function extractStoragePath(string $url): ?string
    {
        $publicPrefix = '/storage/';
        $position = strpos($url, $publicPrefix);

        if ($position === false) {
            return null;
        }

        return substr($url, $position + strlen($publicPrefix));
    }
}
