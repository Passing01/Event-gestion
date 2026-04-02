<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Event Q&A</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #1e293b; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); padding: 48px 40px; text-align: center; color: #ffffff; }
        .logo { background: rgba(255, 255, 255, 0.2); width: 48px; height: 48px; border-radius: 12px; display: inline-block; line-height: 48px; font-weight: 800; font-size: 18px; margin-bottom: 16px; }
        .content { padding: 48px 40px; line-height: 1.6; }
        h1 { margin: 0 0 16px; font-size: 24px; font-weight: 800; color: #0f172a; }
        p { margin: 0 0 24px; font-size: 16px; color: #475569; }
        .button-container { text-align: center; margin: 32px 0; }
        .button { background-color: #7c3aed; color: #ffffff !important; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; display: inline-block; }
        .warning-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
        .warning-box p { margin: 0; font-size: 14px; color: #92400e; }
        .footer { text-align: center; padding: 32px 40px; font-size: 14px; color: #94a3b8; }
        .divider { border-top: 1px solid #e2e8f0; margin: 32px 0; }
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
                    <h1>Réinitialisation de votre mot de passe</h1>
                    <p>Bonjour <strong>{{ $userName }}</strong>,</p>
                    <p>Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte <strong>{{ $appName }}</strong>.</p>
                    <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>

                    <div class="button-container">
                        <a href="{{ $resetUrl }}" class="button">Réinitialiser mon mot de passe</a>
                    </div>

                    <div class="warning-box">
                        <p>⏱ <strong>Ce lien expire dans 60 minutes.</strong> Passé ce délai, vous devrez effectuer une nouvelle demande.</p>
                    </div>

                    <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
                    <p class="small" style="word-break: break-all; color: #7c3aed;">{{ $resetUrl }}</p>

                    <div class="divider"></div>

                    <p class="small">Si vous n'avez pas demandé de réinitialisation de mot de passe, vous pouvez ignorer cet e-mail. Votre mot de passe restera inchangé.</p>
                </td>
            </tr>
        </table>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Event Q&A. Tous droits réservés.</p>
            <p>Développé pour des échanges plus humains.</p>
        </div>
    </div>
</body>
</html>
