# CodeIgniter 4 Handbook: The "Simple over Easy" Standard

## Meta-Rules

1.  **Universal**: Applies to **ANY** CI4 project. Project-agnostic.
2.  **No Specifics**: Use generic terms (`BillingService`, not `GeminiService`).
3.  **First Source**: Read before starting any task.

---

## 0. Core Philosophy

**Prioritize Simple (Architectural Purity/Disentangled) over Easy (Developer Convenience).**

- **Simple**: Single-responsibility, linear, independent.
- **Easy**: Intertwined, "quick fix", kills modularity.

---

## 1. Project Initialization & Architecture

### 1.1 Boot Protocol (3 Steps)

1.  **Install**: `composer install` (`--no-dev` for prod).
2.  **Migrate**: `php spark migrate --all`.
3.  **Seed**: `php spark db:seed MainSeeder`.

### 1.2 Modular MVC-S Structure

- **Core** (`app/`): Shell/Shared (BaseController, AppConfig).
- **Modules** (`app/Modules/`): Self-contained Features.

```text
app/Modules/Feature/
├── Config/      # Routes.php (Manual reg required)
├── Controllers/ # HTTP Orchestration
├── Database/    # Migrations, Seeds
├── Entities/    # Business Objects
├── Libraries/   # Services (Business Logic)
├── Models/      # DB Interaction
└── Views/       # Presentation
```

### 1.3 Generator & Tooling

- **Generate**: `php spark make:module [Name]`.
- **Register**: Add namespace `App\Modules\Name` in `app/Config/Autoload.php`.
- **Route**: Define in `app/Modules/Name/Config/Routes.php`.
- **Custom Commands** (Source: `tooling_setup.md`): `make:module`, `db:backup`, `db:restore`.

### 1.4 Code Standards

- **Strict**: `declare(strict_types=1);` and PSR-12.
- **Docs**: Complete PHPDoc for all classes/methods.
- **Private Helpers**:
  - **Visibility**: `private function _helperName()`.
  - **Location**: Grouped under `// --- Helper Methods ---`.
  - **Order**: Defined **before** public usage. Stateless.

---

## 2. Layer Responsibilities

### 2.1 Controllers (Orchestration)

- **Role**: Validate Input → Call Service → Return Response.
- **Forbidden**: DB calls, Business Logic, HTML generation.
- **SEO**: MUST pass standard SEO `$data` (PageTitle, MetaDesc, Robots).

### 2.2 Services (Business Logic)

- **Role**: **Sole** location for Logic, Calculations, & API Interactions.
- **Responsibilities**: Handle Transactions, File Processing, Facades.
- **Topology**: Vertical flow only. **Triangular/brother-dependencies FORBIDDEN**. Use Facades/Orchestrators.

### 2.3 Models (Data Access)

- **Role**: Fetch/Store.
- **Config**: `returnType = Entity`, strict `allowedFields`.
- **Forbidden**: Business logic, Direct Calls from Views.

### 2.4 Views (Presentation)

- **Role**: Display only.
- **Security**: `esc($var)` MANDATORY.
- **Logic**: Loops/Simple conditionals only.

### 2.5 Database

- **Management**: Strict **Migrations** & **Seeds**.
- **Migration Strategy**:
  - **Dev/Prod**: Incremental updates.
  - **Fresh Setup**: "Compression Plan" MANDATORY before merging updates into base migrations.
- **Indexing**: `addKey()` in `createTable` MANDATORY for: `status`, `user_id`, `type`, `slug`, `hash`, timestamps.
- **Forbidden**: Manual SQL/GUI schema changes.

### 2.6 Helpers (`app/Helpers/`)

- **Role**: Pure, stateless, reusable procedural functions. No Logic/DB.

### 2.7 Config (`app/Config/`)

- **Secrets**: Must be in `.env`.
- **Custom**: Use `App\Config\Custom` namespace.

### 2.8 Performance

- **Pagination**: MANDATORY for lists. `findAll()` forbidden on large tables.
- **Auto-Routing**: `false` MANDATORY.

---

## 3. Data & Protocol

### 3.1 Routing

- **Rules**: Named routes, Grouped (`$routes->group`), `static` callbacks.
- **Forbidden**: Closures in routes, Hardcoded URLs (Use `url_to`).

### 3.2 Form Handling (PRG)

- **Flow**: POST → Validate → Service → Redirect (`back()->with(...)`).
- **Feedback**: Use standard flash keys: `success`, `error`, `errors`, `warning`, `info`.
- **Forbidden**: Returning Views from POST.

### 3.3 Stateless & Files

- **Unlink Pattern**: Upload → Process → Delete. Filesystem is ephemeral.
- **Tempfile**: Random naming (`getRandomName`), centralized storage, auto-cleanup.
- **Session**: IDs only. No binary data.

### 3.4 API & AJAX

- **Format**: Standard JSON Structure.
  ```json
  {
      "status": "success|error",
      "message": "Readable",
      "result": { ... },
      "errors": [ ... ],
      "csrf_token": "hash"
  }
  ```
- **CSRF**: **MANDATORY** rotation in every JSON response.
- **Frontend**: Centralized handler for token rotation and 403 redirects.

---

## 4. Security Mandates

1.  **CSRF**: Global. Forms use `csrf_field()`. JSON uses `csrf_token`.
2.  **Validation**: Strict Controller validation.
3.  **reCAPTCHA**: Verify via Service. Keys in `.env`.
4.  **Throttling**: MANDATORY for Auth & Resource-heavy (AI/Crypto) routes.
5.  **Transactions**: MANDATORY for **ANY** DB modification (`transStart`/`transComplete`).

---

## 5. Dev & Observability

1.  **Logging**: `writable/logs/` is Truth. Context arrays required.
2.  **Testing**: "No feature done without a test". Default no test unless expicitly requested.
3.  **Deployment**: `composer install --no-dev`, `spark optimize`, `display_errors=0`.
4.  **Exceptions**: Catch `\Throwable` in Controllers to prevent white screens.

---

## 6. Frontend Blueprints

- **Stack**: Bootstrap 5 (Utility-first). Views extend `layouts/default`.
- **Structure**: Container > Blueprint Header > Blueprint Card.
- **Theme**: No hardcoded colors. Use BS5 vars or `--custom-vars`.
- **SEO**: OpenGraph & Twitter Cards MANDATORY.

### 6.1 Shared UI & Feedback

- **Partials**: Modular snippets located in `app/Views/partials/`.
- **Flash Messages**: Centralized helper (`partials/flash_messages.php`) to render session flash data. Always `esc()` message content.
