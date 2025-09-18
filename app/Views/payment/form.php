<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 150px);"> <!-- Adjust min-height based on header/footer height -->
        <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
            <h1 class="card-title text-center text-primary mb-4">Make a Payment</h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="list-unstyled mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <p class="mb-0"><?= esc(session()->getFlashdata('success')) ?></p>
            </div>
        <?php endif; ?>

        <?= form_open(url_to('payment.initiate')) ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', $email)) ?>" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount (KES)</label>
                <input type="number" class="form-control" id="amount" name="amount" value="<?= esc(old('amount')) ?>" min="100" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Pay Now</button>
        <?= form_close() ?>
        </div>
    </div>
<?= $this->endSection() ?>
