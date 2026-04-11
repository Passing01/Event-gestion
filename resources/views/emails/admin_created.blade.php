<x-mail::message>
# Bonjour {{ $user->name }},

Votre compte administrateur sur la plateforme a été créé avec succès.

Voici vos identifiants de connexion :
- **Email :** {{ $user->email }}
- **Mot de passe :** {{ $password }}

Vous pouvez vous connecter en cliquant sur le bouton ci-dessous :

<x-mail::button :url="config('app.url') . '/login'">
Se connecter
</x-mail::button>

Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
