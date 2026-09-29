<x-mail::message>
# Bonjour {{ $user->first_name ?? $user->email }},

Nous avons reçu une demande de réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.

<x-mail::button :url="$resetUrl">
Réinitialiser mon mot de passe
</x-mail::button>

Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
<br>
<a href="{{ $resetUrl }}">{{ $resetUrl }}</a>

Ce lien expirera dans 24 heures. Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
