<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos accès Panéliste - Event Q&A</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #1e293b; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); padding: 48px 40px; text-align: center; color: #ffffff; }
        .logo { background: rgba(255, 255, 255, 0.2); width: 48px; height: 48px; border-radius: 12px; display: inline-block; line-height: 48px; font-weight: 800; font-size: 18px; margin-bottom: 16px; }
        .content { padding: 48px 40px; line-height: 1.6; }
        .access-box { background: #f1f5f9; border-radius: 12px; padding: 24px; margin: 24px 0; border: 1px solid #e2e8f0; }
        .access-item { display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; }
        h1 { margin: 0 0 16px; font-size: 24px; font-weight: 800; color: #0f172a; }
        p { margin: 0 0 24px; font-size: 16px; color: #475569; }
        .button-container { text-align: center; margin: 32px 0; }
        .button { background-color: #7c3aed; color: #ffffff !important; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; display: inline-block; }
        .footer { text-align: center; padding: 32px 40px; font-size: 14px; color: #94a3b8; }
        .small { font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <div class="logo">Q&A</div>
                    <h2 style="margin:0; font-size: 20px; font-weight: 600; opacity: 0.9;">Event Q&A</h2>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h1>Bienvenue parmi les panélistes !</h1>
                    <p>Bonjour <strong>{{ $userName }}</strong>,</p>
                    <p>Vous avez été ajouté en tant que panéliste pour l'événement <strong>{{ $eventName }}</strong> sur <strong>{{ $appName }}</strong>.</p>
                    
                    <p>Voici vos identifiants pour vous connecter à votre espace dédié :</p>
                    
                    <div class="access-box">
                        <div class="access-item">
                            <span style="color:#64748b;">Email :</span>
                            <span style="font-weight:600;">{{ $userEmail }}</span>
                        </div>
                        <div class="access-item" style="border:none; margin:0; padding:0;">
                            <span style="color:#64748b;">Mot de passe :</span>
                            <span style="font-weight:600; color:#7c3aed;">{{ $password }}</span>
                        </div>
                    </div>

                    <div class="button-container">
                        <a href="{{ $loginUrl }}" class="button">Se connecter maintenant</a>
                    </div>

                    <p style="font-size:14px; color:#64748b;">Par sécurité, nous vous conseillons de changer votre mot de passe dès votre première connexion dans votre profil.</p>
                </td>
            </tr>
        </table>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Event Q&A. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
