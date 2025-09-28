<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-lg text-center">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">User Dashboard</h3>
                </div>
                <div class="card-body p-5">
                    <h4 class="card-title">Welcome back, <?= esc($username ?? 'User') ?>!</h4>
                    <p class="card-text text-muted">Your Web Development Solution is ready to be activated.</p>
                    
                    <div class="card my-4">
                        <div class="card-body text-start">
                            <h5 class="card-title text-primary">Account Information</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Email:</strong> <?= esc($email ?? 'N/A') ?></li>
                                <li class="list-group-item"><strong>Member Since:</strong> <?= esc($member_since ? date('F d, Y', strtotime($member_since)) : 'N/A') ?></li>
                                <li class="list-group-item"><strong>Current Balance:</strong> <?= esc(number_format($balance ?? '0.00', 2)) ?></li>
                                <li class="list-group-item">
                                    <strong>Service Status:</strong>
                                    <?php if (isset($balance) && floatval($balance) > 0.00): ?>
                                        <span class="badge bg-success">Service Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Payment Required</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="mt-4">Complete your purchase to unlock all features and launch your project.</p>
                    <a href="<?= url_to('payment.index') ?>" class="btn btn-primary btn-lg mt-2 px-5 btn-hover-effect">
                        Purchase Service
                    </a>
                </div>
                <div class="card-footer text-muted">
                    Need help? <a href="<?= url_to('contact.form') ?>">Contact Support</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
