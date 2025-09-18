<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Welcome, <?= esc($username) ?>!</h4>
                </div>
                <div class="card-body">
                    <p class="lead">We're glad to have you here.</p>
                    <p>This is your personalized dashboard. From here, you can manage your account, view your recent activity, and explore the features of our application.</p>
                    <hr>
                    <p class="mb-0">If you have any questions, feel free to reach out to our support team.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
