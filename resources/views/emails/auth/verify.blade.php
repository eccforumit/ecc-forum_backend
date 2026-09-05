<x-mail::message>
# Bonjour {{ $user->first_name ?? $user->email }},

Merci de vous être inscrit sur le Forum ECC ! Pour activer votre compte, cliquez sur le bouton ci-dessous.

<x-mail::button :url="$verificationUrl">
Vérifier mon adresse email
</x-mail::button>

Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
<br>
<a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>

Ce lien expirera dans 24 heures. Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.

À très vite sur {{ $frontendUrl }} !

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
