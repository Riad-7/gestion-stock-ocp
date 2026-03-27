@php
    use Illuminate\Support\Str;

    $routeName = request()->route()?->getName();
    $action = $routeName ? Str::afterLast($routeName, '.') : null;
    $resource = $routeName && str_contains($routeName, '.')
        ? Str::before($routeName, '.')
        : $routeName;

    $resourceLabels = [
        'welcome' => 'Accueil',
        'dashboard' => 'Dashboard',
        'profile' => 'Profil',
        'login' => 'Connexion',
        'register' => 'Inscription',
        'password' => 'Mot de passe',
        'verification' => 'Verification email',
        'clients' => 'Clients',
        'fournisseurs' => 'Fournisseurs',
        'articles' => 'Articles',
        'ventes' => 'Ventes',
        'commandes' => 'Commandes',
        'notifications' => 'Notifications',
    ];

    $actionLabels = [
        'index' => null,
        'create' => 'Nouveau',
        'edit' => 'Modifier',
        'show' => 'Details',
        'request' => 'Mot de passe oublie',
        'reset' => 'Reinitialisation mot de passe',
        'confirm' => 'Confirmation mot de passe',
        'notice' => 'Verification email',
    ];

    $explicitTitle = trim($__env->yieldContent('title'));
    $pageTitle = $explicitTitle;

    if ($pageTitle === '') {
        if (in_array($routeName, ['welcome', 'dashboard', 'login', 'register'], true)) {
            $pageTitle = $resourceLabels[$routeName];
        } elseif ($routeName === 'profile.edit') {
            $pageTitle = 'Mon profil';
        } elseif (in_array($routeName, ['password.request', 'password.reset', 'password.confirm'], true)) {
            $pageTitle = $actionLabels[$action] ?? $resourceLabels[$resource] ?? 'Mot de passe';
        } elseif (str_starts_with((string) $routeName, 'verification.')) {
            $pageTitle = 'Verification email';
        } elseif ($resource) {
            $resourceLabel = $resourceLabels[$resource] ?? Str::headline($resource);
            $actionLabel = $actionLabels[$action] ?? null;

            $pageTitle = $actionLabel
                ? "{$actionLabel} {$resourceLabel}"
                : $resourceLabel;
        } else {
            $pageTitle = config('app.name', 'GS OCP');
        }
    }

    $fullTitle = "Gestion de stock | {$pageTitle}";
@endphp

<title>{{ $fullTitle }}</title>
<link rel="icon" type="image/png" href="{{ asset('logoocp-removebg-preview.png') }}">
