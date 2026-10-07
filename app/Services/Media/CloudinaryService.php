<?php

namespace App\Services\Media;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CloudinaryService
{
    private $uploadApi;

    public function __construct()
    {
        // Configuration directe de Cloudinary
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME')),
                'api_key' => config('services.cloudinary.api_key', env('CLOUDINARY_API_KEY')),
                'api_secret' => config('services.cloudinary.api_secret', env('CLOUDINARY_API_SECRET')),
            ],
        ]);

        $this->uploadApi = new UploadApi();
    }

    public function upload(UploadedFile $file, string $folder, string $resourceType = 'auto'): array
    {
        try {
            $folderPath = trim($folder, '/');
            $extension = $file->getClientOriginalExtension();
            $publicId = Str::uuid()->toString();

            $uploadOptions = [
                'folder' => $folderPath,
                'resource_type' => $resourceType,
                'public_id' => $publicId,
                'overwrite' => true,
            ];

            if ($extension && in_array(strtolower($extension), ['pdf', 'doc', 'docx'])) {
                $uploadOptions['format'] = strtolower($extension);
            }

            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);

            return [
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
            ];
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            throw new \Exception('Échec de l\'upload du fichier. Veuillez réessayer.');
        }
    }

    public function delete(?string $publicId): void
    {
        if (!$publicId) {
            return;
        }

        try {
            $this->uploadApi->destroy($publicId, ['invalidate' => true]);
        } catch (\Exception $e) {
            \Log::warning('Cloudinary delete failed: ' . $e->getMessage());
        }
    }
}
