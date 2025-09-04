<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Welcome to Our Landing Page' ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #eef2f7;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background-color: #ffffff;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8em;
            font-weight: bold;
            color: #007bff;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-left: 25px;
        }
        nav ul li a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        nav ul li a:hover {
            color: #007bff;
        }
        .hero {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 80px 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }
        .cta-button {
            display: inline-block;
            background-color: #ffffff;
            color: #007bff;
            padding: 15px 30px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .cta-button:hover {
            background-color: #e0e0e0;
            color: #0056b3;
        }
        .features {
            padding: 60px 20px;
            text-align: center;
        }
        .features h2 {
            font-size: 2.5em;
            margin-bottom: 40px;
            color: #007bff;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .feature-item {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-5px);
        }
        .feature-item h3 {
            margin-top: 0;
            color: #007bff;
            font-size: 1.5em;
            margin-bottom: 15px;
        }
        .feature-item p {
            font-size: 1em;
            color: #666;
        }
        footer {
            background-color: #333;
            color: #ffffff;
            text-align: center;
            padding: 30px 0;
            margin-top: 40px;
        }
        footer p {
            margin: 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">MySite</div>
            <nav>
                <ul>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li>Hello, <?= esc(session()->get('username')) ?></li>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <li><a href="/register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <h1><?= $heroTitle ?? 'Build Your Dreams with Us' ?></h1>
            <p><?= $heroSubtitle ?? 'We provide innovative solutions to help you succeed.' ?></p>
            <a href="#features" class="cta-button">Learn More</a>
        </section>

        <section id="features" class="features">
            <h2>Our Features</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <h3>Feature One</h3>
                    <p>Description of the first amazing feature that sets us apart from the competition.</p>
                </div>
                <div class="feature-item">
                    <h3>Feature Two</h3>
                    <p>Description of the second key feature, designed to enhance user experience.</p>
                </div>
                <div class="feature-item">
                    <h3>Feature Three</h3>
                    <p>Description of the third powerful feature, offering advanced capabilities.</p>
                </div>
            </div>
        </section>

        <section id="about" class="features">
            <h2>About Us</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <h3>Our Mission</h3>
                    <p>To empower businesses and individuals with cutting-edge technology and unparalleled support.</p>
                </div>
                <div class="feature-item">
                    <h3>Our Vision</h3>
                    <p>To be the leading provider of innovative solutions, driving progress and creating value.</p>
                </div>
            </div>
        </section>

        <section id="contact" class="features">
            <h2>Contact Us</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <h3>Email</h3>
                    <p>info@mysite.com</p>
                </div>
                <div class="feature-item">
                    <h3>Phone</h3>
                    <p>+1 (123) 456-7890</p>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> MySite. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
