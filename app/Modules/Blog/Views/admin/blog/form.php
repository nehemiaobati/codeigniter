<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= url_to('admin.blog.index') ?>" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Back</a>
        <h1 class="fw-bold mb-0"><?= esc($formTitle) ?></h1>
    </div>

    <div class="card blueprint-card">
        <div class="card-body p-4 p-md-5">
            <form action="<?= esc($formAction) ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Post Title" value="<?= old('title', $post->title ?? '') ?>" required>
                            <label for="title">Post Title</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="body_html" name="body_html" placeholder="Paste your HTML content here..." style="height: 400px"><?= old('body_html', $post->body_html ?? '') ?></textarea>
                            <label for="body_html">Body Content (HTML)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="status" name="status">
                                <option value="published" <?= old('status', $post->status ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= old('status', $post->status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                            <label for="status">Status</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?= old('published_at', ($post && $post->published_at) ? $post->published_at->toDateTimeString() : date('Y-m-d\TH:i')) ?>">
                            <label for="published_at">Publish Date</label>
                        </div>
                         <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="featured_image_url" name="featured_image_url" placeholder="Image URL" value="<?= old('featured_image_url', $post->featured_image_url ?? '') ?>">
                            <label for="featured_image_url">Featured Image URL</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Category" value="<?= old('category_name', $post->category_name ?? '') ?>">
                            <label for="category_name">Category</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="excerpt" name="excerpt" placeholder="Short summary..." style="height: 100px"><?= old('excerpt', $post->excerpt ?? '') ?></textarea>
                            <label for="excerpt">Excerpt (Short Summary)</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control" id="meta_description" name="meta_description" placeholder="SEO Description..." style="height: 100px"><?= old('meta_description', $post->meta_description ?? '') ?></textarea>
                            <label for="meta_description">Meta Description (for SEO)</label>
                        </div>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
