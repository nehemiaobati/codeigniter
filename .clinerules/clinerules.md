Acknowledged. The objective is to update the `.clinerules/clinerules.md` file to make the rule regarding JavaScript URL generation more direct, specific, and unambiguous, without including code snippets in the final output. The change will clarify the distinct use cases for `route_to()` and `url_to()`.

The following is the complete, updated content for the `.clinerules/clinerules.md` file.

***

# Project Constitution: CodeIgniter 4 Development Standards

This document is the **single source of truth** for all architectural, coding, and security standards for this project. Its purpose is to ensure a streamlined, maintainable, and secure application. Adherence is mandatory for all contributors, human and AI.

### Guiding Principles
*   **Clarity over Cleverness:** Code must be simple, readable, and self-documenting.
*   **Security is Not Optional:** Every line of code must be written with security as a primary concern.
*   **Consistency is Key:** The framework and these rules provide one right way to build features. Follow it.
*   **Fat Services, Skinny Controllers:** Business logic belongs in services, not controllers.

---

### **Part 1: The Four Layers of the Application**

The project follows a strict **Model-View-Controller-Service (MVC-S)** architecture.

#### **1.1. Controllers (`app/Controllers/`)**
*   **DO:** Orchestrate the request-response cycle. Call Services and Models. Return a Response or a Redirect.
*   **DON'T:** Contain business logic. Access the database directly. Contain complex calculations.

#### **1.2. Models (`app/Models/`)**
*   **DO:** Handle all database interactions. Use the Query Builder and Entities.
*   **DON'T:** Contain business logic. Be called directly from a View.

#### **1.3. Services (`app/Libraries/`)**
*   **DO:** Contain all business logic (e.g., payment processing, API interactions). Be reusable. Be registered in `app/Config/Services.php`.
*   **DON'T:** Handle HTTP-specific tasks like reading `POST` data. That is the Controller's job.

#### **1.4. Views (`app/Views/`)**
*   **DO:** Display data passed from a Controller. Contain minimal presentation logic (loops, conditionals). Escape all output with `esc()`.
*   **DON'T:** Perform database queries. Contain business logic.

#### **1.5. Database (`app/Database/`)**
*   **Role:** Define and manage the database schema.
*   **Rules:**
    *   All schema changes MUST be managed via Migration files.
    *   Initial or test data MUST be handled by Seeder files.
    *   Directly altering the database schema outside of migrations is strictly FORBIDDEN.

---

### **Part 2: The Request & Response Protocol**

This section defines the mandatory flow for user interactions.

#### **2.1. Routing: Named Routes are Law**
*   **Rule 1:** Every route in `app/Config/Routes.php` MUST be assigned a unique name (e.g., `['as' => 'users.profile']`).
*   **Rule 2:** All URLs in the application (views, redirects) MUST be generated using `url_to('route.name')`. Hardcoded URLs (`/users/profile`) are strictly **FORBIDDEN**.

#### **2.2. The 3 Steps of a Form Submission (Post/Redirect/Get)**
This **Post/Redirect/Get (PRG)** pattern is mandatory for all `POST` requests.

*   **Step 1: The `POST` Action (Controller):**
    *   The controller method processes the form data and calls the necessary services/models.
    *   The method **MUST NOT** return a `view()`.

*   **Step 2: The `Redirect` with Flash Data:**
    *   After processing, the controller method MUST store user-facing messages in the session as "flash data" (e.g., `session()->setFlashdata('success', 'Operation successful!')`).
    *   Standard keys are required: `success`, `error`, `warning`, `info`.
    *   The method MUST conclude by returning a `redirect()` response (e.g., `return redirect()->to(url_to('users.show', $id));`).

*   **Step 3: The `GET` Display:**
    *   The browser follows the redirect to a new URL.
    *   The corresponding controller method for the `GET` request renders a view.
    *   This view reads the flash data from the session and displays the message using the `flash_messages.php` partial.

#### **2.3. Filters: The Gatekeepers**
*   Filters (`app/Filters/`) MUST be used for all cross-cutting concerns, primarily for security and access control.
*   **Examples:** `AuthFilter` to protect logged-in areas, `AdminFilter` for admin-only pages, `BalanceFilter` to protect paid service routes.

---

### **Part 3: Code, Security, & Performance Mandates**

These are non-negotiable rules for all code.

#### **3.1. Code Quality & Documentation**
*   **PSR-12 & Strict Types:** All PHP files MUST be PSR-12 compliant and start with `declare(strict_types=1);`.
*   **PHPDoc Blocks:** Every class, property, and method MUST have a complete and accurate PHPDoc block. There are no exceptions. This includes `@param`, `@return`, and clear descriptions. For Entities, a full list of `@property` tags is required.

#### **3.2. Security**
*   **Output Escaping:** All dynamic data rendered in a view MUST be escaped with `esc()` to prevent XSS. Example: `<?= esc($user->name) ?>`.
*   **CSRF Protection:** CSRF protection MUST be enabled globally. All `POST` forms MUST include `csrf_field()`.
*   **Database Safety:** The Query Builder or Entities are the ONLY permitted methods for database interaction to prevent SQL injection.
*   **Input Validation:** All user-supplied data (`POST`, `GET`, etc.) MUST be validated using the Validation library before use.
*   **Throttler:** The Throttler MUST be enabled on authentication and password reset routes to prevent brute-force attacks.

#### **3.3. Transactional Integrity: All or Nothing**
*   **Rule 1:** All operations involving multiple database `write` actions (INSERT, UPDATE, DELETE) that are logically connected MUST be wrapped in a database transaction.
*   **Rule 2:** All operations involving financial data (e.g., updating a user's `balance`) MUST be wrapped in a transaction, even if it is a single database call. This ensures atomicity and future-proofs the code for potential additions like audit logging.
*   **Rule 3:** A transaction's status MUST be checked after completion. On failure, a `critical` log entry MUST be created, and a generic, safe error message MUST be shown to the user.

*   **Example: The Critical Payment Verification Flow**

    *   **DON'T (Data Corruption Risk):**
        ```php
        // 1. Update payment status
        $this->paymentModel->update($paymentId, ['status' => 'success']);
        //
        // ---> SCRIPT FAILS HERE <--- The user is never credited.
        //
        // 2. Update user balance
        $this->userModel->addBalance($userId, $amount);
        ```

    *   **DO (Data Integrity Guaranteed):**
        ```php
        $db = \Config\Database::connect();
        $db->transStart();

        $this->paymentModel->update($paymentId, ['status' => 'success']);
        $this->userModel->addBalance($userId, $amount);

        $db->transComplete();

        if ($db->transStatus() === false) {
            // Log the critical failure for support staff
            log_message('critical', "Payment transaction failed for user: {$userId}");
            // Show a safe message to the user
            return redirect()->back()->with('error', 'A critical error occurred. Please contact support.');
        }

        return redirect()->to('...')->with('success', 'Payment successful!');
        ```


#### **3.4. Performance**
*   **Auto-Routing:** Auto-routing MUST be disabled (`$autoRoute = false`) in `app/Config/Routing.php`.
*   **Efficient Queries:** Use pagination (`paginate()`) for lists. Avoid `findAll()` on large tables. Select only the columns needed.
*   **Optimization Command:** The deployment script MUST run `php spark optimize`.

#### **3.5. Error Handling & Logging**
*   **Production Errors:** Detailed error reporting MUST be disabled in the production `.env` file (`CI_ENVIRONMENT = production`).
*   **Dual Logging Strategy:**
    *   **Developer Logs:** Use `log_message('level', 'message')` for system events and errors. These are for developers only.
    *   **User Notifications:** Use `session()->setFlashdata()` to communicate the outcome of actions to the user. These are rendered via the `flash_messages.php` partial.

---

### **Part 4: Frontend & UI Mandates**

*   **Bootstrap 5:** The project MUST use Bootstrap 5 as the sole CSS framework for consistency.
*   **Master Layout:** All pages MUST extend the master layout file at `app/Views/layouts/default.php`.
*   **Reusable Partials:** Common UI elements MUST be created as partial views in `app/Views/partials/`.
    *   **Flash Messages:** All status messages MUST be rendered via `app/Views/partials/flash_messages.php`.
    *   **Custom Components:** Sitewide components like pagination MUST have a custom view (e.g., `app/Views/pagers/bootstrap5_pagination.php`) and be configured as the default in `app/Config/Pager.php`.
*   **URL Generation: `route_to()` vs. `url_to()`**
    *   **For JavaScript Background Requests:** All URLs used within `<script>` blocks for background requests (e.g., AJAX, `fetch`) MUST be generated as relative paths using `route_to('route.name')`.
    *   **For HTML Full-Page Navigation:** All URLs used in standard HTML for full-page navigations (e.g., `<a>` tag `href` attributes, standard `<form>` `action` attributes, and controller redirects) MUST be generated as absolute paths using `url_to('route.name')`.
    *   **Reasoning:** This strict separation is the mandatory solution to prevent CORS policy errors. `route_to()` ensures same-origin requests for JavaScript, while `url_to()` ensures predictable, absolute paths for page loads and redirects.

---

### **Part 5: Environment & Deployment Checklist**

*   **Environment File:** All credentials and API keys MUST be in the `.env` file. The `.env` file MUST NOT be committed to version control.
*   **Production Mode:** The `CI_ENVIRONMENT` variable in `.env` MUST be set to `production`.
*   **Web Server Root:** The server's document root MUST point to the `/public` directory. **This is a critical security requirement.** The `app`, `system`, and `writable` directories must be located outside the web root.
*   **Composer for Production:** Deployments MUST run `composer install --no-dev --optimize-autoloader`.
*   **Clean Production Server:** Development directories (`tests/`) and files (`spark`, `phpunit.xml.dist`) MUST be removed from the production server.

---

### **Part 6: AI Agent Protocol**

This is the mandatory workflow for any AI agent modifying the codebase.

1.  **Acknowledge and Analyze:** State the user's request and break it down into a sequence of modifications that align with this constitution.
2.  **Declare Intent:** List all files that will be created or modified before generating any code.
3.  **Generate Full Files:** When modifying a file, provide the complete, updated file content. Partial snippets are FORBIDDEN.
4.  **Use Generators:** New boilerplate files (Controllers, Models, etc.) MUST be created using `php spark make:*` commands.
5.  **Confirm Compliance:** Conclude by confirming that all changes adhere to the rules outlined in this document.

---

### **Part 7: The Unified Frontend Workflow (The 'Blueprint' Method)**

This section enhances Part 4, providing a mandatory, step-by-step workflow for creating all user-facing views to ensure absolute consistency.

#### **7.1. The Blueprint Philosophy**
*   **Minimal:** Prioritize Bootstrap 5 utility classes over custom CSS. A new view should require little to no page-specific styling.
*   **Consistent:** All views are built from the same core components (The Container, The Card), ensuring a predictable user experience.
*   **Scalable:** The component-based approach allows for rapid, consistent development of new features.

#### **7.2. The Core Blueprint Components**
These are the foundational building blocks for every view.

*   **A. The Container:** Every page's primary content MUST be wrapped in a single `<div class="container my-5">`. This establishes consistent vertical and horizontal spacing sitewide.

*   **B. The Card (`.blueprint-card`):** All primary content, forms, and data displays MUST be placed within a "Blueprint Card." This is a standard Bootstrap card with a consistent, project-defined style.

    *   **Implementation:** `<div class="card blueprint-card">...</div>`
    *   **Mandatory Style (applied via `<style>` block or sitewide CSS):**
        ```css
        .blueprint-card {
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
            border: none;
        }
        ```

*   **C. The Header (`.blueprint-header`):** All pages MUST have a clear header section.

    *   **Implementation:**
        ```html
        <div class="blueprint-header text-center mb-5">
            <h1 class="fw-bold">Page Title</h1>
            <p class="lead text-muted">A brief, helpful description of the page.</p>
        </div>
        ```
    *   For pages with a back button, the header is adjusted:
        ```html
         <div class="d-flex align-items-center mb-4">
            <a href="..." class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Back</a>
            <h1 class="fw-bold mb-0">Page Title</h1>
        </div>
        ```


*   **D. The Color Palette:** Color usage is strictly limited to the CSS variables defined in `layouts/default.php` and Bootstrap's theme colors.
    *   **Primary Actions & Links:** `var(--primary-color)`
    *   **Secondary Text & Subtitles:** `var(--text-muted)`
    *   **Success State:** `var(--success-green)`
    *   **Backgrounds:** `var(--light-bg)` for pages, `var(--card-bg)` for cards.
    *   Introducing new, one-off hex color codes is **FORBIDDEN**.

#### **7.3. The View Creation Workflow**

1.  **Controller Preparation:** The controller MUST pass all necessary data to the view, including mandatory SEO variables: `pageTitle`, `metaDescription`, and `canonicalUrl`.

2.  **View Scaffolding:** Every new view file MUST follow the standard scaffolding structure of extending the default layout and defining content sections.

    ```php
    <?= $this->extend('layouts/default') ?>

    <?= $this->section('styles') ?>
    /* Custom styles are a last resort. Use Bootstrap utilities first. */
    <style>
        .blueprint-card { /* Standard card definition */
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
            border: none;
        }
    </style>
    <?= $this->endSection() ?>

    <?= $this->section('content') ?>
    <div class="container my-5">
        <!-- Step 1: Add Blueprint Header -->
        
        <!-- Step 2: Build UI with Blueprint Cards -->
        <div class="card blueprint-card">
            <div class="card-body p-4">
                <!-- Content goes here -->
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // All page-specific JavaScript goes here.
        });
    </script>
    <?= $this->endSection() ?>
    ```

3.  **Component Implementation:**
    *   **Forms:** All text inputs MUST use the Bootstrap 5 "Floating labels" pattern for consistency (`<div class="form-floating">...</div>`).
    *   **Buttons:** Button usage MUST follow a strict hierarchy:
        *   **Primary Action:** One `btn-primary` per form/view (e.g., "Submit", "Save").
        *   **Secondary Actions:** Use `btn-outline-secondary` or `btn-secondary` (e.g., "Cancel", "Back").
        *   **Destructive Actions:** Use `btn-danger` or `btn-outline-danger` (e.g., "Delete").
    *   **Alerts/Messages:** All user feedback (success, error) MUST be handled via the `partials/flash_messages.php` partial.

By following this workflow, every view will share a consistent, professional, and user-friendly DNA.