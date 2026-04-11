<!DOCTYPE html>
<html lang="fr" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Event Q&A</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    
    {{-- Injection forcée des styles admin pour éviter les problèmes de cache en prod --}}
    <style>
        .admin-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .admin-table-card { overflow-x: auto; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; background: var(--card); }
        .admin-table th { padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); }
        .admin-table td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .admin-table tr:hover td { background: var(--muted); }
        .badge { border-radius: 9999px; padding: 0.25rem 0.625rem; font-size: 0.75rem; background: var(--brand-light); color: var(--brand); flex-shrink: 0; display: inline-flex; }
        .badge-success { background: rgba(34, 197, 94, 0.1) !important; color: #22c55e !important; }
        .badge-error { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }
        .badge-info { background: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none !important; }
        .btn-primary { background: var(--brand) !important; color: #fff !important; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--foreground); }
        .btn-outline:hover { background: var(--muted); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .dash-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .dash-title { font-size: 1.5rem; font-weight: 700; color: var(--foreground); }
        .dash-subtitle { font-size: 0.875rem; color: var(--muted-foreground); }
    </style>
    @stack('styles')
</head>
<body id="body-root">

<div class="dashboard-wrapper">
    <div class="dashboard-outer">
        <div class="dashboard-card-wrap">

            <div class="dash-flex">

                <!-- SIDEBAR MOBILE OVERLAY -->
                <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

                <!-- SIDEBAR -->
                <div class="sidebar-mobile-wrap" id="sidebar-wrap">
                    <aside class="sidebar" id="sidebar">
                        
                        <!-- Header sidebar -->
                        <div class="sidebar-header">
                            <div class="flex items-center gap-2">
                                <div class="sidebar-logo">QA</div>
                                <span class="sidebar-title">Event Q&A</span>
                            </div>
                            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="chevron-icon" id="chevron-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Navigation -->
                        <nav class="sidebar-nav">
                            <ul>
                                @if(Auth::user()->role === 'admin')
                                    <!-- ADMIN MENU -->
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25a2.25 2.25 0 01-2.25 2.25h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                                            </svg>
                                            <span>Tableau de bord</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.admins.index') }}" class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>Admins</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            <span>Utilisateurs</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                            </svg>
                                            <span>Événements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.subscriptions.index') }}" class="{{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75-10.5h16.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5H3.75A1.5 1.5 0 012.25 18V4.5a1.5 1.5 0 011.5-1.5z" />
                                            </svg>
                                            <span>Abonnements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.marketplace.index') }}" class="{{ request()->routeIs('admin.marketplace.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-16.5 0V15m0 0v-1.5a6.75 6.75 0 0113.5 0v1.5m-13.5 0h13.5m-13.5 0a2.25 2.25 0 00-2.25 2.25v2.25m13.5-4.5a2.25 2.25 0 012.25 2.25v2.25" />
                                            </svg>
                                            <span>Marketplace</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <span>Rapports</span>
                                        </a>
                                    </li>
                                @else
                                    <!-- MODERATOR/PANELIST MENU -->
                                    <li>
                                        <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            <span>Accueil</span>
                                        </a>
                                    </li>
                                    
                                    @if(Auth::user()->role === 'panelist')
                                    <li>
                                        <a href="{{ route('panelist.index') }}" class="{{ request()->routeIs('panelist.index') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Mes Événements (Panéliste)</span>
                                        </a>
                                    </li>
                                    @endif

                                    @if(Auth::user()->role === 'moderator')
                                    <li>
                                        <a href="{{ route('dashboard.events.index') }}" class="{{ request()->routeIs('dashboard.events.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Mes Événements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.statistics') }}" class="{{ request()->routeIs('dashboard.statistics') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            <span>Statistiques</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.insights.index') }}" class="{{ request()->routeIs('dashboard.insights.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.25 15l.75-2.25 2.25-.75-2.25-.75-.75-2.25-.75 2.25-2.25.75 2.25.75.75 2.25zM15.75 7.5l.5-1.5 1.5-.5-1.5-.5-.5-1.5-.5 1.5-1.5.5 1.5.5.5 1.5z" />
                                            </svg>
                                            <span>IA Insights</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.subscription.index') }}" class="{{ request()->routeIs('dashboard.subscription.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            <span>Abonnement</span>
                                        </a>
                                    </li>
                                    @endif

                                    <li>
                                        <a href="{{ route('marketplace.index') }}" class="{{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                            </svg>
                                            <span>Marketplace</span>
                                        </a>
                                    </li>
                                @endif

                                <!-- SHARED LINKS -->
                                <li>
                                    <a href="{{ route('dashboard.profile') }}" class="{{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Profil</span>
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('auth.logout') }}" method="POST" id="logout-form" style="display: none;">@csrf</form>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Déconnexion</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <!-- Tip de bas de sidebar -->
                        <div class="sidebar-tip">
                            <div class="sidebar-tip-inner" id="sidebar-tip">
                                Contrôlez vos évènements en toute simplicité.
                            </div>
                        </div>

                    </aside>
                </div>
                <!-- /SIDEBAR -->

                <!-- ======== CONTENU PRINCIPAL ======== -->
                <main class="dash-main" id="dash-main">

                    <!-- TOPBAR -->
                    <header class="topbar" id="topbar">
                        <div class="topbar-inner">

                            <!-- Bouton menu mobile -->
                            <button class="menu-btn" onclick="openSidebar()" aria-label="Ouvrir le menu">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="2" stroke="currentColor" style="width:1.25rem;height:1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            <!-- Recherche -->
                            <div class="topbar-search">
                                <label>
                                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="search" id="topbar-search-input"
                                           placeholder="Rechercher pièces, appareils..."
                                           aria-label="Recherche">
                                </label>
                            </div>

                            <!-- Actions -->
                            <div class="topbar-actions">

                                <!-- Notifications -->
                                <div class="dropdown" id="notif-dropdown">
                                    <button class="topbar-btn" id="notif-btn" aria-label="Notifications"
                                            onclick="toggleDropdown('notif-dropdown')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <span class="notif-badge">3</span>
                                        <span style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);">Ouvrir les notifications</span>
                                    </button>
                                    <div class="dropdown-content">
                                        <div class="dropdown-label">Notifications</div>
                                        <div class="dropdown-sep"></div>
                                        <div class="dropdown-item">Cycle de lessive terminé</div>
                                        <div class="dropdown-item">Porte d'entrée verrouillée</div>
                                        <div class="dropdown-item">Rappel filtre HVAC</div>
                                        <div class="dropdown-sep"></div>
                                        <div class="dropdown-item text-muted">Voir tout</div>
                                    </div>
                                </div>

                                <!-- Settings -->
                                <div class="dropdown" id="settings-dropdown">
                                    <button class="topbar-btn" id="settings-btn" aria-label="Paramètres"
                                            onclick="toggleDropdown('settings-dropdown')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                    <div class="dropdown-content">
                                        <div class="dropdown-label">Paramètres</div>
                                        <div class="dropdown-sep"></div>
                                        <div class="dropdown-item">Gérer les utilisateurs</div>
                                        <div class="dropdown-item">Réseau</div>
                                        <div class="dropdown-sep"></div>
                                        <!-- Theme toggle -->
                                        <div style="padding:0.375rem 0.5rem;">
                                            <button class="theme-toggle-btn" id="theme-toggle-btn" onclick="toggleTheme()">
                                                <span>Thème</span>
                                                <span id="theme-label" style="display:inline-flex;align-items:center;gap:0.375rem;">
                                                    Clair
                                                    <svg id="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                         style="width:1rem;height:1rem;">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                        <!-- Color theme picker -->
                                        <div style="padding:0 0.5rem 0.5rem;">
                                            <p class="color-picker-label">Couleur du thème</p>
                                            <div class="color-picker-row" id="color-picker-row">
                                                <button class="color-swatch active" data-brand="purple" aria-label="Violet"
                                                        style="background:#7c3aed;" onclick="setBrand('purple',this)"></button>
                                                <button class="color-swatch" data-brand="blue" aria-label="Bleu"
                                                        style="background:#2563eb;" onclick="setBrand('blue',this)"></button>
                                                <button class="color-swatch" data-brand="teal" aria-label="Turquoise"
                                                        style="background:#0d9488;" onclick="setBrand('teal',this)"></button>
                                                <button class="color-swatch" data-brand="orange" aria-label="Orange"
                                                        style="background:#ea580c;" onclick="setBrand('orange',this)"></button>
                                                <button class="color-swatch" data-brand="pink" aria-label="Rose"
                                                        style="background:#db2777;" onclick="setBrand('pink',this)"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Utilisateur -->
                                <div class="dropdown" id="user-dropdown">
                                    <button class="avatar-btn" onclick="toggleDropdown('user-dropdown')"
                                            aria-label="Menu utilisateur">
                                        <div class="avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                                    </button>
                                    <div class="dropdown-content">
                                        <div class="dropdown-label" style="display:flex;align-items:center;gap:0.5rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ Auth::user()->name }}
                                        </div>
                                        <div class="dropdown-sep"></div>
                                        <a href="{{ route('dashboard.profile') }}" class="dropdown-item">Mon Profil</a>
                                        <div class="dropdown-sep"></div>
                                        <form action="{{ route('auth.logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item destructive" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-family: inherit; font-size: inherit; padding: 0.5rem 1rem;">Déconnexion</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            <!-- /Actions -->
                        </div>
                    </header>
                    <!-- /TOPBAR -->

                    <!-- Contenu de la page -->
                    @yield('content')

                    <!-- Footer -->
                    <p class="copyright">
                        © Tous droits réservés par <a href="https://codescandy.com/" target="_blank">CodesCandy</a>.
                        Distribué par : <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                    </p>

                </main>
                <!-- /CONTENU PRINCIPAL -->

            </div><!-- /dash-flex -->

        </div><!-- /dashboard-card-wrap -->
    </div><!-- /dashboard-outer -->
</div><!-- /dashboard-wrapper -->

<!-- ============================================================
     JavaScript du Dashboard
     ============================================================ -->
<script>
    // ── Sidebar collapse ──────────────────────────────────────
    const sidebar    = document.getElementById('sidebar');
    const sidebarWrap = document.getElementById('sidebar-wrap');
    const overlay    = document.getElementById('sidebar-overlay');
    const chevron    = document.getElementById('chevron-icon');
    const tipEl      = document.getElementById('sidebar-tip');

    let sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === '1';

    function applySidebarState() {
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            chevron.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>`;
            if (tipEl) tipEl.textContent = 'Tip';
        } else {
            sidebar.classList.remove('collapsed');
            chevron.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>`;
            if (tipEl) tipEl.textContent = 'Contrôlez vos évènements en toute simplicité.';
        }
    }

    function toggleSidebar() {
        sidebarCollapsed = !sidebarCollapsed;
        localStorage.setItem('sidebar-collapsed', sidebarCollapsed ? '1' : '0');
        applySidebarState();
    }

    function openSidebar() {
        sidebarWrap.classList.add('open');
        overlay.classList.add('active');
    }

    function closeSidebar() {
        sidebarWrap.classList.remove('open');
        overlay.classList.remove('active');
    }

    applySidebarState();

    // ── Theme toggle ──────────────────────────────────────────
    const htmlRoot = document.getElementById('html-root');
    const themeLabel = document.getElementById('theme-label');
    const MOON_SVG = `Sombre <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>`;
    const SUN_SVG  = `Clair <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`;

    let isDark = localStorage.getItem('theme') === 'dark';

    function applyTheme() {
        if (isDark) {
            htmlRoot.classList.add('dark');
            if (themeLabel) themeLabel.innerHTML = MOON_SVG;
        } else {
            htmlRoot.classList.remove('dark');
            if (themeLabel) themeLabel.innerHTML = SUN_SVG;
        }
    }

    function toggleTheme() {
        isDark = !isDark;
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        applyTheme();
    }

    applyTheme();

    // ── Brand color ───────────────────────────────────────────
    const swatches = document.querySelectorAll('.color-swatch');

    function setBrand(brand, el) {
        htmlRoot.setAttribute('data-brand', brand);
        localStorage.setItem('brand', brand);
        swatches.forEach(s => s.classList.remove('active'));
        if (el) el.classList.add('active');
        // Mettre à jour les sliders dynamiques si présents
        if (typeof updateRangeTrack === 'function') updateRangeTrack();
    }

    (function initBrand() {
        const saved = localStorage.getItem('brand') || 'purple';
        htmlRoot.setAttribute('data-brand', saved);
        swatches.forEach(s => {
            if (s.dataset.brand === saved) s.classList.add('active');
            else s.classList.remove('active');
        });
    })();

    // ── Dropdowns ─────────────────────────────────────────────
    function toggleDropdown(id) {
        const dd = document.getElementById(id);
        const isOpen = dd.classList.contains('open');
        // Fermer tous
        document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
        if (!isOpen) dd.classList.add('open');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
        }
    });

    // ── Toggle switches interactifs (dans les pages) ──────────
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('toggle-input')) {
            const thumb = e.target.nextElementSibling?.nextElementSibling;
        }
    });
</script>

@stack('scripts')

</body>
</html>
