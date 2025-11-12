<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>

    pre {
        background-color:var(--code-bg);
        padding: 10px;
        border: 1px solid #ddd;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    code {
        font-family: DejaVu Sans Mono, monospace;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    th {
        background-color: var(--code-bg);
    }

    hr {
        border: 0;
        border-top: 1px solid #ccc;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container">

    <h1>Project Documentation: GenAI Web Platform</h1><br />
    <hr /><br />
    <h3><strong>Part I: Getting Started</strong></h3><br />
    <p><strong>1. Introduction</strong><br />
        1.1. What is GenAI Web Platform?<br />
        1.2. Core Features &amp; Capabilities<br />
        1.3. Who Is This For?<br />
        1.4. Technology Stack</p><br />
    <p><strong>2. Quick Start Guide</strong><br />
        2.1. Your First 5 Minutes<br />
        2.2. Running the Application Locally<br />
        2.3. Key Concepts at a Glance</p><br />
    <p><strong>3. Installation</strong><br />
        3.1. Server Requirements &amp; Prerequisites<br />
        3.2. Automated Installation (Recommended)<br />
        3.3. Manual Installation (Advanced)<br />
        3.4. Environment Configuration (<code>.env</code>)<br />
        3.5. Post-Installation Steps &amp; Security</p><br />
    <hr /><br />
    <h3><strong>Part II: Guides &amp; Tutorials</strong></h3><br />
    <p><strong>4. Core Concepts</strong><br />
        4.1. Architectural Overview (MVC-S)<br />
        4.2. The Request Lifecycle<br />
        4.3. Service Container &amp; Dependency Injection<br />
        4.4. Directory Structure Explained<br />
        4.5. Security Principles</p><br />
    <p><strong>5. Tutorial: Building Your First Feature</strong><br />
        5.1. Creating a New Route<br />
        5.2. Building the Controller &amp; Service<br />
        5.3. Interacting with the Database (Model &amp; Entity)<br />
        5.4. Displaying Data in a View</p><br />
    <p><strong>6. Feature Guides (Deep Dives)</strong><br />
        6.1. User Authentication<br />
        6.1.1. Registration &amp; Login Flow<br />
        6.1.2. Email Verification &amp; Password Resets<br />
        6.1.3. Access Control with Filters<br />
        6.2. Payment Gateway Integration<br />
        6.2.1. Configuration<br />
        6.2.2. Initiating a Transaction<br />
        6.2.3. Verifying a Payment<br />
        6.3. AI Service Integration<br />
        6.3.1. Generating Content<br />
        6.3.2. Conversational Memory System<br />
        6.3.3. Handling Multimedia Inputs<br />
        6.4. Cryptocurrency Data Service<br />
        6.4.1. Querying Balances<br />
        6.4.2. Fetching Transaction Histories<br />
        6.5. Administrative Dashboard<br />
        6.5.1. User Management<br />
        6.5.2. Sending Email Campaigns</p><br />
    <hr /><br />
    <h3><strong>Part III: Technical Reference</strong></h3><br />
    <p><strong>7. API Reference</strong><br />
        7.1. Authentication<br />
        7.2. Endpoints<br />
        7.2.1. <code>GET /resource</code><br />
        7.2.2. <code>POST /resource</code><br />
        7.2.3. <code>PUT /resource/{id}</code><br />
        7.2.4. <code>DELETE /resource/{id}</code><br />
        7.3. Rate Limiting<br />
        7.4. Error Codes &amp; Responses</p><br />
    <p><strong>8. Command-Line Interface (CLI)</strong><br />
        8.1. Overview of Custom Commands<br />
        8.2. <code>php spark train</code><br />
        8.3. <code>php spark [another:command]</code></p><br />
    <p><strong>9. Configuration Reference</strong><br />
        9.1. Application (<code>App.php</code>)<br />
        9.2. Database (<code>Database.php</code>)<br />
        9.3. Custom Configurations (<code>AGI.php</code>, etc.)</p><br />
    <p><strong>10. Testing</strong><br />
        10.1. Running the Test Suite<br />
        10.2. Writing Unit Tests<br />
        10.3. Writing Feature Tests</p><br />
    <hr /><br />
    <h3><strong>Part IV: Operations &amp; Community</strong></h3><br />
    <p><strong>11. Deployment</strong><br />
        11.1. Production Server Setup<br />
        11.2. Deployment Checklist<br />
        11.3. Performance Optimization</p><br />
    <p><strong>12. Troubleshooting</strong><br />
        12.1. Frequently Asked Questions (FAQ)<br />
        12.2. Common Error Resolutions<br />
        12.3. Logging &amp; Debugging</p><br />
    <p><strong>13. Contributing</strong><br />
        13.1. Contribution Guidelines<br />
        13.2. Code Style (PSR-12)<br />
        13.3. Submitting a Pull Request</p><br />
    <p><strong>14. Appendices</strong><br />
        14.1. Glossary of Terms<br />
        14.2. Changelog &amp; Release History</p><br />
    <hr /><br />
    <h3><strong>Part IV: Operations &amp; Community</strong></h3><br />
    <p><strong>11. Deployment</strong><br />
        11.1. Production Server Setup<br />
        11.2. Deployment Checklist<br />
        11.3. Performance Optimization</p><br />
    <p><strong>12. Troubleshooting</strong><br />
        12.1. Frequently Asked Questions (FAQ)<br />
        12.2. Common Error Resolutions<br />
        12.3. Logging &amp; Debugging</p><br />
    <p><strong>13. Contributing</strong><br />
        13.1. Contribution Guidelines<br />
        13.2. Code Style (PSR-12)<br />
        13.3. Submitting a Pull Request</p><br />
    <p><strong>14. Appendices</strong><br />
        14.1. Glossary of Terms<br />
        14.2. Changelog &amp; Release History</p><br />
    <hr /><br />
    <h2><strong>Part I: Getting Started</strong></h2><br />
    <h3><strong>1. Introduction</strong></h3><br />
    <h4><strong>1.1. What is GenAI Web Platform?</strong></h4><br />
    <p>The GenAI Web Platform is a comprehensive, multi-functional application built on the CodeIgniter 4 framework. It
        serves as a portal for registered users to access a suite of powerful digital services, including AI-driven
        content generation and analysis, real-time cryptocurrency data queries, and robust user and content management
        capabilities. Designed with a modular architecture, it features a secure user authentication system, an account
        dashboard with an integrated balance and payment system (supporting M-Pesa, Airtel, and Card), and a complete
        administrative panel for user oversight.</p><br />
    <h4><strong>1.2. Core Features &amp; Capabilities</strong></h4><br />
    <ul><br />
        <li><strong>User Authentication:</strong> Secure registration, login, email verification, and password reset
            functionality.</li><br />
        <li><strong>Payment Gateway Integration:</strong> Seamless payments via Paystack, a popular African payment
            gateway.</li><br />
        <li><strong>AI Service Integration:</strong> Advanced text and multimedia interaction with Google's Gemini API,
            featuring a sophisticated conversational memory system.</li><br />
        <li><strong>Cryptocurrency Data Service:</strong> Real-time balance and transaction history queries for Bitcoin
            (BTC) and Litecoin (LTC) addresses.</li><br />
        <li><strong>Administrative Dashboard:</strong> Robust tools for user management, balance adjustments, financial
            oversight, and sending email campaigns to all users.</li><br />
        <li><strong>Secure &amp; Performant:</strong> Built with modern security best practices and optimized for
            production environments.</li><br />
    </ul><br />
    <h4><strong>1.3. Who Is This For?</strong></h4><br />
    <p>This platform is designed for developers, creators, and businesses, particularly in Kenya and the broader African
        market, who require a flexible, pay-as-you-go solution for accessing advanced AI and blockchain data services.
        It serves as both a functional application and a robust foundation for building more complex systems.</p><br />
    <h4><strong>1.4. Technology Stack</strong></h4><br />
    <ul><br />
        <li><strong>Backend:</strong> PHP 8.1+, CodeIgniter 4</li><br />
        <li><strong>Frontend:</strong> Bootstrap 5, JavaScript, HTML5, CSS3</li><br />
        <li><strong>Database:</strong> MySQL</li><br />
        <li><strong>Web Server:</strong> Apache2</li><br />
        <li><strong>Key Libraries:</strong><br />
            <ul><br />
                <li><code>google/gemini-php</code>: For interacting with the Gemini API.</li><br />
                <li><code>dompdf/dompdf</code>: For PDF generation.</li><br />
                <li><code>nlp-tools/nlp-tools</code>: For Natural Language Processing tasks.</li><br />
                <li><code>php-ffmpeg/php-ffmpeg</code>: For audio and video processing.</li><br />
            </ul>
        </li><br />
        <li><strong>System Dependencies:</strong> Pandoc, ffmpeg</li><br />
        <li><strong>Development &amp; Deployment:</strong> Composer, PHPUnit, Spark CLI, Git, Bash</li><br />
    </ul><br />
    <h3><strong>2. Quick Start Guide</strong></h3><br />
    <h4><strong>2.1. Your First 5 Minutes</strong></h4><br />
    <p>For a fresh Ubuntu server, the fastest way to get started is with the automated setup script.</p><br />
    <ol><br />
        <li>Clone the repository: <code>git clone https://github.com/nehemiaobati/genaiwebapplication.git</code></li>
        <br />
        <li>Navigate into the directory: <code>cd genaiwebapplication</code></li><br />
        <li>Make the script executable: <code>chmod +x setup.sh</code></li><br />
        <li>Run with sudo: <code>sudo ./setup.sh</code></li><br />
        <li>After completion, edit the newly created <code>.env</code> file to add your API keys.</li><br />
    </ol><br />
    <h4><strong>2.2. Running the Application Locally</strong></h4><br />
    <ol><br />
        <li><strong>Clone the Repository:</strong>
            <code>git clone https://github.com/nehemiaobati/genaiwebapplication.git</code></li><br />
        <li><strong>Install Dependencies:</strong> <code>composer install</code></li><br />
        <li><strong>Create Environment File:</strong> Copy <code>env</code> to <code>.env</code> and configure your
            local database and <code>app.baseURL</code>.</li><br />
        <li><strong>Run Migrations:</strong> <code>php spark migrate</code></li><br />
        <li><strong>Start the Server:</strong> <code>php spark serve</code></li><br />
        <li>Access the application at <code>http://localhost:8080</code>.</li><br />
    </ol><br />
    <h4><strong>2.3. Key Concepts at a Glance</strong></h4><br />
    <ul><br />
        <li><strong>MVC-S Architecture:</strong> The application separates concerns into Models (database), Views
            (presentation), Controllers (request handling), and Services (business logic).</li><br />
        <li><strong>Services:</strong> Core functionality like payment processing (<code>PaystackService</code>), AI
            interaction (<code>GeminiService</code>), and crypto queries (<code>CryptoService</code>) are encapsulated
            in their own service classes for reusability.</li><br />
        <li><strong>Pay-As-You-Go:</strong> Users top up an account balance, and this balance is debited for each AI or
            Crypto query they perform.</li><br />
    </ul><br />
    <h3><strong>3. Installation</strong></h3><br />
    <h4><strong>3.1. Server Requirements &amp; Prerequisites</strong></h4><br />
    <ul><br />
        <li><strong>OS:</strong> Ubuntu (Recommended)</li><br />
        <li><strong>Web Server:</strong> Apache2 or Nginx</li><br />
        <li><strong>PHP:</strong> Version 8.1 or higher with <code>intl</code>, <code>mbstring</code>,
            <code>bcmath</code>, <code>curl</code>, <code>xml</code>, <code>zip</code>, <code>gd</code> extensions.</li>
        <br />
        <li><strong>Database:</strong> MySQL Server</li><br />
        <li><strong>Tools:</strong> Composer, Git, Pandoc, ffmpeg</li><br />
    </ul><br />
    <h4><strong>3.2. Automated Installation (Recommended)</strong></h4><br />
    <p>The <code>setup.sh</code> script is designed for a clean Ubuntu server and automates the entire installation
        process. It will:</p><br />
    <ul><br />
        <li>Install Apache2, PHP 8.2, and MySQL.</li><br />
        <li>Create a dedicated database and user.</li><br />
        <li>Install Composer and Node.js.</li><br />
        <li>Clone the project repository.</li><br />
        <li>Install all project dependencies.</li><br />
        <li>Create the <code>.env</code> file with generated database credentials.</li><br />
        <li>Run database migrations.</li><br />
        <li>Configure an Apache virtual host.</li><br />
    </ul><br />
    <p><strong>Usage:</strong></p><br />
    <pre><code class="language-bash">chmod +x setup.sh<br />
sudo ./setup.sh</code></pre><br />
    <h4><strong>3.3. Manual Installation (Advanced)</strong></h4><br />
    <ol><br />
        <li><strong>Clone Repository:</strong>
            <code>git clone https://github.com/nehemiaobati/genaiwebapplication.git .</code></li><br />
        <li><strong>Install Dependencies:</strong> Run <code>composer install</code>.</li><br />
        <li><strong>Configure Environment:</strong> Copy <code>env</code> to <code>.env</code>.</li><br />
        <li><strong>Database Setup:</strong> Create a MySQL database and user.</li><br />
        <li><strong>Edit <code>.env</code> file:</strong> Fill in your <code>app.baseURL</code>, database credentials,
            API keys, and email settings.</li><br />
        <li><strong>Run Migrations:</strong> Run <code>php spark migrate</code> to create all necessary tables.</li>
        <br />
        <li><strong>Set Permissions:</strong> Ensure the <code>writable/</code> directory is writable by the web server:
            <code>chmod -R 775 writable/</code>.</li><br />
        <li><strong>Configure Web Server:</strong> Point your web server's document root to the project's
            <code>public/</code> directory.</li><br />
    </ol><br />
    <h4><strong>3.4. Environment Configuration (<code>.env</code>)</strong></h4><br />
    <p>The <code>.env</code> file is critical for configuring the application. You must fill in the following values:
    </p><br />
    <ul><br />
        <li><code>CI_ENVIRONMENT</code>: <code>development</code> for local, <code>production</code> for live.</li>
        <br />
        <li><code>app.baseURL</code>: The full URL of your application (e.g., <code>http://yourdomain.com/</code>).</li>
        <br />
        <li><code>database.default.*</code>: Your database connection details.</li><br />
        <li><code>encryption.key</code>: A unique, 32-character random string for encryption.</li><br />
        <li><code>PAYSTACK_SECRET_KEY</code>: Your secret key from your Paystack dashboard.</li><br />
        <li><code>GEMINI_API_KEY</code>: Your API key for the Google Gemini service.</li><br />
        <li><code>recaptcha_siteKey</code> &amp; <code>recaptcha_secretKey</code>: Your keys for Google reCAPTCHA v2.
        </li><br />
        <li><code>email.*</code>: Configuration details for your SMTP email sending service.</li><br />
    </ul><br />
    <h4><strong>3.5. Post-Installation Steps &amp; Security</strong></h4><br />
    <ol><br />
        <li><strong>Secure <code>.env</code>:</strong> Ensure the <code>.env</code> file is never committed to version
            control.</li><br />
        <li><strong>Set DNS:</strong> Point your domain's A record to the server's IP address.</li><br />
        <li><br />
            <p><strong>Enable HTTPS:</strong> For production, install an SSL certificate. Using Certbot is recommended:
            </p><br />
            <pre><code class="language-bash">sudo apt install certbot python3-certbot-apache<br />
sudo certbot --apache</code></pre><br />
        </li><br />
    </ol><br />
    <hr /><br />
    <h2><strong>Part II: Guides &amp; Tutorials</strong></h2><br />
    <h3><strong>4. Core Concepts</strong></h3><br />
    <h4><strong>4.1. Architectural Overview (MVC-S)</strong></h4><br />
    <p>The project extends the traditional Model-View-Controller (MVC) pattern with a <strong>Service layer
            (MVC-S)</strong> to better organize business logic.</p><br />
    <ul><br />
        <li><strong>Models (<code>app/Models</code>)</strong>: Handle all direct database interactions. They are
            responsible for querying, inserting, and updating data.</li><br />
        <li><strong>Views (<code>app/Views</code>)</strong>: Contain the presentation logic (HTML). They receive data
            from controllers and render it for the user.</li><br />
        <li><strong>Controllers (<code>app/Controllers</code>)</strong>: Act as the bridge between Models and Views.
            They handle incoming HTTP requests, orchestrate calls to services, and pass data to the appropriate view.
        </li><br />
        <li><strong>Services (<code>app/Libraries</code>)</strong>: Contain the core business logic. This includes
            interacting with third-party APIs (Paystack, Gemini), processing complex data, and performing calculations.
            This keeps controllers lean and focused on handling the request-response cycle.</li><br />
    </ul><br />
    <h4><strong>4.2. The Request Lifecycle</strong></h4><br />
    <ol><br />
        <li>The request first hits <code>public/index.php</code>.</li><br />
        <li>CodeIgniter's routing (<code>app/Config/Routes.php</code>) matches the URL to a specific controller method.
        </li><br />
        <li>Any defined Filters (<code>app/Config/Filters.php</code>) are executed before the controller is called.</li>
        <br />
        <li>The Controller method is executed. It may validate input, call one or more Services, and retrieve data from
            Models.</li><br />
        <li>The Controller passes the prepared data to a View.</li><br />
        <li>The View is rendered into HTML and sent back to the browser as the final response.</li><br />
    </ol><br />
    <h4><strong>4.3. Service Container &amp; Dependency Injection</strong></h4><br />
    <p>The application uses CodeIgniter's service container to manage class instances. Core services are defined in
        <code>app/Config/Services.php</code>. This allows for easy instantiation and sharing of service objects
        throughout the application.</p><br />
    <ul><br />
        <li><strong>Registration:</strong> Custom services like <code>PaystackService</code> and
            <code>GeminiService</code> are registered as static methods in <code>app/Config/Services.php</code>.</li>
        <br />
        <li><strong>Usage:</strong> Services are accessed anywhere in the application using the <code>service()</code>
            helper function (e.g., <code>$geminiService = service('geminiService');</code>).</li><br />
    </ul><br />
    <h4><strong>4.4. Directory Structure Explained</strong></h4><br />
    <ul><br />
        <li><code>app/Commands</code>: Houses custom <code>spark</code> CLI commands, like the <code>train</code>
            command.</li><br />
        <li><code>app/Config</code>: Contains all application configuration files, including <code>Routes.php</code> and
            <code>Services.php</code>.</li><br />
        <li><code>app/Controllers</code>: Handles web requests.</li><br />
        <li><code>app/Database</code>: Contains database migrations and seeders for schema management.</li><br />
        <li><code>app/Entities</code>: Object-oriented representations of database table rows.</li><br />
        <li><code>app/Filters</code>: Middleware for route protection (e.g., authentication).</li><br />
        <li><code>app/Libraries</code>: This directory is used for the <strong>Service layer</strong>, containing all
            core business logic.</li><br />
        <li><code>app/Models</code>: Handles database interactions.</li><br />
        <li><code>app/Views</code>: Contains all HTML templates for the user interface.</li><br />
        <li><code>public/</code>: The web server's document root, containing the main <code>index.php</code> file and
            public assets.</li><br />
        <li><code>writable/</code>: Directory for logs, cache, and file uploads. Must be server-writable.</li><br />
    </ul><br />
    <h4><strong>4.5. Security Principles</strong></h4><br />
    <p>The application adheres to security best practices to protect against common vulnerabilities.</p><br />
    <ul><br />
        <li><strong>Public Webroot:</strong> The server's document root is set to the <code>public/</code> directory,
            preventing direct web access to application source code.</li><br />
        <li><strong>CSRF Protection:</strong> Cross-Site Request Forgery tokens are used on all POST forms to prevent
            malicious submissions.</li><br />
        <li><strong>XSS Filtering:</strong> All data rendered in views is escaped using <code>esc()</code> to prevent
            Cross-Site Scripting attacks.</li><br />
        <li><strong>Environment Variables:</strong> Sensitive information like API keys and database credentials are
            stored in the <code>.env</code> file, which is never committed to version control.</li><br />
        <li><strong>Query Builder &amp; Entities:</strong> All database queries use CodeIgniter's built-in methods,
            which automatically escape parameters to prevent SQL injection.</li><br />
    </ul><br />
    <h3><strong>5. Tutorial: Building Your First Feature</strong></h3><br />
    <p>This tutorial demonstrates how to build a simple &quot;Notes&quot; feature following the MVC-S pattern.</p><br />
    <h4><strong>5.1. Creating a New Route</strong></h4><br />
    <p>Open <code>app/Config/Routes.php</code> and add routes for viewing and creating notes.</p><br />
    <pre><code class="language-php">$routes-&gt;group('notes', ['filter' =&gt; 'auth'], static function ($routes) {<br />
    $routes-&gt;get('/', 'NoteController::index', ['as' =&gt; 'notes.index']);<br />
    $routes-&gt;post('create', 'NoteController::create', ['as' =&gt; 'notes.create']);<br />});</code></pre><br />
    <h4><strong>5.2. Building the Controller &amp; Service</strong></h4><br />
    <ol><br />
        <li><strong>Create the Controller</strong> using the command line:
            <code>php spark make:controller NoteController</code></li><br />
        <li>Edit <code>app/Controllers/NoteController.php</code>:</li><br />
    </ol><br />
    <pre><code class="language-php">&lt;?php<br />namespace App\Controllers;<br /><br />class NoteController extends BaseController<br />{<br />    public function index()<br />
    {<br />
        // For simplicity, we call the model directly here.<br />
        // In a complex app, this would go through a NoteService.<br />
        $noteModel = new \App\Models\NoteModel();<br />
        $data['notes'] = $noteModel-&gt;where('user_id', session()-&gt;get('userId'))-&gt;findAll();<br />
        return view('notes/index', $data);<br />
    }<br /><br />
    public function create()<br />
    {<br />
        $noteModel = new \App\Models\NoteModel();<br />
        $noteModel-&gt;save([<br />
            'user_id' =&gt; session()-&gt;get('userId'),<br />
            'content' =&gt; $this-&gt;request-&gt;getPost('content')<br />
        ]);<br />
        return redirect()-&gt;to(url_to('notes.index'))-&gt;with('success', 'Note saved!');<br />    }<br />}</code></pre>
    <br />
    <h4><strong>5.3. Interacting with the Database (Model &amp; Entity)</strong></h4><br />
    <ol><br />
        <li><strong>Create a Migration:</strong> <code>php spark make:migration create_notes_table</code></li><br />
        <li>Edit the new migration file in <code>app/Database/Migrations/</code> to define the table schema.</li><br />
        <li><strong>Create the Model:</strong> <code>php spark make:model NoteModel</code></li><br />
        <li><strong>Create the Entity:</strong> <code>php spark make:entity Note</code></li><br />
    </ol><br />
    <h4><strong>5.4. Displaying Data in a View</strong></h4><br />
    <p>Create a new file at <code>app/Views/notes/index.php</code>.</p><br />
    <pre><code class="language-php">&lt;?= $this-&gt;extend('layouts/default') ?&gt;<br />&lt;?= $this-&gt;section('content') ?&gt;<br />&lt;div class="container my-5"&gt;<br />
    &lt;h1&gt;My Notes&lt;/h1&gt;<br />
    &lt;!-- Form to create a new note --&gt;<br />
    &lt;form action="&lt;?= url_to('notes.create') ?&gt;" method="post"&gt;<br />
        &lt;?= csrf_field() ?&gt;<br />
        &lt;textarea name="content" class="form-control"&gt;&lt;/textarea&gt;<br />
        &lt;button type="submit" class="btn btn-primary mt-2"&gt;Save Note&lt;/button&gt;<br />
    &lt;/form&gt;<br /><br />
    &lt;!-- List existing notes --&gt;<br />
    &lt;ul class="list-group mt-4"&gt;<br />
        &lt;?php foreach($notes as $note): ?&gt;<br />
            &lt;li class="list-group-item"&gt;&lt;?= esc($note-&gt;content) ?&gt;&lt;/li&gt;<br />
        &lt;?php endforeach; ?&gt;<br />
    &lt;/ul&gt;<br />&lt;/div&gt;<br />
&lt;?= $this-&gt;endSection() ?&gt;</code></pre><br />
    <h3><strong>6. Feature Guides (Deep Dives)</strong></h3><br />
    <h4><strong>6.1. User Authentication</strong></h4><br />
    <ul><br />
        <li><strong>6.1.1. Registration &amp; Login Flow</strong>: Managed by <code>AuthController.php</code>, this
            feature handles user registration with validation and reCAPTCHA, credential verification for login, and
            session management.</li><br />
        <li><strong>6.1.2. Email Verification &amp; Password Resets</strong>: Upon registration, a unique token is
            generated and emailed to the user. <code>AuthController::verifyEmail()</code> handles this token. The
            password reset flow also uses a secure, expiring token sent via email.</li><br />
        <li><strong>6.1.3. Access Control with Filters</strong>: The <code>AuthFilter</code>
            (<code>app/Filters/AuthFilter.php</code>) is applied to routes in <code>app/Config/Routes.php</code> to
            protect pages that require a user to be logged in.</li><br />
    </ul><br />
    <h4><strong>6.2. Payment Gateway Integration</strong></h4><br />
    <ul><br />
        <li><strong>6.2.1. Configuration</strong>: The Paystack secret key is configured in the <code>.env</code> file
            (<code>PAYSTACK_SECRET_KEY</code>).</li><br />
        <li><strong>6.2.2. Initiating a Transaction</strong>: <code>PaymentsController::initiate()</code> collects the
            amount and email, creates a local record in the <code>payments</code> table with a <code>pending</code>
            status, and calls <code>PaystackService::initializeTransaction()</code>. This service sends the request to
            Paystack, which returns a unique authorization URL. The user is then redirected to this URL to complete the
            payment.</li><br />
        <li><strong>6.2.3. Verifying a Payment</strong>: After payment, Paystack redirects the user to the
            <code>callback_url</code> (<code>payment/verify</code>). <code>PaymentsController::verify()</code> retrieves
            the transaction reference and uses <code>PaystackService::verifyTransaction()</code> to confirm the payment
            status with Paystack. If successful, the local payment record is updated to <code>success</code> and the
            user's balance is updated within a database transaction.</li><br />
    </ul><br />
    <h4><strong>6.3. AI Service Integration</strong></h4><br />
    <ul><br />
        <li><strong>6.3.1. Generating Content</strong>: <code>GeminiController::generate()</code> is the core method. It
            prepares the user's prompt, adds contextual data from the memory system, and sends it to
            <code>GeminiService::generateContent()</code>. The service makes the API call to Google Gemini and returns
            the response.</li><br />
        <li><strong>6.3.2. Conversational Memory System</strong>: This advanced feature is managed by
            <code>MemoryService.php</code>. When a user submits a prompt, the service:<br />
            <ol><br />
                <li>Generates a vector embedding of the user's input using <code>EmbeddingService</code>.</li><br />
                <li>Performs a hybrid search (semantic vector search + keyword search) on past interactions stored in
                    the database.</li><br />
                <li>Constructs a context block from the most relevant past interactions.</li><br />
                <li>Prepends this context to the user's new prompt before sending it to the AI.</li><br />
                <li>After receiving a response, it stores the new question-and-answer pair and updates the relevance
                    scores of all memories.</li><br />
            </ol>
        </li><br />
        <li><strong>6.3.3. Handling Multimedia Inputs</strong>: Users can upload files via the AI Studio.
            <code>GeminiController::uploadMedia()</code> handles the file, and <code>GeminiController::generate()</code>
            processes it, converting the file to base64 and including it in the API request to Gemini, which is a
            multimodal model.</li><br />
    </ul><br />
    <h4><strong>6.4. Cryptocurrency Data Service</strong></h4><br />
    <ul><br />
        <li><strong>6.4.1. Querying Balances</strong>: <code>CryptoController::query()</code> calls
            <code>CryptoService::getBtcBalance()</code> or <code>getLtcBalance()</code>. The service makes an API call
            to a third-party blockchain explorer (e.g., blockchain.info, blockchair.com) and formats the response.</li>
        <br />
        <li><strong>6.4.2. Fetching Transaction Histories</strong>: Similarly, the controller calls
            <code>CryptoService::getBtcTransactions()</code> or <code>getLtcTransactions()</code>. The service fetches
            the transaction data and formats it into a readable structure for the view.</li><br />
    </ul><br />
    <h4><strong>6.5. Administrative Dashboard</strong></h4><br />
    <ul><br />
        <li><strong>6.5.1. User Management</strong>: <code>AdminController.php</code> provides methods to list, search,
            view details, update balances, and delete users. All actions are protected to ensure only administrators can
            perform them. Balance updates are handled within a database transaction for data integrity.</li><br />
        <li><strong>6.5.2. Sending Email Campaigns</strong>: <code>CampaignController.php</code> allows an administrator
            to compose an email that is then sent to every registered user in the <code>users</code> table. The process
            iterates through users and sends individual emails.</li><br />
    </ul><br />
    <hr /><br />
    <h2><strong>Part III: Technical Reference</strong></h2><br />
    <h3><strong>7. API Reference</strong></h3><br />
    <p>While this project is primarily a web application, its controllers can be adapted to serve a RESTful API. The
        following is a conceptual reference.</p><br />
    <h4><strong>7.1. Authentication</strong></h4><br />
    <p>Current authentication is session-based. For a stateless API, this would be replaced with a token-based system
        (e.g., JWT).</p><br />
    <h4><strong>7.2. Endpoints</strong></h4><br />
    <p>Endpoint definitions would follow standard REST conventions.</p><br />
    <ul><br />
        <li><strong><code>GET /resource</code></strong>: List all items of a resource.<br />
            <ul><br />
                <li><strong>Example:</strong> <code>GET /api/users</code> - Would be handled by
                    <code>AdminController::index()</code> to return a JSON list of users.</li><br />
            </ul>
        </li><br />
        <li><strong><code>POST /resource</code></strong>: Create a new resource item.<br />
            <ul><br />
                <li><strong>Example:</strong> <code>POST /api/users</code> - Would be handled by
                    <code>AuthController::store()</code> to create a new user.</li><br />
            </ul>
        </li><br />
        <li><strong><code>PUT /resource/{id}</code></strong>: Update a specific resource item.<br />
            <ul><br />
                <li><strong>Example:</strong> <code>PUT /api/users/{id}/balance</code> - Would be handled by
                    <code>AdminController::updateBalance()</code> to modify a user's balance.</li><br />
            </ul>
        </li><br />
        <li><strong><code>DELETE /resource/{id}</code></strong>: Delete a specific resource item.<br />
            <ul><br />
                <li><strong>Example:</strong> <code>DELETE /api/users/{id}</code> - Would be handled by
                    <code>AdminController::delete()</code>.</li><br />
            </ul>
        </li><br />
    </ul><br />
    <h4><strong>7.3. Rate Limiting</strong></h4><br />
    <p>CodeIgniter's Throttler filter can be applied to API routes in <code>app/Config/Routes.php</code> to prevent
        abuse by limiting the number of requests a user can make in a given time.</p><br />
    <h4><strong>7.4. Error Codes &amp; Responses</strong></h4><br />
    <p>The API would use standard HTTP status codes to indicate the outcome of a request.</p><br />
    <ul><br />
        <li><code>200 OK</code>: Request was successful.</li><br />
        <li><code>201 Created</code>: The resource was successfully created.</li><br />
        <li><code>400 Bad Request</code>: The request was malformed (e.g., validation error).</li><br />
        <li><code>401 Unauthorized</code>: Authentication failed or is required.</li><br />
        <li><code>403 Forbidden</code>: The authenticated user does not have permission.</li><br />
        <li><code>404 Not Found</code>: The requested resource does not exist.</li><br />
        <li><code>500 Internal Server Error</code>: A server-side error occurred.</li><br />
    </ul><br />
    <h3><strong>8. Command-Line Interface (CLI)</strong></h3><br />
    <p>CodeIgniter's CLI tool, <code>spark</code>, is used for various development and maintenance tasks.</p><br />
    <h4><strong>8.1. Overview of Custom Commands</strong></h4><br />
    <p>You can list all available commands by running <code>php spark</code>. Custom application commands are located in
        <code>app/Commands/</code>.</p><br />
    <h4><strong>8.2. <code>php spark train</code></strong></h4><br />
    <p>This is a custom command defined in <code>app/Commands/Train.php</code>.</p><br />
    <ul><br />
        <li><strong>Purpose:</strong> To run the AI text classification training service offline.</li><br />
        <li><strong>Action:</strong> It invokes <code>TrainingService::train()</code>, which reads a training dataset,
            processes it, trains a Naive Bayes classifier, and saves the serialized model files to the
            <code>writable/nlp/</code> directory. This ensures that performance-intensive model training does not impact
            the live web application.</li><br />
    </ul><br />
    <h4><strong>8.3. <code>php spark [another:command]</code></strong></h4><br />
    <p>Other essential built-in commands include:</p><br />
    <ul><br />
        <li><code>php spark migrate</code>: Applies pending database migrations.</li><br />
        <li><code>php spark db:seed &lt;SeederName&gt;</code>: Runs a database seeder to populate tables with data.</li>
        <br />
        <li><code>php spark make:controller &lt;Name&gt;</code>: Generates a new controller file.</li><br />
        <li><code>php spark optimize</code>: Caches configuration and file locations for improved performance.</li>
        <br />
    </ul><br />
    <h3><strong>9. Configuration Reference</strong></h3><br />
    <h4><strong>9.1. Application (<code>App.php</code>)</strong></h4><br />
    <p>Located at <code>app/Config/App.php</code>, this file contains the base configuration for the application,
        including the <code>baseURL</code>, <code>indexPage</code>, and <code>appTimezone</code>.</p><br />
    <h4><strong>9.2. Database (<code>Database.php</code>)</strong></h4><br />
    <p>Located at <code>app/Config/Database.php</code>, this file defines the connection parameters for your databases.
        The <code>default</code> group is used for the main application, while the <code>tests</code> group is used for
        PHPUnit testing.</p><br />
    <h4><strong>9.3. Custom Configurations (<code>AGI.php</code>, etc.)</strong></h4><br />
    <p>Custom configuration files are placed in <code>app/Config/Custom/</code>.</p><br />
    <ul><br />
        <li><code>AGI.php</code>: Contains all settings related to the AI service, including embedding models, hybrid
            search parameters, and memory logic (e.g., decay scores, context budget).</li><br />
        <li><code>Recaptcha.php</code>: Stores the site and secret keys for the Google reCAPTCHA service, which are
            loaded from the <code>.env</code> file.</li><br />
    </ul><br />
    <h3><strong>10. Testing</strong></h3><br />
    <h4><strong>10.1. Running the Test Suite</strong></h4><br />
    <p>The project is configured to use PHPUnit for testing. The test suite can be run using a Composer script.</p>
    <br />
    <pre><code class="language-bash">composer test</code></pre><br />
    <p>This command executes <code>phpunit</code> as defined in <code>composer.json</code> and uses the configuration
        from <code>phpunit.xml.dist</code>.</p><br />
    <h4><strong>10.2. Writing Unit Tests</strong></h4><br />
    <p>Unit tests focus on testing individual classes (like a Service or Model) in isolation. Test files should be
        placed in the <code>tests/</code> directory, mirroring the <code>app/</code> structure.</p><br />
    <h4><strong>10.3. Writing Feature Tests</strong></h4><br />
    <p>Feature tests are designed to test a full request-response cycle, simulating a user interacting with the
        application. They allow you to test controllers, views, and redirects together.</p><br />
    <hr /><br />
    <h2><strong>Part IV: Operations &amp; Community</strong></h2><br />
    <h3><strong>11. Deployment</strong></h3><br />
    <h4><strong>11.1. Production Server Setup</strong></h4><br />
    <p>The <code>setup.sh</code> script provides a complete, automated setup for a production-ready Ubuntu server,
        including installing the web server, PHP, database, and all project dependencies, as well as configuring a
        virtual host.</p><br />
    <h4><strong>11.2. Deployment Checklist</strong></h4><br />
    <ol><br />
        <li>Set <code>CI_ENVIRONMENT</code> to <code>production</code> in your <code>.env</code> file.</li><br />
        <li>Install production-only dependencies: <code>composer install --no-dev --optimize-autoloader</code>.</li>
        <br />
        <li>Run database migrations: <code>php spark migrate</code>.</li><br />
        <li>Optimize the application: <code>php spark optimize</code>.</li><br />
        <li>Ensure the web server's document root points to the <code>/public/</code> directory.</li><br />
        <li>Verify that <code>writable/</code> directory has the correct server permissions.</li><br />
    </ol><br />
    <h4><strong>11.3. Performance Optimization</strong></h4><br />
    <ul><br />
        <li><strong>Caching:</strong> The application can be configured to use various caching strategies. The
            <code>app/Config/Cache.php</code> file is set to use Redis as the primary handler with a file-based
            fallback.</li><br />
        <li><strong>Autoloader Optimization:</strong> The <code>--optimize-autoloader</code> flag in the composer
            install command creates an optimized class map for faster class loading.</li><br />
        <li><strong>Spark Optimize Command:</strong> <code>php spark optimize</code> caches configuration and speeds up
            the framework's file locator.</li><br />
        <li><strong>Database Queries:</strong> Use pagination (<code>paginate()</code>) for lists and select only
            necessary columns to keep database interactions efficient.</li><br />
    </ul><br />
    <h3><strong>12. Troubleshooting</strong></h3><br />
    <h4><strong>12.1. Frequently Asked Questions (FAQ)</strong></h4><br />
    <ul><br />
        <li><strong>Why did my payment fail?</strong> Ensure you have sufficient funds and that your payment provider
            (e.g., M-Pesa) is active. If the problem persists, contact support with your transaction reference.</li>
        <br />
        <li><strong>Why can't I log in after registering?</strong> You must click the verification link sent to your
            email address before you can log in.</li><br />
        <li><strong>Why is my AI query failing?</strong> This could be due to insufficient balance or a temporary issue
            with the Gemini API. Check your balance and try again after a few moments.</li><br />
    </ul><br />
    <h4><strong>12.2. Common Error Resolutions</strong></h4><br />
    <ul><br />
        <li><strong>&quot;Whoops! We hit a snag.&quot;</strong>: This is the generic production error message. Check the
            server logs at <code>writable/logs/</code> for the specific error details.</li><br />
        <li><strong>&quot;File upload failed.&quot;</strong>: This usually indicates a permissions issue. Ensure the
            <code>writable/uploads/</code> directory and its subdirectories are writable by the web server.</li><br />
        <li><strong>&quot;Could not send email.&quot;</strong>: Verify that your SMTP credentials in the
            <code>.env</code> file are correct and that your email provider is not blocking the connection.</li><br />
    </ul><br />
    <h4><strong>12.3. Logging &amp; Debugging</strong></h4><br />
    <ul><br />
        <li><strong>Log Location:</strong> All application logs are stored in <code>writable/logs/</code>, with a new
            file created for each day.</li><br />
        <li><strong>Log Levels:</strong> The logging sensitivity can be adjusted in <code>app/Config/Logger.php</code>.
            In a <code>development</code> environment, the threshold is low to capture all messages. In
            <code>production</code>, it is higher to only log errors and critical issues.</li><br />
    </ul><br />
    <h3><strong>13. Contributing</strong></h3><br />
    <h4><strong>13.1. Contribution Guidelines</strong></h4><br />
    <ol><br />
        <li>Fork the repository.</li><br />
        <li>Create a new feature branch (<code>git checkout -b feature/AmazingFeature</code>).</li><br />
        <li>Commit your changes (<code>git commit -m 'Add some AmazingFeature'</code>).</li><br />
        <li>Push to the branch (<code>git push origin feature/AmazingFeature</code>).</li><br />
        <li>Open a Pull Request.</li><br />
    </ol><br />
    <h4><strong>13.2. Code Style (PSR-12)</strong></h4><br />
    <p>The project enforces the PSR-12 coding standard. All contributions must adhere to this standard.</p><br />
    <h4><strong>13.3. Submitting a Pull Request</strong></h4><br />
    <p>Before submitting a pull request, ensure that your code is well-documented, follows the project's architectural
        patterns (MVC-S), and that all existing tests pass.</p><br />
    <h3><strong>14. Appendices</strong></h3><br />
    <h4><strong>14.1. Glossary of Terms</strong></h4><br />
    <ul><br />
        <li><strong>MVC-S:</strong> Model-View-Controller-Service, an architectural pattern that separates database
            logic (Model), presentation (View), request handling (Controller), and business logic (Service).</li><br />
        <li><strong>Service:</strong> A class in the <code>app/Libraries</code> directory that contains reusable
            business logic.</li><br />
        <li><strong>Entity:</strong> A class that represents a single row from a database table, allowing for
            object-oriented interaction with data.</li><br />
        <li><strong>Embedding:</strong> A numerical vector representation of text, used for semantic understanding and
            similarity searches in the AI memory system.</li><br />
        <li><strong>Pay-as-you-go:</strong> A pricing model where users pay only for the services they consume, rather
            than a fixed subscription fee.</li><br />
    </ul><br />
    <h4><strong>14.2. Changelog &amp; Release History</strong></h4><br />
    <p><em>(This section would be maintained with a list of versions and the changes introduced in each release.)</em>
    </p><br />
    <ul><br />
        <li><strong>v1.0.0 (Initial Release)</strong><br />
            <ul><br />
                <li>Core features implemented: User Authentication, Paystack Payments, Gemini AI Integration, Crypto
                    Data Service, Admin Dashboard.</li><br />
            </ul>
        </li><br />
    </ul><br />
    <hr /><br />
    <h2><strong>Part V: Documentation Maintenance Guide</strong></h2><br />
    <h3><strong>15. A Guide for the Project Owner</strong></h3><br />
    <p>This section serves as the standard operating procedure (SOP) for maintaining this project's documentation. Its
        purpose is to ensure accuracy, consistency, and longevity, whether updates are performed by you or an AI
        assistant.</p><br />
    <h4><strong>15.1. The Philosophy of Living Documentation</strong></h4><br />
    <p>Treat this documentation as a core part of the codebase. It should evolve in lockstep with every feature change,
        bug fix, or architectural adjustment. An undocumented change is an incomplete change. The goal is to ensure that
        a new developer, or you in six months, can understand the <em>what</em>, <em>how</em>, and <em>why</em> of the
        system just by reading this document.</p><br />
    <h4><strong>15.2. Your Role vs. the AI's Role</strong></h4><br />
    <ul><br />
        <li><strong>The AI's Role (Efficiency &amp; Accuracy):</strong> The AI is the primary documentation writer. It
            excels at systematically analyzing code changes (<code>git diff</code>), identifying affected components,
            and generating accurate, detailed descriptions based on the established structure. It is responsible for the
            heavy lifting of drafting content.</li><br />
        <li><strong>Your Role (Clarity &amp; Context):</strong> Your role is that of an editor and strategist. You
            review the AI-generated content for clarity, human readability, and high-level context that the code alone
            cannot provide. You ensure the &quot;why&quot; behind a change is captured, not just the &quot;what.&quot;
        </li><br />
    </ul><br />
    <h4><strong>15.3. The Documentation Update Workflow</strong></h4><br />
    <p>This workflow applies to any code changes committed to the main branch.</p><br />
    <p><strong>A. For Simple Changes (e.g., typos, clarifications, minor updates):</strong></p><br />
    <ol><br />
        <li><strong>Identify:</strong> Locate the relevant section in the documentation file.</li><br />
        <li><strong>Edit:</strong> Make the necessary correction or addition directly.</li><br />
        <li><strong>Commit:</strong> Commit the change with a clear message, prefixed with <code>docs:</code>.<br />
            <ul><br />
                <li><em>Example:</em> <code>docs: Correct typo in Installation section 3.4</code></li><br />
            </ul>
        </li><br />
    </ol><br />
    <p><strong>B. For Complex Changes (e.g., new features, architectural modifications):</strong></p><br />
    <ol><br />
        <li><strong>Identify the Scope:</strong> Use the procedure in section <strong>15.4</strong> to identify all
            changed files and map them to the corresponding sections of this documentation.</li><br />
        <li><strong>Draft Updates (or Prompt the AI):</strong> For each affected section, draft the new content. If
            using the AI, provide it with the list of changed files and instruct it to update the documentation
            accordingly.<br />
            <ul><br />
                <li><em>Example Prompt for AI:</em> &quot;A new email campaign feature has been added. The following
                    files were created or modified: <code>CampaignController.php</code>, <code>CampaignModel.php</code>,
                    <code>create.php</code> view, and the routes were updated. Please update the documentation,
                    including a new sub-section in the Feature Guides (6.5.2) and updating the directory
                    structure.&quot;</li><br />
            </ul>
        </li><br />
        <li><strong>Update Table of Contents:</strong> If new sections or sub-sections were added, update the table of
            contents at the beginning of the document.</li><br />
        <li><strong>Update Changelog:</strong> Follow the procedure in section <strong>15.5</strong> to add an entry to
            the Changelog and determine if the version number needs to be updated.</li><br />
        <li><strong>Review and Commit:</strong> Read through all changes from the perspective of someone unfamiliar with
            the update. Is it clear? Is anything missing? Once satisfied, commit the changes.</li><br />
    </ol><br />
    <h4><strong>15.4. Procedure: How to Review the Codebase for Changes</strong></h4><br />
    <p>The most efficient way to find what needs documenting is by analyzing the difference between your feature branch
        and the main branch using Git.</p><br />
    <ol><br />
        <li>
            <p><strong>Generate a File List:</strong> From your feature branch, run the following command to get a list
                of all files that have been added (<code>A</code>), modified (<code>M</code>), or renamed
                (<code>R</code>):</p><br />
            <pre><code class="language-bash">git diff main --name-status  | git diff main --name-status &gt; changed_files.txt<br />git diff main  | git diff main &gt; code_changes.diff</code></pre>
            <br />
        </li><br />
        <li>
            <p><strong>Map Files to Documentation Sections:</strong> Use the output from the command above and this
                checklist to determine which parts of the documentation to review and update.</p><br />
        </li><br />
    </ol><br />
    <table><br />
        <thead><br />
            <tr><br />
                <th style="text-align: left;">If This File/Directory Changed...</th><br />
                <th style="text-align: left;">...Then Review and Update These Documentation Sections</th><br />
            </tr><br />
        </thead><br />
        <tbody><br />
            <tr><br />
                <td style="text-align: left;"><code>setup.sh</code> or <code>.env</code> (new variables)</td><br />
                <td style="text-align: left;"><strong>3. Installation</strong> (Prerequisites, Automated/Manual Setup,
                    Environment Config)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Config/Routes.php</code></td><br />
                <td style="text-align: left;"><strong>5. Tutorial</strong>, <strong>6. Feature Guides</strong>,
                    <strong>7. API Reference</strong> (for new endpoints/URLs)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Controllers/*</code></td><br />
                <td style="text-align: left;"><strong>6. Feature Guides</strong> (logic for a specific feature),
                    <strong>7. API Reference</strong> (endpoint behavior)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Libraries/*</code> (Services)</td><br />
                <td style="text-align: left;"><strong>4. Core Concepts</strong> (if a fundamental service changed),
                    <strong>6. Feature Guides</strong> (detailed business logic)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Models/*</code> or <code>app/Entities/*</code></td><br />
                <td style="text-align: left;"><strong>5. Tutorial</strong>, <strong>6. Feature Guides</strong> (how data
                    is handled for a feature)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Database/Migrations/*</code></td><br />
                <td style="text-align: left;"><strong>5. Tutorial</strong>, <strong>6. Feature Guides</strong> (mention
                    new database tables/columns)</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Commands/*</code></td><br />
                <td style="text-align: left;"><strong>8. Command-Line Interface (CLI)</strong></td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Config/Custom/*</code></td><br />
                <td style="text-align: left;"><strong>9. Configuration Reference</strong> (document new custom settings)
                </td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>app/Views/*</code></td><br />
                <td style="text-align: left;">Usually doesn't require a doc change unless a major new UI feature is
                    introduced.</td><br />
            </tr><br />
            <tr><br />
                <td style="text-align: left;"><code>composer.json</code> (new dependencies)</td><br />
                <td style="text-align: left;"><strong>1.4. Technology Stack</strong>, <strong>3.1. Server
                        Requirements</strong></td><br />
            </tr><br />
        </tbody><br />
    </table><br />
    <h4><strong>15.5. Procedure: Updating the Changelog and Managing Releases</strong></h4><br />
    <p>This project follows <strong>Semantic Versioning (SemVer)</strong>: <code>MAJOR.MINOR.PATCH</code>.</p><br />
    <ul><br />
        <li><strong><code>PATCH</code> (e.g., 1.0.0 -&gt; 1.0.1):</strong> For backward-compatible bug fixes or
            documentation corrections. No new features.</li><br />
        <li><strong><code>MINOR</code> (e.g., 1.0.1 -&gt; 1.1.0):</strong> For new features or functionality added in a
            backward-compatible manner. This will be your most common version bump.</li><br />
        <li><strong><code>MAJOR</code> (e.g., 1.1.0 -&gt; 2.0.0):</strong> For incompatible API changes or significant
            architectural shifts that break backward compatibility.</li><br />
    </ul><br />
    <p><strong>Changelog Update Criteria:</strong></p><br />
    <ol><br />
        <li>
            <p><strong>Locate the Changelog:</strong> Find section <strong>14.2. Changelog &amp; Release
                    History</strong>.</p><br />
        </li><br />
        <li>
            <p><strong>Create a New Entry:</strong> For every set of changes merged to <code>main</code>, add a new
                version heading. If it's the first change for a new version, create the heading; otherwise, add to the
                existing one.</p><br />
        </li><br />
        <li>
            <p><strong>Format the Entry:</strong> Use the following structure, inspired by <a
                    href="https://keepachangelog.com/en/1.0.0/">Keep a Changelog</a>. Only include the categories you
                need for that release.</p><br />
            <pre><code class="language-markdown">**v[MAJOR.MINOR.PATCH] - YYYY-MM-DD**<br />
<br />
### Added<br />
- New feature A.<br />
- New feature B.<br />
<br />
### Changed<br />
- Updated user dashboard for better UX.<br />
- Switched payment provider logic.<br />
<br />
### Fixed<br />
- Resolved login bug affecting Safari users.<br />
<br />
### Removed<br />
- Deprecated the old reporting feature.</code></pre><br />
        </li><br />
        <li>
            <p><strong>Be Concise and User-Focused:</strong> Describe the <em>impact</em> of the change, not just the
                code that was altered.</p><br />
            <ul><br />
                <li><strong>Good:</strong> &quot;Added email notifications for successful payments.&quot;</li><br />
                <li><strong>Bad:</strong> &quot;Modified the <code>PaymentsController</code> and created a
                    <code>PaymentNotification</code> class.&quot;</li><br />
            </ul><br />
        </li><br />
    </ol>
</div>

<?= $this->endSection() ?>