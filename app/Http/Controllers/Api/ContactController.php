<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contact\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService
    ) {
    }

    /**
     * Envoie un message de contact
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:500'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Le nom est requis',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être une adresse email valide',
            'email.max' => 'L\'email ne peut pas dépasser 255 caractères',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'subject.required' => 'Le sujet est requis',
            'subject.max' => 'Le sujet ne peut pas dépasser 500 caractères',
            'message.required' => 'Le message est requis',
            'message.max' => 'Le message ne peut pas dépasser 5000 caractères',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Envoyer le message
            $result = $this->contactService->sendContactMessage($request->all());

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi du message.',
            ], 500);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur technique est survenue. Veuillez réessayer plus tard.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Endpoint de test pour vérifier que le service de contact fonctionne
     *
     * @return JsonResponse
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Le service de contact est opérationnel',
            'mail_configured' => config('mail.default') !== 'log',
        ], 200);
    }
}
