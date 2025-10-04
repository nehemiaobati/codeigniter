<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Reset Password</h4>
                </div>
                <div class="card-body">
                    <?= view('partials/flash_messages') ?>

                    <form action="<?= url_to('auth.update_password') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token) ?>">

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="confirmpassword">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Reset Password</button>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('auth.forgot_password') ?>">Forgot Password?</a> | <a href="<?= url_to('login') ?>">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
