<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    /* Add some basic styling for article content */
    .article-body h3 {
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    .article-body p {
        line-height: 1.8;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-5">
  <div class="row">
    <div class="col-lg-8 mx-auto">
      
      <main>
        <article>
          <!-- Post header-->
          <header class="mb-4">
            <h1 class="fw-bolder mb-1"><?= esc($post->title) ?></h1>
            <div class="text-muted fst-italic mb-2">
              Posted on <?= esc($post->published_at ? $post->published_at->toFormattedDateString() : 'Not Set') ?> by <?= esc($post->author_name) ?>
            </div>
            <?php if (!empty($post->category_name)): ?>
            <a class="badge bg-secondary text-decoration-none link-light" href="#!">
              <?= esc($post->category_name) ?>
            </a>
            <?php endif; ?>
          </header>

          <!-- Preview image figure-->
          <?php if (!empty($post->featured_image_url)): ?>
          <figure class="mb-4">
            <img class="img-fluid rounded" src="<?= esc($post->featured_image_url, 'attr') ?>" alt="<?= esc($post->title, 'attr') ?>">
          </figure>
          <?php endif; ?>

          <!-- Post content-->
          <section class="mb-5 article-body">
            <?php if (!empty($post->excerpt)): ?>
            <p class="fs-5 mb-4 lead"><?= esc($post->excerpt) ?></p>
            <?php endif; ?>
            
            <?= $post->body_html ?>
          </section>
        </article>
      </main>

    </div>
  </div>
</div>
<?= $this->endSection() ?>
