<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Event Q&A — Interagissez en direct')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;500;700&display=swap">
    <style>
        :root {
            --brand: #7c3aed;
            --brand-light: #f5f3ff;
            --brand-dark: #5b21b6;
            --foreground: #0f172a;
            --muted-foreground: #64748b;
            --background: #ffffff;
            --glass: rgba(255, 255, 255, 0.7);
            --border: #e2e8f0;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--foreground);
            background: var(--background);
            line-height: 1.5;
            overflow-x: hidden;
        }

        h1, h2, h3, .brand-font { font-family: 'Outfit', sans-serif; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* --- Header --- */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5rem;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            z-index: 100;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        header.scrolled {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--brand);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-box {
            width: 2.5rem;
            height: 2.5rem;
            background: var(--brand);
            border-radius: 0.75rem;
            display: grid;
            place-items: center;
            color: #fff;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--foreground);
            font-weight: 500;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--brand); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--foreground);
        }

        .btn-outline:hover {
            background: var(--brand-light);
            border-color: var(--brand);
        }

        .mobile-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 101;
            padding: 0.5rem;
        }

        .mobile-toggle span {
            display: block;
            width: 25px;
            height: 2px;
            background: var(--foreground);
            transition: 0.3s;
        }

        /* --- Sections common --- */
        section { padding: 6rem 0; }
        
        .section-header {
            text-align: center;
            max-width: 40rem;
            margin: 0 auto 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .section-header p {
            color: var(--muted-foreground);
            font-size: 1.125rem;
        }

        /* --- Footer --- */
        footer {
            padding: 4rem 0;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--muted-foreground);
            font-size: 0.875rem;
        }

        @media (max-width: 968px) {
            .nav-inner { padding: 0 0.5rem; }
            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                height: 100vh;
                background: #fff;
                flex-direction: column;
                justify-content: center;
                gap: 2rem;
                padding: 2rem;
                align-items: center;
                transition: 0.4s;
                box-shadow: -10px 0 30px rgba(0,0,0,0.1);
                z-index: 100;
            }
            .nav-links.active { right: 0; }
            .mobile-toggle { display: flex; }
            section { padding: 4rem 0; }
        }

        @media (max-width: 380px) {
            .logo span { display: none; }
        }

        @yield('extra_css')
    </style>
</head>
<body>

    <header id="mainHeader">
        <div class="container nav-inner">
            <a href="/" class="logo">
                <div class="logo-box">Q&A</div>
                <span>Event Q&A</span>
            </a>
            <nav class="nav-links" id="navLinks">
                <a href="/#features">Fonctionnalités</a>
                <a href="{{ route('marketplace.index') }}">Marketplace</a>
                <a href="{{ route('participant.join') }}" class="btn btn-outline" style="width: 100%">Rejoindre</a>
                <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%">Mon Espace</a>
            </nav>
            <div class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <main style="padding-top: 5rem;">
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Event Q&A. Développé avec passion pour des échanges plus humains.</p>
        </div>
    </footer>

    <script>
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');
        const header = document.getElementById('mainHeader');

        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const spans = mobileToggle.querySelectorAll('span');
            if (navLinks.classList.contains('active')) {
                spans[0].style.transform = 'translateY(7px) rotate(45deg)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
    @yield('extra_js')
</body>
</html>
