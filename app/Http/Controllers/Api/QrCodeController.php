<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QrCodeRequest;
use App\Services\QrCode\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function __construct(private readonly QrCodeService $qrCodeService)
    {
    }

    public function generateMyQrCode(QrCodeRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isStudent()) {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les étudiants peuvent générer des QR codes'
            ], 403);
        }

        $profile = $user->studentProfile;
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil étudiant non trouvé'
            ], 404);
        }

        try {
            $type = $request->validated()['type'];

            if ($type === 'cv') {
                $qrData = $this->qrCodeService->generateCvQrCode($profile);
            } else {
                $qrData = $this->qrCodeService->generateProfileQrCode($profile);
            }

            return response()->json([
                'success' => true,
                'data' => $qrData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function generateStudentQrCode(QrCodeRequest $request, string $studentId): JsonResponse
    {
        $user = $request->user();

        if (!$user->isStudent()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $profile = \App\Models\StudentProfile::find($studentId);
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil étudiant non trouvé'
            ], 404);
        }

        try {
            $type = $request->validated()['type'];

            if ($type === 'cv') {
                $qrData = $this->qrCodeService->generateCvQrCode($profile);
            } else {
                $qrData = $this->qrCodeService->generateProfileQrCode($profile);
            }

            return response()->json([
                'success' => true,
                'data' => $qrData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getAllEccQrCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isCompany()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $profiles = \App\Models\StudentProfile::where('school', 'ECC')
                ->whereNotNull('cv_file_url')
                ->get();

            $groupedData = [
                'Ingenieur' => [],
                'Bachelor' => []
            ];

            foreach ($profiles as $profile) {
                $major = $profile->major;
                $year = $profile->school_year;

                if (!isset($groupedData[$major])) {
                    continue;
                }

                if (!isset($groupedData[$major][$year])) {
                    $groupedData[$major][$year] = [];
                }

                // Générer QR code depuis cv_file_url
                $qrCodeUrl = $this->qrCodeService->generateQrCodeFromUrl($profile->cv_file_url);

                $groupedData[$major][$year][] = [
                    'id' => $profile->id,
                    'first_name' => $profile->first_name,
                    'last_name' => $profile->last_name,
                    'qr_code_url' => $qrCodeUrl,
                    'cv_file_url' => $profile->cv_file_url,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $groupedData,
                'total' => $profiles->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
