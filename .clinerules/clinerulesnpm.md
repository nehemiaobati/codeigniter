# clinerulesnpm.md

### **Extension: Hybrid Frontend Architecture (Node.js/TypeScript)**

**Note:** This document builds upon the core `clinerules.md`. It specifically governs the integration of Node.js, NPM, and TypeScript modules within the CodeIgniter framework.

#### **9.1. Directory Structure**
*   **Source Code:** All raw JavaScript, TypeScript, and CSS source files MUST be located in the project root `resources/[module_name]/` directory. They MUST NOT be placed directly in `public/`.
*   **Compiled Output:** The build process MUST output files to `public/assets/[module_name]/`.
*   **Public Directory:** The `public/` directory is reserved for CodeIgniter entry points and compiled assets. Raw source code MUST NEVER exist here.

#### **9.2. The Build Pipeline (Vite)**
*   **Tooling:** Vite is the mandated build tool.
*   **Configuration:** The `vite.config.ts` MUST be configured to output specific entry points for each module (e.g., `learn`, `jarvis`) into their respective `public/assets/` subdirectories.
*   **Git Protocols:**
    *   `node_modules/` MUST be added to `.gitignore` and never committed.
    *   `public/assets/` (the compiled result) MUST be committed to Git. The production server is PHP-only and cannot build frontend assets.

#### **9.3. The PHP-to-JavaScript Bridge**
*   **Mount Points:** Views utilizing React MUST provide a unique HTML container ID (e.g., `<div id="module-root"></div>`).
*   **Data Injection:** Data required by the frontend (CSRF tokens, API URLs, User IDs) MUST be passed via a global `window.CI_CONFIG` object injected in the PHP View. Hardcoding URLs or Keys in TypeScript is **FORBIDDEN**.
*   **Script Loading:** Compiled scripts MUST be loaded using `base_url()` in the PHP View (e.g., `src="<?= base_url('assets/module/app.js') ?>"`).

#### **9.4. API Interaction**
*   **Communication:** The Frontend MUST communicate with the Backend via asynchronous `fetch()` calls.
*   **Routing:** Frontend requests MUST target named CodeIgniter routes.
*   **Security:** All AJAX/Fetch requests MUST include the CSRF token provided in `window.CI_CONFIG`.

---

### **Reference: General Functional Flow**

This is the standard workflow for adding a new TypeScript module to the CodeIgniter project.

#### **1. Setup Phase (One-time)**
1.  **Initialize:** Ensure `package.json`, `tsconfig.json`, and `vite.config.ts` exist in the project root.
2.  **Install:** Run `npm install` to populate `node_modules` (ensure this folder is gitignored).

#### **2. Development Phase (New Feature)**
1.  **Create Source:** Create a new folder `resources/[feature_name]/`.
2.  **Entry Point:** Create `resources/[feature_name]/main.tsx`.
3.  **Config:** Update `vite.config.ts` -> `rollupOptions` -> `input` to register the new entry point.
4.  **Develop:** Write React/TypeScript code in `resources/`.
5.  **Build:** Run `npm run build` locally. This compiles TS/React into standard JS in `public/assets/[feature_name]/`.

#### **3. Integration Phase (MVC)**
1.  **Controller:** Create a standard CodeIgniter Controller to serve the view.
2.  **View:** Create a PHP View file.
    *   Add the mount point div (e.g., `id="feature-root"`).
    *   Add the `window.CI_CONFIG` script block (CSRF, URLs).
    *   Add the `<script src="...">` tag pointing to the *compiled* file in `public/assets/`.
3.  **Routes:** Register the route in `Config/Routes.php`.

#### **4. Deployment Phase**
1.  **Build:** Run `npm run build` on the local/dev machine.
2.  **Commit:** Git commit the updated `resources/` (source) AND `public/assets/` (output).
3.  **Push:** Deploy to server. (The server only serves the PHP and the pre-compiled JS).