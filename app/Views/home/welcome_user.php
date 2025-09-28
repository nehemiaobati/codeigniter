<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">User Dashboard</h3>
                </div>
                <div class="card-body p-5 text-center">
                    <h4 class="card-title mb-3">Welcome back, <?= esc($username ?? 'User') ?>!</h4>
                    <p class="card-text text-muted">Your Web Development Solution is ready to be activated.</p>
                    
                    <div class="card my-4 text-start">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Account Information</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Email:</strong>
                                    <span><?= esc($email ?? 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Member Since:</strong>
                                    <span><?= esc($member_since ? date('F d, Y', strtotime($member_since)) : 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Current Balance:</strong>
                                    <span>$<?= esc(number_format((float)($balance ?? 0.00), 2)) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Service Status:</strong>
                                    <?php if (isset($balance) && (float)$balance > 0): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Payment Required</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="mt-4">Complete your purchase to unlock all features and launch your project.</p>
                    <a href="<?= url_to('payment.index') ?>" class="btn btn-primary btn-lg mt-2 px-5 btn-hover-effect">
                        Make a Payment
                    </a>
                </div>
                <div class="card-footer text-center text-muted">
                    Need help? <a href="<?= url_to('contact.form') ?>">Contact Support</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
