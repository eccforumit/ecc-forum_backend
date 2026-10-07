<?php

namespace App\Services\Contact;

use App\Mail\ContactMail;
use App\Mail\ContactConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Traite et envoie un message de contact
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function sendContactMessage(array $data): bool
    {
        try {
            // Valider les données
            $this->validateContactData($data);

            // Préparer les données pour l'email
            $emailData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'],
                'message' => $data['message'],
                'sent_at' => now(),
            ];

            // Envoyer l'email à l'équipe du Forum ECC
            try {
                Log::info('📧 Sending contact email', [
                    'to' => config('mail.contact_email', 'contact@forum-ecc.ma'),
                    'from_user' => $emailData['email'],
                    'from_name' => $emailData['name'],
                    'subject' => $emailData['subject'],
                    'smtp_from' => config('mail.from.address'),
                ]);

                Mail::to(config('mail.contact_email', 'contact@forum-ecc.ma'))
                    ->send(new ContactMail($emailData));

                Log::info('✅ Email sent successfully via SMTP', [
                    'to' => config('mail.contact_email', 'contact@forum-ecc.ma'),
                    'user_email' => $emailData['email'],
                    'user_name' => $emailData['name'],
                ]);

                // Envoyer un email de confirmation à l'utilisateur
                try {
                    Log::info('📧 Sending confirmation email to user', [
                        'to' => $emailData['email'],
                        'name' => $emailData['name'],
                    ]);

                    Mail::to($emailData['email'])
                        ->send(new ContactConfirmationMail($emailData));

                    Log::info('✅ Confirmation email sent to user', [
                        'to' => $emailData['email'],
                    ]);
                } catch (\Exception $confirmException) {
                    // Si l'envoi de la confirmation échoue, on log mais on ne bloque pas
                    Log::warning('⚠️ Failed to send confirmation email to user', [
                        'error' => $confirmException->getMessage(),
                        'user_email' => $emailData['email'],
                    ]);
                }
            } catch (\Exception $mailException) {
                $errorMessage = $mailException->getMessage();

                Log::error('❌ SMTP error during contact message send', [
                    'error' => $errorMessage,
                    'user_email' => $emailData['email'],
                    'user_name' => $emailData['name'],
                    'subject' => $emailData['subject'],
                ]);

                throw new \Exception(
                    'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.'
                );
            }

            // Log de succès
            Log::info('Contact message sent successfully', [
                'from' => $data['email'],
                'subject' => $data['subject'],
            ]);

            return true;
        } catch (\Exception $e) {
            // Log de l'erreur
            Log::error('Failed to send contact message', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Valide les données du formulaire de contact
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateContactData(array $data): void
    {
        $required = ['name', 'email', 'subject', 'message'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Le champ {$field} est requis");
            }
        }

        // Valider le format de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Format d'email invalide");
        }

        // Valider la longueur des champs
        if (strlen($data['name']) > 255) {
            throw new \InvalidArgumentException("Le nom est trop long");
        }

        if (strlen($data['subject']) > 500) {
            throw new \InvalidArgumentException("Le sujet est trop long");
        }

        if (strlen($data['message']) > 5000) {
            throw new \InvalidArgumentException("Le message est trop long");
        }
    }
}
