<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre compte - Event Q&A</title>
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
        .button { background-color: #7c3aed; color: #ffffff !important; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; display: inline-block; transition: background-color 0.2s; }
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
                    <h1>Confirmez votre adresse e-mail</h1>
                    <p>Bienvenue parmi nous ! Nous sommes ravis de vous compter parmi les organisateurs d'Event Q&A.</p>
                    <p>Pour activer votre compte et commencer à créer vos événements interactifs, merci de cliquer sur le bouton ci-dessous :</p>
                    
                    <div class="button-container">
                        <a href="{{ $url }}" class="button">Vérifier mon adresse e-mail</a>
                    </div>
                    
                    <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :</p>
                    <p class="small" style="word-break: break-all; color: #7c3aed;">{{ $url }}</p>
                    
                    <div class="divider"></div>
                    
                    <p class="small">Si vous n'avez pas créé de compte sur Event Q&A, vous pouvez ignorer cet e-mail en toute sécurité.</p>
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
