<!DOCTYPE html>
<html lang="fr" data-brand="purple" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Home')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
</head>
<body id="body-root" style="background:var(--muted);min-height:100vh;">

@yield('content')

<script>
    // Persister le thème et la couleur
    (function() {
        const isDark = localStorage.getItem('theme') === 'dark';
        if (isDark) document.getElementById('html-root').classList.add('dark');
        const brand = localStorage.getItem('brand') || 'purple';
        document.getElementById('html-root').setAttribute('data-brand', brand);
    })();
</script>

</body>
</html>
