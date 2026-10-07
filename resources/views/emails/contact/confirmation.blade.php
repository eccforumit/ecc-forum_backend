@component('mail::message')
# Merci de nous avoir contactés !

Bonjour **{{ $contactData['name'] }}**,

Nous avons bien reçu votre message concernant :

**{{ $contactData['subject'] }}**

Notre équipe du Forum ECC prendra connaissance de votre demande et vous répondra dans les plus brefs délais.

---

## Récapitulatif de votre message

**Sujet :** {{ $contactData['subject'] }}

**Message :**
{{ $contactData['message'] }}

---

Si vous avez des questions urgentes, n'hésitez pas à nous contacter directement :

- **Email :** contact@forum-ecc.ma
- **Adresse :** École Centrale Casablanca, Bouskoura

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:3000')])
Retour au site
@endcomponent

Cordialement,
**L'équipe du Forum ECC**

<small>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</small>
@endcomponent
