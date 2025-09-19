<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Welcome, <?= esc($username) ?>!</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Account Summary</h5>
                            <p><strong>Email:</strong> <?= esc($email ?? 'user@example.com') ?></p>
                            <p><strong>Member Since:</strong> <?= esc($member_since ?? 'January 1, 2024') ?></p>
                            <p><strong>Membership:</strong> <span class="badge bg-success">Premium</span></p>
                        </div>
                        <div class="col-md-8">
                            <h5>Quick Actions</h5>
                            <a href="#" class="btn btn-primary">View Profile</a>
                            <a href="#" class="btn btn-secondary">Account Settings</a>
                            <a href="#" class="btn btn-info">View Payments</a>
                            <hr>
                            <h5>Recent Activity</h5>
                            <ul class="list-group">
                                <li class="list-group-item">Logged in successfully.</li>
                                <li class="list-group-item">Updated profile picture.</li>
                                <li class="list-group-item">Made a new payment.</li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0">If you have any questions, feel free to reach out to our support team.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
