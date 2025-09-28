<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5">

    <!-- Hero Section -->
    <section class="hero text-center mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-3"><?= esc($heroTitle ?? 'Professional Web Development Solutions') ?></h1>
                <p class="lead text-muted mb-4"><?= esc($heroSubtitle ?? 'We build fast, reliable, and scalable web applications tailored to your business needs.') ?></p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= url_to('register') ?>" class="btn btn-primary btn-lg px-4 gap-3 btn-hover-effect">Get Started</a>
                    <a href="#features" class="btn btn-outline-secondary btn-lg px-4 btn-hover-effect">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 text-center">
        <h2 class="display-5 fw-bold mb-5 text-primary">Why Choose Us?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Custom Development</h3>
                        <p class="text-muted">Tailored solutions that perfectly match your specifications and business goals.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Responsive Design</h3>
                        <p class="text-muted">Flawless performance and appearance on all devices, from desktops to smartphones.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 card-hoverable">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; font-size: 1.5rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="fs-4 fw-bold">Secure & Scalable</h3>
                        <p class="text-muted">Built with security best practices and ready to grow with your business.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="display-5 fw-bold text-primary">About Our Company</h2>
                <p class="text-muted">We are a team of dedicated developers passionate about creating high-quality web solutions. Our mission is to transform your ideas into reality with clean, efficient, and maintainable code. We believe in building long-term partnerships with our clients, providing continuous support and value.</p>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://via.placeholder.com/500x300.png?text=Our+Team" class="img-fluid rounded-3 shadow" alt="About Us Image">
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section id="cta" class="py-5 my-5 bg-primary text-white text-center rounded-3 shadow">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-3">Ready to Start Your Project?</h2>
                <p class="lead mb-4">Let's build something amazing together. Register for an account or log in to get started.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= url_to('register') ?>" class="btn btn-light btn-lg px-4 fw-bold btn-hover-effect">Register Now</a>
                    <a href="<?= url_to('login') ?>" class="btn btn-outline-light btn-lg px-4 btn-hover-effect">Login</a>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
