<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'My Application') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 sticky-top">
            <div class="container">
                <a class="navbar-brand fs-4 fw-bold text-primary" href="<?= url_to('welcome') ?>">MySite</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto text-end">
                        <?php if (session()->get('isLoggedIn')): ?>
                            <li class="nav-item text-dark me-3 fw-bold d-lg-flex align-items-center">Hello, <?= esc(session()->get('username')) ?></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('home') ?>">Home</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('payment.index') ?>">Make Payment</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('crypto.index') ?>">Crypto</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('logout') ?>">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('login') ?>">Login</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="<?= url_to('register') ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> MySite. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
