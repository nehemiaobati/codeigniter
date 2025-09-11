<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
    <div class="container">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success mt-4" role="alert">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <ul class="list-unstyled mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
        <section class="hero bg-primary text-white text-center p-5 mt-4 rounded shadow-lg">
            <h1 class="display-3 mb-3"><?= esc($heroTitle ?? 'Build Your Dreams with Us') ?></h1>
            <p class="fs-5 mb-4"><?= esc($heroSubtitle ?? 'We provide innovative solutions to help you succeed.') ?></p>
            <a href="#features" class="btn btn-light btn-lg fw-bold">Learn More</a>
        </section>

        <section id="features" class="py-5 text-center">
            <h2 class="display-4 mb-5 text-primary">Our Features</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Feature One</h3>
                            <p class="card-text text-muted">Description of the first amazing feature that sets us apart from the competition.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Feature Two</h3>
                            <p class="card-text text-muted">Description of the second key feature, designed to enhance user experience.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Feature Three</h3>
                            <p class="card-text text-muted">Description of the third powerful feature, offering advanced capabilities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="py-5 text-center">
            <h2 class="display-4 mb-5 text-primary">About Us</h2>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Our Mission</h3>
                            <p class="card-text text-muted">To empower businesses and individuals with cutting-edge technology and unparalleled support.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Our Vision</h3>
                            <p class="card-text text-muted">To be the leading provider of innovative solutions, driving progress and creating value.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="py-5 text-center">
            <h2 class="display-4 mb-5 text-primary">Contact Us</h2>
            <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Email</h3>
                            <p class="card-text text-muted">info@mysite.com</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="card-title text-primary fs-4 mb-3">Phone</h3>
                            <p class="card-text text-muted">+1 (123) 456-7890</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4 mt-5">
                <h3 class="card-title text-primary fs-3 mb-4">Send us a message</h3>
                <form action="<?= url_to('contact.send') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name')) ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="subject" class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?= esc(old('subject')) ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="message" class="form-label">Message:</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?= esc(old('message')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Send Message</button>
                </form>
            </div>
        </section>
    </div>
<?= $this->endSection() ?>
