<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'My Application') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-light">
    <header class="bg-white shadow-sm py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo fs-4 fw-bold text-primary">MySite</div>
            <nav>
                <ul class="nav">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item text-dark me-3">Hello, <?= esc(session()->get('username')) ?></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('payment.index') ?>">Make Payment</a></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('crypto.index') ?>">Crypto</a></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('login') ?>">Login</a></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <?= $this->renderSection('content') ?>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> MySite. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
