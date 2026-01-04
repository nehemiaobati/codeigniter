# CodeIgniter 4 Handbook: The "Simple over Easy" Standard

## 0. Philosophy: Simple vs. Easy

> _"Conflating [Simple and Easy] is why we’re drowning in complexity."_
> — Rich Hickey

This project adheres to a strict philosophical standard. Every architectural decision is weighed against these definitions:

- **Simple (Objective)**: Disentangled, linear, single-responsibility. Harder to design, but trivial to maintain.
- **Easy (Subjective)**: Familiar, near-at-hand, minimal typing. Quick to start, but creates "complected" (braided) knots that kill modularity.

**Rule**: We prioritize **Simple** (Architectural Purity) over **Easy** (Developer Convenience).

---

## 1. Project Initialization & Architecture

### 1.1 The "3-Command" Boot Protocol

A developer must be able to boot a fresh instance in exactly 3 steps.

1.  **Install Dependencies**: `composer install` (Use `--no-dev` for production).
2.  **Migrate**: `php spark migrate --all` (Runs core & module migrations).
3.  **Seed**: `php spark db:seed MainSeeder` (Populates reference data).

### 1.2 Modular MVC-S Structure

We do not build a "Monolith" in `app/`. We build independent **Features** in `app/Modules/`.

- **Core Shell** (`app/`): Contains only shared resources (BaseController, Auth filters, AppConfig).
- **Modules** (`app/Modules/`): Self-contained features (e.g., `Auth`, `Billing`, `Blog`).

**Directory Structure per Module:**

```text
app/Modules/User/
├── Config/      # Routes.php (Manual registration required)
├── Controllers/ # HTTP Orchestration only
├── Database/
│   ├── Migrations/
│   └── Seeds/
├── Entities/    # Business Objects (Data shape, not logic)
├── Libraries/   # Services (Business Logic)
├── Models/      # DB Interaction
└── Views/       # Presentation
```

### 1.3 Generating a New Module

1.  Run: `php spark make:module [Name]`
2.  **Register Namespace** in `app/Config/Autoload.php`:
    ```php
    public $psr4 = [
        APP_NAMESPACE => APPPATH, // Keep existing
        'App\Modules\Name' => APPPATH . 'Modules/Name',
    ];
    ```
3.  **Define Routes** in `app/Modules/Name/Config/Routes.php`.

### 1.4 Standard Tooling (Custom Commands)

The following commands are **NOT** native to CodeIgniter 4 and must be implemented manually in `app/Commands/`.

- `make:module`: Scaffolds MVC-S structure.
- `db:backup`: Wrapper for `mysqldump` (requires system binary).
- `db:restore`: Wrapper for `mysql` client (requires system binary).

> **Implementation Source**: See `tooling_setup.md` in this directory for the full source code.

---

## 2. Layer Responsibilities (Strict Separation)

### 2.1 Controllers (The Traffic Cop)

- **Role**: Accept Request -> Validate -> Call Service -> Return Response.
- **Forbidden**:
  - Direct Database calls (Model instantiation allowed, but queries discouraged).
  - Business Logic or Calculations.
  - HTML generation (use Views).
- **Key Pattern**: "Skinny Controller". If it has `if` statements for business rules, move them to the Service.

### 2.2 Services (The Engine Room)

- **Location**: `app/Modules/[Name]/Libraries/` or `app/Libraries/`.
- **Role**: The **only** place where business logic lives.
- **Requirement**: Must be reusable and registered in `app/Config/Services.php` (if shared).
- **Responsibilities**:
  - Handle Transactions (`db->transStart()`).
  - Manage File Uploads/Processing.
  - Interact with 3rd Party APIs.
  - Perform calculations.
- **Output**: Returns standardized arrays or Entities.

### 2.3 Models (The Librarian)

- **Role**: Fetching and Storing raw data.
- **Principle**: "Objects for Data, Arrays for Config." Passing raw arrays for business objects is **FORBIDDEN**.
- **Configuration**:
  - `protected $returnType = \App\Entities\User::class;` (Always use Entities).
  - `protected $allowedFields = [...]` (Strict definition).
- **Forbidden**:
  - Business logic methods.
  - Being called directly by a **View**.

### 2.4 Views (The Canvas)

- **Role**: Display data.
- **Security**: MUST use `esc($var)` for dynamic output.
- **Logic**: Loops (`foreach`) and simple conditionals (`if`) only. No complex processing.

### 2.5 Database (The Vault)

- **Role**: Persistent storage.
- **Mandatory**:
  - **Schema**: managed strictly via **Migrations**.
  - **Data**: Initial/Test data managed via **Seeds**.
- **Forbidden**: Manual schema changes (SQL/GUI) outside of migrations.

### 2.6 Helpers (`app/Helpers/`)

- **Role**: Small, stateless, reusable procedural functions.
- **One-Time Setup**: New helpers MUST be registered in `app/Config/Autoload.php`.
- **Forbidden**:
  - Business logic (Use Services).
  - Database queries (Use Models/Services).
  - Stateful operations.

### 2.7 Configuration (`app/Config/`)

- **Sensitive**: API Keys/Secrets MUST live in `.env`.
- **Custom**: Feature-specific config MUST use `App\Config\Custom` namespace (e.g., `app/Config/Custom/BlogConfig.php`).

### 2.8 Performance Rules (& Pagination)

- **Pagination**: Use `paginate()` for lists. `findAll()` on potentially large tables is **FORBIDDEN**.
- **Optimization**: Deployment script MUST run `php spark optimize`.
- **Auto-Routing**: `$autoRoute = false` is Mandatory.

---

## 3. Data & Request Protocol

### 3.1 Routing & URLs

- **Named Routes**: All routes MUST be named (`['as' => 'name']`).
- **Grouping**: Use `$routes->group()` to organize by feature or access level (e.g., `['filter' => 'auth']`).
- **Callbacks**: Group callbacks MUST be `static function`.
- **Target**: Routes MUST point to `Controller::method`. Logic Closures (`function() {...}`) in routes are **FORBIDDEN**.
- **Usage**: `url_to('name')` is mandatory. Hardcoded paths are **FORBIDDEN**.

### 3.2 Form Handling (PRG Pattern)

1.  **POST Request**: Controller validates input.
2.  **Processing**: Service handles the job.
3.  **Response**: Controller redirects (`return redirect()->back()->with('success', '...');`).
    - **NEVER** return a View directly from a POST method.

### 3.3 Stateless/Serverless Compliance (The Unlink Pattern)

We treat the filesystem as ephemeral.

1.  **Uploads**: Controller accepts file -> Service processes it -> Service **deletes** temp file.
2.  **Generated Assets**: Create file -> Stream to user (`readfile`) -> **Delete** file (`@unlink`).
3.  **Session**: No binary data (images/PDFs) in session. Store IDs only.
4.  **Database**: Session driver uses `DatabaseHandler` (`ci_sessions` table with `MEDIUMBLOB`).

### 3.4 Tempfile Pattern (Secure & Randomized)

For consistency and security when handling ephemeral data:

1.  **Storage**: Centralize uploads in `WRITEPATH . 'uploads/[type]/[userId]/'`.
2.  **Naming**: Always use `$file->getRandomName()` to prevent collisions and enumeration.
3.  **Encapsulation**: Services MUST handle directory creation (`mkdir($path, 0755, true)`) and file moving.
4.  **Cleanup**: Provide a `cleanupTempFiles(array $fileIds, int $userId)` method in the Service for idempotent deletion.

### 3.5 API & AJAX

- **Response Format**: standardized JSON format.
  ```json
  {
      "status": "success|error",
      "message": "Human readable string",
      "data": { ... },
      "token": "new_csrf_hash" // MANDATORY
  }
  ```
- **CSRF**: Every JSON response (Success, Error, or Edge Case) MUST include a fresh CSRF token to keep the client in sync.
- **SSE (Streaming)**:
  - **Headers**: Flush immediately (`ob_flush(); flush()`).
  - **Session**: MUST call `session_write_close()` before the loop to prevent locking.
  - **Auth**: Send a fresh CSRF token in the first data event.

---

## 4. Security Mandates

1.  **CSRF**: Enabled globally.
    - **Forms**: Must use `csrf_field()`.
    - **Backend Responsibility**: Every JSON response (Success/Error/Edge Case) MUST include a fresh token (`['token' => csrf_hash()]`).
    - **Frontend**: JS MUST update its token from the response payload. Manual cookie logic is **FORBIDDEN**.
2.  **Validation**: Strict input validation rules in Controller.
3.  **reCAPTCHA**:
    - **Views**: Get key via `service('recaptchaService')->getSiteKey()`.
    - **Controllers**: Verify via `service('recaptchaService')->verify($response)`.
    - **Config**: Keys MUST be in `.env`. Custom config files are **FORBIDDEN**.
4.  **Validation**: Strict input validation rules in Controller.
5.  **Escaping**: Double-check `esc()` in Views. Unescaped output requires explicit approval comments.
6.  **Transactions**: Any method modifying the DB MUST use transactions.
    ```php
    $this->db->transStart();
    // operations
    $this->db->transComplete();
    if ($this->db->transStatus() === false) { ... }
    ```

---

## 5. Development & Observability

### 5.1 Logging & Observability

- **Principle**: In Development, errors MUST be visible and loud. In Production, errors MUST be silent to the user but strictly recorded.
- **Source of Truth**: `writable/logs/` is the first step in ANY investigation.
- **Usage**: `log_message($level, $message, $context)`.
- **Context**: Logs MUST include context arrays (variables, IDs, error traces), not just strings.
- **Levels**:
  - `critical`: System unusable (DB down). Triggers immediate alert.
  - `error`: Runtime failure (Transaction rollback, Upload failed).
  - `info`: Key business events (User login, Report generated).
- **User Feedback**: Use `session()->setFlashdata()` to communicate outcomes (Success/Error/Warning) to the user.

### 5.2 Testing

- **Slogan**: "No feature is done' without a test. Default to False unless requested"
- **Tool**: PHPUnit (`php spark test`).
- **Strategy**:
  - **Unit**: Test Services (mock DB/API).
  - **Feature**: Test Controllers (use real DB with `DatabaseTransactions` trait).

### 5.3 Deployment Checklist

- Set `CI_ENVIRONMENT = production`.
- Run `composer install --no-dev --optimize-autoloader`.
- Run `php spark optimize`.
- Ensure `writable/` is writable by web user.
- Disable `display_errors`.

---

## 6. Frontend Blueprints

- **Framework**: Bootstrap 5 (Utility-first approach).
- **Layouts**: All views extend `layouts/default`.
- **Partials**: Reusable UI chunks go in `partials/` (e.g., `flash_messages.php`).
- **Structure (The Blueprint Method)**:
  - **Container**: Wrap content in `<div class="container my-5">`.
  - **Header**: Use `<div class="blueprint-header">`.
  - **Card**: Use `<div class="card blueprint-card">`.
- **Theme Awareness**: Hardcoding colors is **FORBIDDEN**.
  1.  Use Theme-aware Bootstrap utilities first (e.g., `bg-body-tertiary`).
  2.  Use project CSS variables second (e.g., `var(--card-bg)`).
- **SEO**:
  - **Meta**: Controller MUST pass `pageTitle` and `metaDescription`.
  - **Canonical**: Controller MUST pass `canonicalUrl`.

```

```

N/B

# Simple vs. Easy: Summary

This framework is based on the 2011 talk "Simple Made Easy" by Rich Hickey (creator of the Clojure programming language). It challenges the common industry habit of choosing "convenient" tools that eventually lead to unmanageable complexity.

## Simple (Objective)

- **Definition:** Originates from _simplex_, meaning "one fold" or "one braid."
- **Focus:** Concern, task, and role. It is about the lack of entanglement.
- **Key Characteristics:**
  - **Single Responsibility:** Each part does exactly one thing.
  - **Disentangled:** Components are not "braided" or tied together; they can be moved or changed independently.
  - **The Cost:** Requires significant upfront design, thought, and untangling.
  - **The Benefit:** Makes systems easier to understand, debug, and scale over the long term.

## Easy (Subjective)

- **Definition:** Originates from _adjacens_, meaning "lying nearby" or "at hand."
- **Focus:** Familiarity and accessibility.
- **Key Characteristics:**
  - **Near at Hand:** It's "easy" because it's already installed, familiar, or reachable.
  - **Frictionless:** "Copy, paste, ship" or "Install a package." It feels fast initially.
  - **The Trap:** What is "easy" (familiar) isn't always "simple" (unentangled).
  - **The Cost:** Choosing "easy" often creates "complected" (intertwined) systems that become impossible to change later.

## Comparison Table

| Feature     | Simple                             | Easy                                   |
| :---------- | :--------------------------------- | :------------------------------------- |
| **Nature**  | Objective (The system's structure) | Subjective (The developer's comfort)   |
| **Focus**   | One fold / one braid               | Adjacent / reachable                   |
| **Effort**  | Requires design and untangling     | "Just put it closer" / Familiarity     |
| **Action**  | Single responsibility              | Copy, paste, ship                      |
| **Outcome** | Long-term reliability and speed    | Short-term speed, long-term complexity |

## The Core Message

> _"Conflating [Simple and Easy] is why we’re drowning in complexity."_

While "easy" things allow us to move fast today, only "simple" designs allow us to keep moving fast in the future.
