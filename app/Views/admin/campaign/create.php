<?= '
' ?>
<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    .campaign-card {
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        border: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= url_to('admin.index') ?>" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Back</a>
        <h1 class="fw-bold mb-0">Create Email Campaign</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card campaign-card">
                <div class="card-body p-4 p-md-5">
                    <p class="text-muted">Compose your message below. The email will be sent to all registered users. You can use HTML for formatting.</p>
                    
                    <form action="<?= url_to('admin.campaign.send') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Email Subject" value="<?= old('subject') ?>" required>
                            <label for="subject">Email Subject</label>
                        </div>

                        <div class="form-floating mb-4">
                            <textarea class="form-control" id="message" name="message" placeholder="Your message here... You can use HTML tags like <strong>, <a>, etc." style="height: 300px" required><?= old('message') ?></textarea>
                            <label for="message">Message Body (HTML supported)</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold" onclick="return confirm('Are you sure you want to send this campaign to all users?');">
                                <i class="bi bi-send-fill"></i> Send Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
