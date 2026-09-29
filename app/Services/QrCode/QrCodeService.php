<?php

namespace App\Services\QrCode;

use App\Models\StudentProfile;
use Illuminate\Support\Str;

class QrCodeService
{
    public function generateProfileQrCode(StudentProfile $profile): array
    {
        $profileUrl = $this->generateProfileUrl($profile);
        $qrCodeUrl = $this->generateQrCodeUrl($profileUrl);

        return [
            'profile_url' => $profileUrl,
            'qr_code_url' => $qrCodeUrl,
            'qr_code_png' => $this->generateBase64QrCode($profileUrl),
            'student_name' => $profile->full_name,
            'cv_available' => !empty($profile->cv_file_url)
        ];
    }

    public function generateCvQrCode(StudentProfile $profile): array
    {
        if (empty($profile->cv_file_url)) {
            throw new \Exception('Aucun CV disponible pour ce profil');
        }

        $qrCodeUrl = $this->generateQrCodeUrl($profile->cv_file_url);

        return [
            'cv_url' => $profile->cv_file_url,
            'qr_code_url' => $qrCodeUrl,
            'qr_code_png' => $this->generateBase64QrCode($profile->cv_file_url),
            'student_name' => $profile->full_name
        ];
    }

    private function generateProfileUrl(StudentProfile $profile): string
    {
        $baseUrl = config('app.frontend_url', 'http://localhost:3000');
        return $baseUrl . '/profile/' . $profile->id;
    }

    private function generateQrCodeUrl(string $data): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($data);
    }

    private function generateBase64QrCode(string $data): string
    {
        $qrUrl = $this->generateQrCodeUrl($data);
        $imageData = @file_get_contents($qrUrl);

        if ($imageData === false) {
            throw new \Exception('Impossible de générer le QR code');
        }

        return base64_encode($imageData);
    }

    public function generateQrCodeFromUrl(string $url): string
    {
        return $this->generateQrCodeUrl($url);
    }
}
