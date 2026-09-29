@component('mail::message')
# Nouveau message de contact

Vous avez reçu un nouveau message depuis le formulaire de contact du site Forum ECC.

## Informations de l'expéditeur

**Nom :** {{ $contactData['name'] }}
**Email :** {{ $contactData['email'] }}
@if($contactData['phone'])
**Téléphone :** {{ $contactData['phone'] }}
@endif

---

## Sujet
{{ $contactData['subject'] }}

---

## Message

{{ $contactData['message'] }}

---

<small>Message envoyé le {{ $contactData['sent_at']->format('d/m/Y à H:i') }}</small>

@component('mail::button', ['url' => 'mailto:' . $contactData['email']])
Répondre au contact
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
