CodeIgniter 4 Production Deployment Rules & Workflow
----------------------------------------------------

 1\. AI Agent Workflow

1.  Analyze Request: Deconstruct user requests into logical, sequential file modifications that align with the CodeIgniter 4 file structure.
2.  Propose Changes: Before implementation, clearly state the intended modifications and the files to be affected. For example: "I will add a description column to the products table via a new migration, update the ProductModel and its Entity, modify the Products controller, and add the field to the productform view."
3.  Adhere to Architectural Principles: All modifications must respect the CodeIgniter 4 file structure and its separation of concerns. (See: [Application Structure](https://codeigniter.com/userguide/concepts/structure.html))
       Configuration (app/Config/): Manage all core application settings here. Modifications should target specific files like App.php, Database.php, or Routes.php. (See: [Configuration Guide](https://codeigniter.com/userguide/general/configuration.html))
           Context: Configuration values are accessed using the config() function. For example, to get the application's base URL, you would use:
            
                $baseUrl = config('App')->baseURL;
                
            
       Controllers (app/Controllers/): Handle HTTP requests. Keep them lean and focused on request/response flow. All business logic must be delegated to Models or Libraries/Services. (See: [Controllers Guide](https://codeigniter.com/userguide/incoming/controllers.html))
           Context: A basic controller structure:
            
                namespace App\Controllers;
                
                class Home extends BaseController
                {
                    public function index(): string
                    {
                        return view('welcomemessage');
                    }
                }
                
            
       Models (app/Models/): Manage all database interactions. Use CodeIgniter's Model class, Query Builder, or Entities for a robust data layer. Never place direct database queries in Controllers. (See: [Models Guide](https://codeigniter.com/userguide/models/model.html) and [Entities Guide](https://codeigniter.com/userguide/models/entities.html))
           Context: A basic Model setup that uses an Entity class:
            
                // app/Models/UserModel.php
                namespace App\Models;
                use CodeIgniter\Model;
                class UserModel extends Model
                {
                    protected $table         = 'users';
                    protected $primaryKey    = 'id';
                    protected $returnType    = 'App\Entities\User'; // Use the Entity class
                    protected $allowedFields = ['name', 'email'];
                }
                
                // app/Entities/User.php
                namespace App\Entities;
                use CodeIgniter\Entity\Entity;
                class User extends Entity
                {
                    // You can add custom business logic for a user record here
                }
                
            
       Views (app/Views/): Handle presentation. They should contain minimal PHP and primarily display data passed from controllers. All dynamic data must be escaped with esc() to prevent XSS attacks. For efficiency and maintainability, use View Layouts for page structure and View Cells for reusable components. (See: [View Layouts](https://codeigniter.com/userguide/outgoing/viewlayouts.html) and [View Cells](https://codeigniter.com/userguide/outgoing/viewcells.html)). Under no circumstances should an entire view file's content be wrapped in <![CDATA[...]]> tags.
           Context: Using a View Layout:
            
                // app/Views/layouts/default.php (The Layout)
                <!DOCTYPE html>
                <html>
                <head><title>My App</title></head>
                <body>
                    <?= $this->renderSection('content') ?>
                </body>
                </html>
                
                // app/Views/somepage.php (The View)
                <?= $this->extend('layouts/default') ?>
                <?= $this->section('content') ?>
                    <h1>Welcome to my page!</h1>
                    <p>Hello, <?= esc($username) ?>.</p>
                <?= $this->endSection() ?>
                
            
       Database Migrations & Seeds (app/Database/): All schema changes must be handled through Migration files. Initial data for development or testing should be managed with Seeder files. (See: [Migrations Guide](https://codeigniter.com/userguide/dbmgmt/migration.html) and [Seeding Guide](https://codeigniter.com/userguide/dbmgmt/seeding.html))
           Context: A basic Migration to create a table:
            
                namespace App\Database\Migrations;
                use CodeIgniter\Database\Migration;
                class CreateUsersTable extends Migration
                {
                    public function up()
                    {
                        $this->forge->addField([ / ... fields ... / ]);
                        $this->forge->addKey('id', true);
                        $this->forge->createTable('users');
                    }
                    public function down()
                    {
                        $this->forge->dropTable('users');
                    }
                }
                
            
       Filters (app/Filters/): Use filters for cross-cutting concerns like security (csrf, honeypot), authentication, or rate limiting (throttler). Custom business logic does not belong in filters. (See: [Controller Filters](https://codeigniter.com/userguide/incoming/filters.html))
           Context: Enabling a filter for all routes:
            
                // app/Config/Filters.php
                public array $globals = [
                    'before' => [
                        'csrf',
                        // 'honeypot',
                    ],
                    'after' => [
                        'toolbar',
                        // 'honeypot',
                    ],
                ];
                
            
       Helpers (app/Helpers/): Contain stateless, procedural helper functions. (See: [Helpers Guide](https://codeigniter.com/userguide/general/helpers.html))
       Language (app/Language/): Store language-specific strings for internationalization. (See: [Localization Guide](https://codeigniter.com/userguide/outgoing/localization.html))
       Libraries (app/Libraries/): House custom classes that provide reusable application logic. For better scalability and testability, these should be managed as Services. (See: [Services Guide](https://codeigniter.com/userguide/concepts/services.html))
           Context: Defining a custom class as a service:
            
                // app/Config/Services.php
                namespace Config;
                use CodeIgniter\Config\BaseService;
                use App\Libraries\MyCustomClass;
                
                class Services extends BaseService
                {
                    public static function myCustomService($getShared = true)
                    {
                        if ($getShared) {
                            return static::getSharedInstance('myCustomService');
                        }
                        return new MyCustomClass();
                    }
                }
                
            
4.  Prioritize Framework Functions: Before writing custom helper functions or libraries, exhaust all built-in CodeIgniter functionalities. This approach reduces code footprint and leverages the framework's stability.
5.  File Creation & Modification:
       File Creation: All new boilerplate files—including Controllers, Models, Migrations, Seeds, and Entities—must be generated using the appropriate php spark command (e.g., php spark make:controller Products --restful, php spark make:model Product --table products --return entity, php spark make:migration CreateProductsTable). Manual creation is forbidden. (See: [CLI Generators](https://codeigniter.com/userguide/cli/cligenerators.html))
       Preserve Boilerplate Structure: The file structure, class name, namespace, and boilerplate methods generated by php spark must be preserved. Only add code to implement the specific task.
       File Modification: When providing code, always specify the full path from the project root (e.g., FILE: app/Controllers/Home.php) and provide the complete, updated file content.
6.  Final Confirmation: Before concluding, confirm that all changes have been applied in accordance with the established rules.

 2\. Environment Configuration

1.  The CIENVIRONMENT variable in the .env file must be set to production.
2.  The production .env file must not be committed to version control. A template file, env, should be used as a placeholder.
3.  All environment-specific variables (database credentials, API keys, secrets) must be defined in the .env file and accessed via env(). (See: [Environments Guide](https://codeigniter.com/userguide/general/environments.html))
       Context: Example .env configuration:
        
            CIENVIRONMENT = production
            app.baseURL = 'https://example.com/'
            database.default.hostname = localhost
            database.default.database = myproddb
            database.default.username = produser
            database.default.password = 'p@$$w0rd'
            database.default.DBDriver = MySQLi
            
        
4.  The application's base URL (app.baseURL) must be correctly set in the .env file with a trailing slash (e.g., app.baseURL = 'https://example.com/').
5.  Prioritize Core Configuration: Architectural settings (e.g., filter aliases, module discovery, validation rule sets) must be made directly in the relevant app/Config/ file (e.g., Filters.php, Modules.php, Validation.php). The .env file is only for variables that change per environment.

 3\. Web Server & Filesystem Configuration

1.  The web server's document root must point to the project's /public directory.
2.  The app, system, and writable directories must be located outside of the web server's document root for security. (See: [Deployment Guide](https://codeigniter.com/userguide/installation/deployment.html))
       Context: An Nginx server block should point to the public folder:
        
            server {
                listen 80;
                servername example.com;
                root /path/to/project/public;  Document root is the public directory
                index index.php;
            
                location / {
                    tryfiles $uri $uri/ /index.php?$querystring;
                }
            }
            
        
3.  The writable directory and its subdirectories must have write permissions for the web server user.
4.  Development-only files and directories (tests/, phpunit.xml.dist, spark, preload.php) must be removed from the production server.

 4\. Code & Dependency Management

1.  Before deployment, execute composer install --no-dev --optimize-autoloader to install only production dependencies and optimize the autoloader.
2.  All unused modules in app/Config/Modules.php should be disabled to reduce auto-discovery overhead. (See: [Modules Guide](https://codeigniter.com/userguide/general/modules.html))
3.  Adhere to Coding Standards: All PHP code must follow the PSR-12 standard. Use modern PHP features like strict types (declare(stricttypes=1);), typed properties, and return type declarations. (See: [PSR Guide](https://codeigniter.com/userguide/intro/psr.html))
4.  Leverage Service Injection: Custom classes providing services must be defined in app/Config/Services.php for efficient dependency management and testability. (See: [Services Guide](https://codeigniter.com/userguide/concepts/services.html))
5.  API Development: Use CodeIgniter’s API Response Trait to standardize API responses with methods like respond(), fail(), and appropriate HTTP status codes. (See: [API Responses Guide](https://codeigniter.com/userguide/outgoing/apiresponses.html))
       Context: Standardizing API responses in a controller:
        
            // In a controller that uses the trait: use CodeIgniter\API\ResponseTrait;
            public function show($id)
            {
                $user = $this->model->find($id);
                if ($user === null) {
                    return $this->failNotFound('User not found');
                }
                return $this->respond($user);
            }
            
        

 5\. Error Handling & Logging

1.  Detailed error reporting to the browser must remain disabled as configured in app/Config/Boot/production.php.
2.  Exception logging must be enabled by setting $log to true in app/Config/Exceptions.php. (See: [Error Handling Guide](https://codeigniter.com/userguide/general/errors.html))
3.  The logging threshold in app/Config/Logger.php must be configured for production (e.g., a threshold of 4 to log only critical errors). (See: [Logging Guide](https://codeigniter.com/userguide/general/logging.html))
       Context: Setting the log level:
        
            // app/Config/Logger.php
            class Logger extends BaseConfig
            {
                // 0 = Disables logging, 1 = Emergency, 2 = Alert, 3 = Critical, 4 = Error
                public int $threshold = 4;
            }
            
        
4.  Custom error pages for common HTTP status codes (e.g., 404, 503) must be created in app/Views/errors/html/.

 6\. Performance Optimization

1.  If all routes are explicitly defined in app/Config/Routes.php, disable "Auto Routing" by setting $autoRoute to false in app/Config/Routing.php. (See: [Routing Guide](https://codeigniter.com/userguide/incoming/routing.html))
2.  For frequently accessed, infrequently changed data, enable and configure a production-ready caching driver like Redis or Memcached in app/Config/Cache.php. (See: [Caching Guide](https://codeigniter.com/userguide/libraries/caching.html))
       Context: Configuring Redis as the cache handler:
        
            // app/Config/Cache.php
            public string $handler = 'redis';
            public array $redis = [
                'host'     => '127.0.0.1',
                'password' => null,
                'port'     => 6379,
                'timeout'  => 0,
                'database' => 0,
            ];
            
        
3.  As part of the deployment script, use the php spark optimize command to enable config and file locator caching.
4.  Optimize Database Queries: To reduce memory footprint, select only the necessary columns using $builder->select('col1, col2'). Avoid using findAll() where a more specific method like find() or a custom query with a limit can be used. (See: [Query Builder Guide](https://codeigniter.com/userguide/database/querybuilder.html))
       Context: A selective query is more efficient than findAll():
        
            // Inefficient for a single record:
            $users = $userModel->findAll(); // Fetches all columns for all users
            
            // Efficient:
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $builder->select('id, name, email')->where('status', 'active');
            $users = $builder->get()->getResult();
            
        

 7\. Security Mandates (Non-Negotiable)

1.  CSRF Protection: Cross-Site Request Forgery (CSRF) protection must be enabled globally in app/Config/Filters.php. (See: [Security Guide](https://codeigniter.com/userguide/libraries/security.html))
       Context: Add the CSRF token to any web form:
        
            <form action="/profile" method="post">
                <?= csrffield() ?>
                <!-- other form fields -->
            </form>
            
        
2.  Content Security Policy (CSP): To mitigate Cross-Site Scripting (XSS) attacks, CSP must be enabled ($CSPEnabled = true in app/Config/App.php) and configured in app/Config/ContentSecurityPolicy.php. (See: [CSP Guide](https://codeigniter.com/userguide/outgoing/csp.html))
3.  Database Interactions: All database queries must use the Query Builder or prepared statements to prevent SQL injection. Raw queries are forbidden.
4.  Input Validation: All user-submitted data ($GET, $POST, etc.) must be validated with the CodeIgniter Validation library before being processed. (See: [Validation Library](https://codeigniter.com/userguide/libraries/validation.html))
       Context: Validating POST data in a controller:
        
            $rules = [
                'username' => 'required|maxlength[30]',
                'email'    => 'required|validemail',
            ];
            
            if (! $this->validate($rules)) {
                // Return with validation errors
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // Data is valid, proceed...
            $model->save($this->request->getPost());
            
        
5.  Output Escaping: All data rendered in HTML views must be escaped using the esc() function to prevent XSS attacks. Do not wrap files or HTML blocks in CDATA sections.
6.  Session Security: When using HTTPS, the cookie.secure and cookie.httponly settings in your .env file must be true. Store session data in a database for enhanced security. (See: [Sessions Library](https://codeigniter.com/userguide/libraries/sessions.html))
       Context: Configuring the session to use a database:
        
            // app/Config/Session.php
            public string $driver = \CodeIgniter\Session\Handlers\DatabaseHandler::class;
            public string $savePath = 'sessions'; // Name of the database table
            
        
7.  Rate Limiting: To prevent brute-force attacks, the Throttler filter must be enabled and configured on relevant routes. (See: [Throttler Library](https://codeigniter.com/userguide/libraries/throttler.html))
       Context: Applying the throttler to login routes:
        
            // app/Config/Routes.php
            $routes->group('account', static function ($routes) {
                $routes->post('login', 'AuthController::login', ['filter' => 'throttler']);
                // ... other routes
            });
            
        

 8\. Development & Testing

1.  Unit & Feature Testing: New business logic in models, libraries, or services should be accompanied by corresponding tests in the tests/ directory. Use CodeIgniter's test case classes and php spark to generate test files. (See: [Testing Guide](https://codeigniter.com/userguide/testing/index.html))
       Context: A basic feature test to check a page:
        
            // tests/feature/HomeTest.php
            namespace App;
            use CodeIgniter\Test\CIUnitTestCase;
            use CodeIgniter\Test\FeatureTestTrait;
            
            class HomeTest extends CIUnitTestCase
            {
                use FeatureTestTrait;
            
                public function testHomePageIsVisible()
                {
                    $result = $this->call('get', '/');
                    $result->assertStatus(200);
                    $result->assertSee('Welcome to CodeIgniter');
                }
            }

9\. Authentication Guidelines

1.  Simplicity and Readability:
       Controllers: Keep authentication controller methods concise, focusing on request handling, validation, and delegating data persistence to models.
       Form Helpers: Utilize CodeIgniter's helper(['form']) for streamlined form management.
       Session Management: Implement session handling using session()->set() for login and session()->destroy() for logout, ensuring clear state management.
2.  Efficiency:
       Model Usage: Leverage CodeIgniter's Model class for all user-related database operations (e.g., save, where, first) to ensure efficient data access.
       Password Hashing: Always use passwordhash() for storing passwords and passwordverify() for checking them, ensuring strong security and efficient verification.
       Redirection: Employ redirect()->to() for efficient and clear navigation after authentication actions.
3.  User-Friendliness:
       Validation Feedback: Provide immediate and clear validation error messages to the user using $validation->listErrors() in views.
       Form Repopulation: Use old('fieldname') to automatically repopulate form fields after submission errors, improving user experience.
       Status Messages: Display success and error messages using session flashdata (session()->getFlashdata('success'), session()->getFlashdata('error')) for clear user feedback.
       Dynamic Navigation: Implement conditional rendering in views to show appropriate navigation links (login/register vs. dashboard/logout) based on the user's authentication status.

 10\. Database Migration Guidelines

1.  Clarity and Structure:
       Generation: Always generate migration files using php spark make:migration [MigrationName] to ensure consistent naming and boilerplate.
       Schema Definition: Define table schemas clearly within the up() method, specifying data types, constraints (e.g., INT, VARCHAR, unique, autoincrement), and keys.
       Timestamps: Include createdat and updatedat fields in user-related tables, and configure the corresponding model with useTimestamps = true for automatic management.
2.  Efficiency:
       Forge Methods: Utilize CodeIgniter's forge methods (addField, addKey, createTable, dropTable) for efficient and standardized database schema manipulation.
3.  Simplicity:
       Focused Migrations: Keep each migration file focused on a single, logical schema change to maintain simplicity and ease of management.