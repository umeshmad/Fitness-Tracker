<?php
// $base is the path prefix to the site root ('' for top-level pages, '../' for pages in auth/),
// so links and assets work from any folder.
$base = isset($base) ? $base : '';
$pageTitle = isset($pageTitle) ? $pageTitle : 'Fitness Tracker';
$active = isset($active) ? $active : '';
// One-time message, for example after logging in or saving a workout.
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Stride Log</title>
    <link rel="icon" href="<?= $base ?>images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= $base ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base ?>css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark site-nav sticky-top">
    <div class="container">
        <a class="navbar-brand brand" href="<?= $base ?>index.php">Stride Log</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link<?= $active === 'home' ? ' active' : '' ?>" href="<?= $base ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link<?= $active === 'features' ? ' active' : '' ?>" href="<?= $base ?>features.php">Features</a></li>
                <li class="nav-item"><a class="nav-link<?= $active === 'contact' ? ' active' : '' ?>" href="<?= $base ?>contact.php">Contact</a></li>
                <?php // The menu changes depending on whether the user is logged in. ?>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link<?= $active === 'dashboard' ? ' active' : '' ?>" href="<?= $base ?>dashboard.php">Dashboard</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-sun btn-sm" href="<?= $base ?>auth/logout.php">Log out</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link<?= $active === 'login' ? ' active' : '' ?>" href="<?= $base ?>auth/login.php">Log in</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-sun btn-sm" href="<?= $base ?>auth/register.php">Create account</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mb-0" role="alert">
            <?= e($flash['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
<main>
