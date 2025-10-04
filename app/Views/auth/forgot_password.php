<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Forgot Password</h4>
                </div>
                <div class="card-body">
                    <?= view('partials/flash_messages') ?>

                    <p>Enter your email address and we will send you a link to reset your password.</p>

                    <form action="<?= url_to('auth.send_reset_link') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Send Password Reset Link</button>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('login') ?>">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
