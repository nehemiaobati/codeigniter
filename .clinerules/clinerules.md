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

#### **3.3. Performance**
*   **Auto-Routing:** Auto-routing MUST be disabled (`$autoRoute = false`) in `app/Config/Routing.php`.
*   **Efficient Queries:** Use pagination (`paginate()`) for lists. Avoid `findAll()` on large tables. Select only the columns needed.
*   **Optimization Command:** The deployment script MUST run `php spark optimize`.

#### **3.4. Error Handling & Logging**
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
*   **Dynamic URLs in JS:** JavaScript making AJAX/fetch requests MUST construct URLs dynamically using `window.location.origin` to avoid hardcoding and ensure portability.

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
