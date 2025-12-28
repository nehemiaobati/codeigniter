<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<!-- External Styles -->
<link rel="stylesheet" href="<?= base_url('assets/highlight/styles/atom-one-dark.min.css') ?>">

<style>
    /* 
    |--------------------------------------------------------------------------
    | AI Studio Implementation - Internal Styles
    |--------------------------------------------------------------------------
    | Scoped styles for the AI Studio interface.
    */

    /* =========================================
       1. Global Layout Overrides
       ========================================= */
    #mainNavbar,
    .footer,
    .container.my-4 {
        display: none !important;
    }

    body {
        overflow: hidden;
        padding: 0 !important;
        background-color: var(--bs-body-bg);
    }

    :root {
        --code-bg: #282c34;
        --sidebar-width: 350px;
        --header-height: 60px;
    }

    /* =========================================
       2. Main Container
       ========================================= */
    .gemini-view-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100dvh;
        width: 100vw;
        display: flex;
        overflow: hidden;
        z-index: 1000;
        background-color: var(--bs-body-bg);
    }

    .gemini-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        min-width: 0;
        overflow: hidden;
    }

    /* =========================================
       3. Header & Sidebar
       ========================================= */
    .gemini-header {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
        padding: 0.5rem 1.5rem;
        height: var(--header-height);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gemini-sidebar {
        width: var(--sidebar-width);
        border-left: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        overflow-y: auto;
        height: 100%;
        padding: 1.5rem;
        transition: transform 0.3s ease, margin-right 0.3s ease;
    }

    .gemini-sidebar.collapse:not(.show) {
        display: none;
    }

    @media (max-width: 991.98px) {
        .gemini-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 1050;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }
    }

    /* =========================================
       4. Content Areas
       ========================================= */
    .gemini-response-area {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
        scroll-behavior: smooth;
        min-height: 0;
    }

    .gemini-prompt-area {
        width: 100%;
        background: var(--bs-body-bg);
        border-top: 1px solid var(--bs-border-color);
        padding: 1rem 1.5rem calc(1rem + env(safe-area-inset-bottom));
        z-index: 10;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
    }

    /* =========================================
       5. Components: Inputs
       ========================================= */
    .prompt-textarea {
        resize: none;
        overflow-y: hidden;
        min-height: 40px;
        max-height: 120px;
        border-radius: 1.5rem;
        padding: 0.6rem 1rem;
        line-height: 1.5;
        transition: border-color 0.2s;
    }

    .prompt-textarea:focus {
        box-shadow: none;
        border-color: var(--bs-primary);
    }

    /* =========================================
       6. Components: Cards & Files
       ========================================= */
    .model-card {
        cursor: pointer;
        transition: 0.2s;
        border: 2px solid transparent;
        background-color: var(--bs-body-bg);
    }

    .model-card:hover {
        border-color: var(--bs-primary);
        transform: translateY(-2px);
    }

    .model-card.active {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
    }

    #upload-list-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        max-height: 100px;
        overflow-y: auto;
        margin-bottom: 0.5rem;
    }

    .file-chip {
        display: flex;
        align-items: center;
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.85rem;
        max-width: 220px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .file-chip .file-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-right: 8px;
        max-width: 150px;
    }

    .file-chip .progress-ring {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        border: 2px solid var(--bs-secondary-bg);
        border-top: 2px solid var(--bs-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* =========================================
       7. Components: Results & Code
       ========================================= */
    #results-card {
        overflow: visible;
        /* Allow dropdowns */
        border-radius: var(--bs-border-radius);
    }

    #results-card .card-header {
        border-top-left-radius: calc(var(--bs-border-radius) - 1px);
        border-top-right-radius: calc(var(--bs-border-radius) - 1px);
    }

    #results-card .card-footer {
        border-bottom-left-radius: calc(var(--bs-border-radius) - 1px);
        border-bottom-right-radius: calc(var(--bs-border-radius) - 1px);
    }

    pre {
        background: var(--code-bg);
        color: #fff;
        padding: 1rem;
        border-radius: 5px;
        position: relative;
        margin-top: 1rem;
    }

    /* Copy Button */
    .copy-code-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: all 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(5px);
        background: rgba(0, 0, 0, 0.2) !important;
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
        z-index: 5;
    }

    pre:hover .copy-code-btn {
        opacity: 1;
    }

    .copy-code-btn:hover {
        background: rgba(0, 0, 0, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }

    .copy-code-btn.copied {
        background: rgba(40, 167, 69, 0.8) !important;
        border-color: rgba(40, 167, 69, 1);
    }

    /* Media Output */
    .media-output-container {
        background-color: var(--bs-tertiary-bg);
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 300px;
        position: relative;
    }

    .generated-media-item {
        max-height: 500px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    .video-wrapper {
        width: 100%;
        max-width: 800px;
        aspect-ratio: 16/9;
        background: #000;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .polling-pulse {
        animation: pulse-border 2s infinite;
    }

    @keyframes pulse-border {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
            border-color: var(--bs-primary);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            border-color: var(--bs-primary);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            border-color: var(--bs-primary);
        }
    }

    /* =========================================
       8. Memory Stream Styles
       ========================================= */
    .memory-item {
        font-size: 0.9rem;
        border-left: 3px solid transparent;
        transition: all 0.2s;
        cursor: default;
        background-color: var(--bs-body-bg);
    }

    .memory-item:hover {
        background-color: var(--bs-tertiary-bg);
    }

    .memory-item.active-context {
        border-left-color: var(--bs-warning);
        background-color: rgba(255, 193, 7, 0.1) !important;
        border-radius: 4px;
    }

    .memory-date-header {
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        color: var(--bs-secondary);
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        position: sticky;
        top: 0;
        background: var(--bs-body-bg);
        z-index: 5;
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .delete-memory-btn {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .memory-item:hover .delete-memory-btn {
        opacity: 1;
    }

    /* Thinking Block Styles */
    .thinking-block {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
        transition: all 0.2s;
    }

    .thinking-block[open] {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .thinking-content {
        white-space: pre-wrap;
        font-family: monospace;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="gemini-view-container">

    <!-- Main Content Area -->
    <div class="gemini-main">
        <!-- Header -->
        <div class="gemini-header">
            <a href="<?= url_to('home') ?>" class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                <i class="bi bi-stars text-primary fs-4"></i>
                <span class="fw-bold fs-5">AI Studio</span>
            </a>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm theme-toggle" title="Toggle Theme">
                    <i class="bi bi-circle-half"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#geminiSidebar">
                    <i class="bi bi-layout-sidebar-reverse"></i> Settings
                </button>
            </div>
        </div>

        <!-- Chat / Response Area -->
        <div class="gemini-response-area" id="response-area-wrapper">
            <div id="flash-messages-container"><?= view('App\Views\partials\flash_messages') ?></div>

            <!-- Audio Player (Conditional) -->
            <div id="audio-player-container">
                <?php if (session()->getFlashdata('audio_url')): ?>
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-volume-up-fill fs-4 me-3"></i>
                        <audio controls autoplay class="w-100">
                            <source src="<?= url_to('gemini.serve_audio', session()->getFlashdata('audio_url')) ?>" type="audio/mpeg">
                            <source src="<?= url_to('gemini.serve_audio', session()->getFlashdata('audio_url')) ?>" type="audio/wav">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Results or Empty State -->
            <?php if ($result = session()->getFlashdata('result')): ?>
                <div class="card blueprint-card shadow-sm border-primary" id="results-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-stars me-2"></i>Studio Output</span>
                        <!-- Toolbar -->
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-light copy-btn" id="copyFullResponseBtn" data-format="text">
                                    <i class="bi bi-clipboard me-1"></i> Copy
                                </button>
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <h6 class="dropdown-header"><i class="bi bi-clipboard me-1"></i> Copy As</h6>
                                    </li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="text">Plain Text</a></li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="markdown">Markdown</a></li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="html">HTML</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header"><i class="bi bi-download me-1"></i> Export As</h6>
                                    </li>
                                    <li><a class="dropdown-item download-action" href="#" data-format="pdf">PDF Document</a></li>
                                    <li><a class="dropdown-item download-action" href="#" data-format="docx">Word Document</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Body -->
                    <div class="card-body response-content" id="ai-response-body"><?= $result ?></div>
                    <textarea id="raw-response" class="d-none"><?= esc(session()->getFlashdata('raw_result')) ?></textarea>
                    <div class="card-footer bg-body border-top text-center py-2">
                        <small class="text-muted fw-medium d-block">Generated by Google Gemini / Imagen / Veo</small>
                        <small class="text-muted" style="font-size: 0.7rem;">AI may make mistakes. Verify important information.</small>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center text-muted mt-5 pt-5" id="empty-state">
                    <div class="display-1 text-body-tertiary mb-3"><i class="bi bi-lightbulb"></i></div>
                    <h5>Start Creating</h5>
                    <p>Enter your prompt below to generate text, images, or code.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Prompt Input Area -->
        <div class="gemini-prompt-area">
            <form id="geminiForm" action="<?= url_to('gemini.generate') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Mode Tabs -->
                <ul class="nav nav-pills nav-sm mb-2" id="generationTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-2 px-3" data-bs-toggle="tab" data-type="text" data-model="gemini-2.5-flash">
                            <i class="bi bi-chat-text me-2"></i>Text
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3" data-bs-toggle="tab" data-type="image">
                            <i class="bi bi-image me-2"></i>Image
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3" data-bs-toggle="tab" data-type="video">
                            <i class="bi bi-camera-video me-2"></i>Video
                        </button>
                    </li>
                </ul>

                <!-- Model Selection (Dynamic) -->
                <div id="model-selection-area" class="mb-2 d-none">
                    <!-- Image Models -->
                    <div id="image-models-grid" class="d-flex gap-2 d-none overflow-auto py-2">
                        <?php foreach ($mediaConfigs as $modelId => $config): ?>
                            <?php if (strpos($config['type'], 'image') !== false): ?>
                                <div class="model-card card p-2" style="min-width: 120px;" data-model="<?= esc($modelId) ?>" data-type="image">
                                    <div class="text-center small">
                                        <i class="bi bi-image fs-5 text-primary"></i>
                                        <div class="text-truncate mt-1"><?= esc($config['name']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <!-- Video Models -->
                    <div id="video-models-grid" class="d-flex gap-2 d-none overflow-auto py-2">
                        <?php foreach ($mediaConfigs as $modelId => $config): ?>
                            <?php if ($config['type'] === 'video'): ?>
                                <div class="model-card card p-2" style="min-width: 120px;" data-model="<?= esc($modelId) ?>" data-type="video">
                                    <div class="text-center small">
                                        <i class="bi bi-camera-video fs-5 text-danger"></i>
                                        <div class="text-truncate mt-1"><?= esc($config['name']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Attachments & Input -->
                <div id="upload-list-wrapper"></div>
                <div id="uploaded-files-container"></div>

                <div class="d-flex align-items-end gap-2 bg-body-tertiary p-2 rounded-4 border">
                    <!-- Attachment Button -->
                    <div id="mediaUploadArea" class="d-inline-block p-0 border-0 bg-transparent mb-1">
                        <input type="file" id="media-input-trigger" multiple class="d-none">
                        <label for="media-input-trigger" class="btn btn-link text-secondary p-1" title="Attach files">
                            <i class="bi bi-paperclip fs-4"></i>
                        </label>
                    </div>

                    <!-- Main Text Input -->
                    <div class="flex-grow-1">
                        <input type="hidden" name="model_id" id="selectedModelId" value="gemini-2.0-flash">
                        <input type="hidden" name="generation_type" id="generationType" value="text">
                        <textarea id="prompt" name="prompt" class="form-control border-0 bg-transparent prompt-textarea shadow-none" placeholder="Message Gemini..." rows="1"><?= old('prompt') ?></textarea>
                    </div>

                    <!-- Submit & Save -->
                    <div class="d-flex align-items-center gap-1 mb-1">
                        <button type="button" class="btn btn-link text-secondary p-1" data-bs-toggle="modal" data-bs-target="#savePromptModal" title="Save Prompt">
                            <i class="bi bi-bookmark-plus fs-5"></i>
                        </button>
                        <button type="submit" id="generateBtn" class="btn btn-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Generate">
                            <i class="bi bi-arrow-up text-white fs-5"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Sidebar (Settings & History) -->
    <div class="gemini-sidebar collapse collapse-horizontal show" id="geminiSidebar">
        <!-- Header with Tabs -->
        <div class="d-flex align-items-center mb-3">
            <ul class="nav nav-pills nav-fill flex-grow-1 p-1 bg-body rounded" id="sidebarTabs" role="tablist" style="font-size: 0.9rem;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-1" id="config-tab" data-bs-toggle="tab" data-bs-target="#config-pane" type="button" role="tab"><i class="bi bi-sliders me-1"></i> Config</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1" id="memory-tab" data-bs-toggle="tab" data-bs-target="#memory-pane" type="button" role="tab"><i class="bi bi-activity me-1"></i> History</button>
                </li>
            </ul>
            <button class="btn-close ms-2 d-lg-none" data-bs-toggle="collapse" data-bs-target="#geminiSidebar"></button>
        </div>

        <div class="tab-content h-100 overflow-hidden d-flex flex-column">

            <!-- Configuration Pane -->
            <div class="tab-pane fade show active h-100 overflow-auto custom-scrollbar" id="config-pane" role="tabpanel">

                <!-- Toggles -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input setting-toggle" type="checkbox" id="assistantMode" data-key="assistant_mode_enabled" <?= $assistant_mode_enabled ? 'checked' : '' ?>>
                    <label class="form-check-label fw-medium" for="assistantMode">Conversational Memory</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input setting-toggle" type="checkbox" id="voiceOutput" data-key="voice_output_enabled" <?= $voice_output_enabled ? 'checked' : '' ?>>
                    <label class="form-check-label fw-medium" for="voiceOutput">Voice Output (TTS)</label>
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input setting-toggle" type="checkbox" id="streamOutput" data-key="stream_output_enabled" <?= $stream_output_enabled ? 'checked' : '' ?>>
                    <label class="form-check-label fw-medium" for="streamOutput">Stream Responses</label>
                </div>

                <hr>

                <!-- Saved Prompts -->
                <label class="form-label small fw-bold text-uppercase text-muted">Saved Prompts</label>
                <div id="saved-prompts-wrapper">
                    <div class="input-group mb-3 <?= empty($prompts) ? 'd-none' : '' ?>" id="savedPromptsContainer">
                        <select class="form-select form-select-sm" id="savedPrompts">
                            <option value="" disabled selected>Select...</option>
                            <?php if (!empty($prompts)): ?>
                                <?php foreach ($prompts as $p): ?>
                                    <option value="<?= esc($p->prompt_text, 'attr') ?>" data-id="<?= $p->id ?>"><?= esc($p->title) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="usePromptBtn">Load</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" id="deletePromptBtn" disabled><i class="bi bi-trash"></i></button>
                    </div>
                    <div id="no-prompts-alert" class="alert alert-light border mb-3 small text-muted <?= !empty($prompts) ? 'd-none' : '' ?>">
                        No saved prompts yet.
                    </div>
                </div>

                <hr>

                <!-- Danger Zone -->
                <form action="<?= url_to('gemini.memory.clear') ?>" method="post" onsubmit="return confirm('Clear all history?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm"><i class="bi bi-trash me-2"></i> Clear History</button>
                </form>

                <div class="mt-4 pt-4 text-center">
                    <small class="text-muted">AFRIKENKID AI Studio v2</small>
                </div>
            </div>

            <!-- Memory Stream Pane -->
            <div class="tab-pane fade h-100 overflow-auto custom-scrollbar" id="memory-pane" role="tabpanel">
                <div id="memory-loading" class="text-center py-4 d-none">
                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                </div>
                <div id="history-list" class="d-flex flex-column pb-5">
                    <!-- History items will be injected here -->
                    <div class="text-center text-muted small mt-5">
                        <i class="bi bi-clock-history fs-4 mb-2 d-block"></i>
                        Select the History tab to load interactions.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Hidden Support Forms -->
<form id="downloadForm" method="post" action="<?= url_to('gemini.download_document') ?>" target="_blank" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="raw_response" id="dl_raw">
    <input type="hidden" name="format" id="dl_format">
</form>

<!-- Save Prompt Modal -->
<div class="modal fade" id="savePromptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= url_to('gemini.prompts.add') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Save Prompt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Content</label>
                    <textarea name="prompt_text" id="modalPromptText" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Global Toasts -->
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3 gemini-toast-container">
    <div id="liveToast" class="toast text-bg-dark" role="alert">
        <div class="toast-body"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/highlight/highlight.js') ?>"></script>
<script src="<?= base_url('assets/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/marked/marked.min.js') ?>"></script>
<script>
    /**
     * ==========================================
     * Gemini AI Studio - Frontend Application
     * ==========================================
     */

    // Configuration Constants
    const APP_CONFIG = {
        csrfName: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>', // Initial hash
        limits: {
            maxFileSize: <?= $maxFileSize ?? 10 * 1024 * 1024 ?>,
            maxFiles: <?= $maxFiles ?? 5 ?>,
            supportedTypes: <?= $supportedMimeTypes ?? '[]' ?>,
        },
        endpoints: {
            upload: '<?= url_to('gemini.upload_media') ?>',
            deleteMedia: '<?= url_to('gemini.delete_media') ?>',
            settings: '<?= url_to('gemini.settings.update') ?>',
            deletePromptBase: '<?= url_to('gemini.prompts.delete', 0) ?>'.slice(0, -1),
            stream: '<?= url_to('gemini.stream') ?>',
            generate: '<?= url_to('gemini.generate') ?>',
            generateMedia: '<?= url_to('gemini.media.generate') ?>',
            pollMedia: '<?= url_to('gemini.media.poll') ?>',
            history: '<?= url_to('gemini.history.fetch') ?>',
            deleteHistory: '<?= url_to('gemini.history.delete') ?>'
        }
    };

    /**
     * 1. Core Application Controller
     */
    class GeminiApp {
        constructor() {
            this.csrfHash = APP_CONFIG.csrfHash; // Track current hash

            // Initialize Sub-Modules
            this.ui = new UIManager(this);
            this.uploader = new MediaUploader(this);
            this.prompts = new PromptManager(this);
            this.history = new HistoryManager(this);
            this.streamer = new StreamHandler(this);
            this.interaction = new InteractionHandler(this);
        }

        init() {
            // Setup Libraries
            if (typeof marked !== 'undefined') marked.use({
                breaks: true,
                gfm: true
            });

            // Initialize Modules
            this.ui.init();
            this.uploader.init();
            this.prompts.init();
            this.history.init();
            this.interaction.init();

            // Expose for debugging
            window.geminiApp = this;
        }

        /**
         * Update CSRF token state
         */
        refreshCsrf(hash) {
            if (!hash) return;
            this.csrfHash = hash;
            document.querySelectorAll(`input[name="${APP_CONFIG.csrfName}"]`)
                .forEach(el => el.value = hash);
        }

        /**
         * Unified AJAX Helper
         */
        async sendAjax(url, data = null) {
            const formData = data instanceof FormData ? data : new FormData();
            if (!formData.has(APP_CONFIG.csrfName)) {
                formData.append(APP_CONFIG.csrfName, this.csrfHash);
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

                const json = await res.json();

                // Rotation
                const token = json.token || json.csrf_token || res.headers.get('X-CSRF-TOKEN');
                if (token) this.refreshCsrf(token);

                return json;
            } catch (e) {
                console.error("AJAX Failure", e);
                this.ui.showToast('Communication error.');
                throw e;
            }
        }
    }

    /**
     * 2. UI Manager
     * Handles DOM visual updates, toggles, and library integrations.
     */
    class UIManager {
        constructor(app) {
            this.app = app;
            this.els = {
                generateBtn: document.getElementById('generateBtn'),
                sidebar: document.getElementById('geminiSidebar'),
                responseArea: document.getElementById('response-area-wrapper'),
                toast: document.getElementById('liveToast')
            };
        }

        init() {
            this.setupResponsiveSidebar();
            this.setupTabs();
            this.setupSettingsToggles();
            this.initTinyMCE();

            // Initial setup for existing content (e.g. after validation error)
            this.enableCodeFeatures();
            this.setupDownloads();
        }

        setupResponsiveSidebar() {
            if (window.innerWidth < 992 && this.els.sidebar && this.els.sidebar.classList.contains('show')) {
                this.els.sidebar.classList.remove('show');
            }
        }

        initTinyMCE() {
            if (typeof tinymce === 'undefined') return;
            tinymce.init({
                selector: '#prompt',
                menubar: false,
                statusbar: false,
                toolbar: false,
                license_key: 'gpl',
                plugins: 'autoresize',
                autoresize_bottom_margin: 0,
                min_height: 40,
                max_height: 120,
                highlight_on_focus: false, // Prevents TinyMCE's specific focus highlighting
                content_style: 'body { outline: none !important; }',
                setup: (editor) => {
                    editor.on('keydown', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            if (editor.getContent().trim()) {
                                editor.save();
                                document.getElementById('geminiForm').requestSubmit();
                            }
                        }
                    });
                    editor.on('init', () => this.updateModelSelectionUI(document.getElementById('generationType').value));
                }
            });
        }

        showToast(msg) {
            if (!this.els.toast) return;
            this.els.toast.querySelector('.toast-body').textContent = msg;
            new bootstrap.Toast(this.els.toast).show();
        }

        setError(msg) {
            const container = document.getElementById('flash-messages-container');
            if (container) {
                container.innerHTML = `<div class="alert alert-danger alert-dismissible fade show">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }
        }

        setLoading(isLoading) {
            const btn = this.els.generateBtn;
            if (isLoading) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm text-white"></span>';
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-up text-white fs-5"></i>';
            }
        }

        /**
         * Tab & Model Selection Logic
         */
        setupTabs() {
            document.querySelectorAll('#generationTabs button').forEach(btn => {
                btn.addEventListener('shown.bs.tab', (e) => {
                    const type = e.target.dataset.type;
                    document.getElementById('generationType').value = type;
                    this.updateModelSelectionUI(type);
                });
            });

            document.querySelectorAll('.model-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.model-card').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    document.getElementById('selectedModelId').value = card.dataset.model;
                });
            });
        }

        updateModelSelectionUI(type) {
            const area = document.getElementById('model-selection-area');
            const imgGrid = document.getElementById('image-models-grid');
            const vidGrid = document.getElementById('video-models-grid');
            const mInput = document.getElementById('selectedModelId');

            area.classList.add('d-none');
            imgGrid.classList.add('d-none');
            vidGrid.classList.add('d-none');

            let placeholder = 'Message Gemini...';

            if (type === 'text') {
                mInput.value = 'gemini-2.0-flash';
            } else {
                area.classList.remove('d-none');
                if (type === 'image') {
                    imgGrid.classList.remove('d-none');
                    placeholder = 'Describe the image...';
                    imgGrid.querySelector('.model-card')?.click();
                } else if (type === 'video') {
                    vidGrid.classList.remove('d-none');
                    placeholder = 'Describe the video...';
                    vidGrid.querySelector('.model-card')?.click();
                }
            }

            // Update TinyMCE placeholder
            if (tinymce.activeEditor) tinymce.activeEditor.getBody().setAttribute('data-mce-placeholder', placeholder);
            else document.getElementById('prompt')?.setAttribute('placeholder', placeholder);
        }

        /**
         * Setup Code highlighting and Copy buttons
         */
        enableCodeFeatures() {
            if (typeof hljs !== 'undefined') hljs.highlightAll();

            document.querySelectorAll('pre code').forEach((block) => {
                if (block.parentElement.querySelector('.copy-code-btn')) return;

                const btn = document.createElement('button');
                btn.className = 'btn btn-sm btn-dark copy-code-btn';
                btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                btn.onclick = (e) => {
                    e.preventDefault();
                    navigator.clipboard.writeText(block.innerText).then(() => {
                        btn.classList.add('copied');
                        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                        setTimeout(() => {
                            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                            btn.classList.remove('copied');
                        }, 2000);
                    });
                };
                block.parentElement.appendChild(btn);
            });
        }

        setupDownloads() {
            // Download handlers
            document.querySelectorAll('.download-action').forEach(btn => {
                btn.onclick = (e) => {
                    e.preventDefault();
                    document.getElementById('dl_raw').value = document.getElementById('raw-response').value;
                    document.getElementById('dl_format').value = e.target.dataset.format;
                    document.getElementById('downloadForm').submit();
                };
            });

            // Copy handlers
            const mainCopyBtn = document.getElementById('copyFullResponseBtn');
            if (!mainCopyBtn) return;

            mainCopyBtn.onclick = () => this.copyContent('text', mainCopyBtn);

            document.querySelectorAll('.copy-format-action').forEach(btn => {
                btn.onclick = (e) => {
                    e.preventDefault();
                    this.copyContent(e.target.dataset.format, mainCopyBtn);
                };
            });
        }

        copyContent(format, btn) {
            const raw = document.getElementById('raw-response');
            const body = document.getElementById('ai-response-body');
            if (!raw || !body) return;

            let content;
            if (format === 'markdown') {
                content = raw.value;
            } else if (format === 'html') {
                content = body.innerHTML;
            } else { // text
                // Temporarily expand thinking block to ensure innerText captures it
                const thinkingBlock = body.querySelector('.thinking-block');
                const wasOpen = thinkingBlock ? thinkingBlock.hasAttribute('open') : true;
                if (thinkingBlock && !wasOpen) thinkingBlock.setAttribute('open', '');

                content = body.innerText;

                if (thinkingBlock && !wasOpen) thinkingBlock.removeAttribute('open');
            }

            if (!content.trim()) return this.showToast('Nothing to copy.');

            navigator.clipboard.writeText(content).then(() => {
                this.showToast('Copied!');
                if (btn) {
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied';
                    setTimeout(() => btn.innerHTML = original, 2000);
                }
            });
        }

        setupSettingsToggles() {
            document.querySelectorAll('.setting-toggle').forEach(t => {
                t.addEventListener('change', async (e) => {
                    const fd = new FormData();
                    fd.append('setting_key', e.target.dataset.key);
                    fd.append('enabled', e.target.checked);
                    try {
                        const d = await this.app.sendAjax(APP_CONFIG.endpoints.settings, fd);
                        if (d.status !== 'success') this.showToast('Failed to save setting.');
                    } catch (e) {
                        /* Handled */
                    }
                });
            });
        }

        ensureResultCard() {
            const existing = document.getElementById('results-card');
            if (existing) return;
            document.getElementById('empty-state')?.remove();

            const html = `
                <div class="card blueprint-card shadow-sm border-primary" id="results-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-stars me-2"></i>Studio Output</span>
                         <div class="d-flex gap-2">
                             <!-- (Copy buttons ... reused from PHP template logic ideally, but rebuilt here for JS) -->
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-light copy-btn" id="copyFullResponseBtn" data-format="text"><i class="bi bi-clipboard me-1"></i> Copy</button>
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle</span></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header"><i class="bi bi-clipboard me-1"></i> Copy As</h6></li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="text">Plain Text</a></li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="markdown">Markdown</a></li>
                                    <li><a class="dropdown-item copy-format-action" href="#" data-format="html">HTML</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header"><i class="bi bi-download me-1"></i> Export As</h6></li>
                                    <li><a class="dropdown-item download-action" href="#" data-format="pdf">PDF Document</a></li>
                                    <li><a class="dropdown-item download-action" href="#" data-format="docx">Word Document</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body response-content" id="ai-response-body"></div>
                    <textarea id="raw-response" class="d-none"></textarea>
                    <div class="card-footer bg-body border-top text-center py-2">
                        <small class="text-muted fw-medium d-block">Generated by Google Gemini / Imagen / Veo</small>
                    </div>
                </div>`;
            this.els.responseArea.insertAdjacentHTML('beforeend', html);
            this.setupDownloads();
        }

        scrollToBottom() {
            setTimeout(() => document.getElementById('results-card')?.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            }), 100);
        }

        renderMediaCard(html, isProcessing = false) {
            const existing = document.getElementById('results-card');
            if (existing) existing.remove();
            document.getElementById('empty-state')?.remove();

            const processingClass = isProcessing ? 'polling-pulse' : '';
            const title = isProcessing ? 'Generating Content...' : 'Studio Output';

            const card = `
                <div class="card blueprint-card mt-4 shadow-sm border-primary ${processingClass}" id="results-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-stars me-2"></i>${title}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="media-output-container">${html}</div>
                    </div>
                </div>`;
            this.els.responseArea.insertAdjacentHTML('beforeend', card);
            this.scrollToBottom();
        }

        renderAudio(url) {
            if (!url) return;
            document.getElementById('audio-player-container').innerHTML = `
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-volume-up-fill fs-4 me-3"></i>
                    <audio controls autoplay class="w-100">
                        <source src="${url}" type="audio/mpeg">
                        <source src="${url}" type="audio/wav">
                    </audio>
                </div>`;
        }
    }

    /**
     * 3. Interaction Handler
     * Orchestrates form submissions and delegation to specific handlers.
     */
    class InteractionHandler {
        constructor(app) {
            this.app = app;
        }

        init() {
            document.getElementById('geminiForm')?.addEventListener('submit', e => this.handleSubmit(e));
        }

        async handleSubmit(e) {
            e.preventDefault();
            const type = document.getElementById('generationType').value;
            if (typeof tinymce !== 'undefined') tinymce.triggerSave();

            const prompt = document.getElementById('prompt').value.trim();
            if (!prompt && type === 'text') return this.app.ui.showToast('Please enter a prompt.');

            this.app.ui.setLoading(true);
            const fd = new FormData(document.getElementById('geminiForm'));

            try {
                if (type === 'text') {
                    if (document.getElementById('streamOutput')?.checked) await this.app.streamer.start(fd);
                    else await this.generateText(fd);
                } else {
                    await this.generateMedia(fd);
                }
            } catch (e) {
                /* Error UI handled in methods */
            } finally {
                if (type !== 'video') this.app.ui.setLoading(false); // Video polling handles its own loading state
            }
        }

        async generateText(fd) {
            this.app.ui.ensureResultCard();
            try {
                const d = await this.app.sendAjax(APP_CONFIG.endpoints.generate, fd);
                if (d.status === 'success') {
                    document.getElementById('ai-response-body').innerHTML = d.result;
                    document.getElementById('raw-response').value = d.raw_result;
                    this.app.ui.enableCodeFeatures();
                    this.app.ui.scrollToBottom();
                    if (d.flash_html) document.getElementById('flash-messages-container').innerHTML = d.flash_html;
                    this.app.ui.renderAudio(d.audio_url);

                    // Dynamic History Update
                    if (d.new_interaction_id) {
                        this.app.history.addItem({
                            id: d.new_interaction_id,
                            timestamp: d.timestamp,
                            user_input: d.user_input
                        }, d.raw_result); // Pass raw result for truncation in UI
                    }

                    if (d.used_interaction_ids) this.app.history.highlightContext(d.used_interaction_ids);
                } else {
                    this.app.ui.setError(d.message || 'Generation failed.');
                }
            } catch (e) {
                this.app.ui.setError('An error occurred during generation.');
            }
            this.app.uploader.clear();
        }

        async generateMedia(fd) {
            try {
                const d = await this.app.sendAjax(APP_CONFIG.endpoints.generateMedia, fd);
                if (d.status === 'error') throw new Error(d.message);

                if (d.type === 'image') {
                    const html = `<img src="${d.url}" class="generated-media-item img-fluid" onclick="window.open('${d.url}','_blank')">`;
                    this.app.ui.renderMediaCard(html);
                } else if (d.type === 'video') {
                    this.app.ui.renderMediaCard('<div class="text-center p-4"><div class="spinner-border text-primary mb-3"></div><h5>Synthesizing Video</h5><p class="text-muted">Processing...</p></div>', true);
                    this.pollVideo(d.op_id);
                    return; // Keep loading true
                }
            } catch (e) {
                this.app.ui.setError(e.message || 'Media Generation Failed');
            }
            this.app.uploader.clear();
        }

        pollVideo(opId) {
            const t = setInterval(async () => {
                const fd = new FormData();
                fd.append('op_id', opId);
                try {
                    const d = await this.app.sendAjax(APP_CONFIG.endpoints.pollMedia, fd);
                    if (d.status === 'completed') {
                        clearInterval(t);
                        const html = `<div class="video-wrapper"><video controls autoplay loop playsinline><source src="${d.url}" type="video/mp4"></video></div>`;
                        this.app.ui.renderMediaCard(html);
                        this.app.ui.setLoading(false);
                    } else if (d.status === 'failed') {
                        throw new Error(d.message);
                    }
                } catch (e) {
                    clearInterval(t);
                    this.app.ui.setError(e.message || 'Video processing failed.');
                    this.app.ui.setLoading(false);
                }
            }, 5000);
        }
    }

    /**
     * 4. Stream Handler
     * Manages server-sent events for streaming responses.
     */
    class StreamHandler {
        constructor(app) {
            this.app = app;
        }

        async start(formData) {
            this.app.ui.ensureResultCard();
            const els = {
                body: document.getElementById('ai-response-body'),
                raw: document.getElementById('raw-response'),
                audio: document.getElementById('audio-player-container')
            };

            // Reset
            els.body.innerHTML = '';
            els.raw.value = '';
            els.audio.innerHTML = '';

            try {
                if (!formData.has(APP_CONFIG.csrfName)) formData.append(APP_CONFIG.csrfName, this.app.csrfHash);

                const response = await fetch(APP_CONFIG.endpoints.stream, {
                    method: 'POST',
                    body: formData
                });

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let accum = '';

                while (true) {
                    const {
                        value,
                        done
                    } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value, {
                        stream: true
                    });
                    accum = this.processChunk(chunk, accum, els);
                }

                this.app.ui.enableCodeFeatures();
            } catch (e) {
                this.app.ui.setError('Stream Connection Lost.');
            }

            this.app.uploader.clear();
        }

        processChunk(chunk, accum, els) {
            const lines = chunk.split('\n');
            lines.forEach(line => {
                if (line.startsWith('data: ')) {
                    try {
                        const d = JSON.parse(line.substring(6));

                        // Handle Content
                        if (d.thought) {
                            this._ensureThinkingBlock(els.body);
                            this._appendToThinkingBlock(els.body, d.thought);

                            // Initialize raw value with header if empty
                            if (!els.raw.value.includes('=== THINKING PROCESS ===')) {
                                els.raw.value = '=== THINKING PROCESS ===\n\n' + els.raw.value;
                            }
                            els.raw.value += d.thought;

                        } else if (d.text) {
                            accum += d.text;

                            // Add separation and Answer header if transitioning from thoughts to text
                            if (els.raw.value.includes('=== THINKING PROCESS ===') && !els.raw.value.includes('=== ANSWER ===')) {
                                els.raw.value += '\n\n=== ANSWER ===\n\n';
                            }

                            this._preserveThinkingBlockWhileUpdating(els.body, () => {
                                els.body.innerHTML = marked.parse(accum);
                                els.raw.value += d.text;
                            });
                        }

                        // Handle Metadata
                        if (d.error) this.app.ui.setError(d.error);
                        if (d.cost) document.getElementById('flash-messages-container').innerHTML = `<div class="alert alert-success alert-dismissible fade show">KSH ${parseFloat(d.cost).toFixed(2)} deducted.<button class="btn-close" data-bs-dismiss="alert"></button></div>`;
                        if (d.audio_url) this.app.ui.renderAudio(d.audio_url);
                        if (d.csrf_token) this.app.refreshCsrf(d.csrf_token);

                        // Dynamic History Update
                        if (d.new_interaction_id) {
                            this.app.history.addItem({
                                id: d.new_interaction_id,
                                timestamp: d.timestamp,
                                user_input: d.user_input
                            }, this.app.streamer.currentFullText ?? '');
                        }
                        if (d.used_interaction_ids) this.app.history.highlightContext(d.used_interaction_ids);
                    } catch (e) {
                        /* Ignore partial JSON */
                        console.error("Parse error in stream chunk:", e);
                    }
                }
            });
            return accum;
        }

        /**
         * Ensure thinking block exists in the body element
         * @private
         */
        _ensureThinkingBlock(bodyEl) {
            if (bodyEl.querySelector('.thinking-block')) return;

            const thinkingBlock = document.createElement('details');
            thinkingBlock.className = 'thinking-block mb-3';

            const summary = document.createElement('summary');
            summary.textContent = 'Thinking Process';
            summary.className = 'cursor-pointer text-muted fw-bold small';

            const content = document.createElement('div');
            content.className = 'thinking-content fst-italic text-muted p-2 border-start mt-1 small';

            thinkingBlock.appendChild(summary);
            thinkingBlock.appendChild(content);

            bodyEl.insertBefore(thinkingBlock, bodyEl.firstChild);
        }

        /**
         * Append text to the thinking block content
         * @private
         */
        _appendToThinkingBlock(bodyEl, thoughtText) {
            const contentDiv = bodyEl.querySelector('.thinking-block .thinking-content');
            if (contentDiv) contentDiv.textContent += thoughtText;
        }

        /**
         * Preserve thinking block while updating body content
         * @private
         */
        _preserveThinkingBlockWhileUpdating(bodyEl, updateFn) {
            const thinkingBlock = bodyEl.querySelector('.thinking-block');
            updateFn();
            if (thinkingBlock) {
                bodyEl.insertBefore(thinkingBlock, bodyEl.firstChild);
            }
        }

        appendChunk(text) {
            // Ensure result area
            if (!this.resultEl) {
                this.createResultContainer();
            }

            // If first chunk, clear any placeholders
            if (this.isFirstChunk) {
                this.resultEl.innerHTML = '';
                this.isFirstChunk = false;
            }

            // Append text to buffer and update md
            this.streamBuffer += text;

            // Get existing thinking block if any
            const thinkingBlock = this.resultEl.querySelector('.thinking-block');

            this.resultEl.innerHTML = marked.parse(this.streamBuffer);
            this.rawEl.value = this.streamBuffer; // Update raw output

            if (thinkingBlock) {
                this.resultEl.prepend(thinkingBlock);
            }

            // Scroll to bottom
            const wrapper = document.getElementById('response-area-wrapper');
            if (wrapper) wrapper.scrollTop = wrapper.scrollHeight;
        }

        appendThinking(text) {
            if (!this.resultEl) {
                this.createResultContainer();
            }
            if (this.isFirstChunk) {
                this.resultEl.innerHTML = '';
                this.isFirstChunk = false;
            }

            let thinkingBlock = this.resultEl.querySelector('.thinking-block');
            if (!thinkingBlock) {
                // Create structure: <details><summary>...</summary><div>...</div></details>
                thinkingBlock = document.createElement('details');
                thinkingBlock.className = 'thinking-block mb-3';

                const summary = document.createElement('summary');
                summary.textContent = 'Thinking Process';
                summary.className = 'cursor-pointer text-muted fw-bold small';
                // summary.style.listStyle = 'none'; // Optional to hide default triangle

                const content = document.createElement('div');
                content.className = 'thinking-content fst-italic text-muted p-2 border-start mt-1 small';

                thinkingBlock.appendChild(summary);
                thinkingBlock.appendChild(content);

                this.resultEl.prepend(thinkingBlock);
            }

            const contentDiv = thinkingBlock.querySelector('.thinking-content');
            // Just append text node effectively or innerHTML safe
            // Text is likely to come in small tokens, not markdown formatted usually for thought blocks?
            // Gemini docs say it's text. Let's just append text.
            contentDiv.textContent += text;

            // We don't auto-scroll for thinking, maybe? Or yes?
            const wrapper = document.getElementById('response-area-wrapper');
            if (wrapper) wrapper.scrollTop = wrapper.scrollHeight;
        }
    }

    /**
     * 5. Media Uploader
     * Handles drag & drop and file APIs.
     */
    class MediaUploader {
        constructor(app) {
            this.app = app;
            this.queue = [];
            this.isUploading = false;
        }

        init() {
            const area = document.getElementById('mediaUploadArea');
            const inp = document.getElementById('media-input-trigger');
            if (!area) return;

            // Drag & Drop Events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => {
                area.addEventListener(e, ev => {
                    ev.preventDefault();
                    ev.stopPropagation();
                });
            });
            ['dragenter', 'dragover'].forEach(e => area.addEventListener(e, () => area.classList.add('dragover')));
            ['dragleave', 'drop'].forEach(e => area.addEventListener(e, () => area.classList.remove('dragover')));

            area.addEventListener('drop', e => this.handleFiles(e.dataTransfer.files));
            inp.addEventListener('change', e => {
                this.handleFiles(e.target.files);
                inp.value = '';
            });

            document.getElementById('upload-list-wrapper')?.addEventListener('click', e => {
                const btn = e.target.closest('.remove-btn');
                if (btn) this.removeFile(btn);
            });
        }

        handleFiles(files) {
            const currentCount = document.querySelectorAll('.file-chip').length;
            if ((files.length + currentCount) > APP_CONFIG.limits.maxFiles) {
                return this.app.ui.showToast(`Limit reached: Max ${APP_CONFIG.limits.maxFiles} files.`);
            }

            Array.from(files).forEach(f => {
                if (APP_CONFIG.limits.supportedTypes.includes(f.type) && f.size <= APP_CONFIG.limits.maxFileSize) {
                    const id = Math.random().toString(36).substr(2, 9);
                    this.queue.push({
                        file: f,
                        id: id,
                        ui: this.createFileChip(f, id)
                    });
                } else {
                    this.app.ui.showToast(`Invalid: ${f.name}`);
                }
            });

            if (this.queue.length) this.processQueue();
        }

        createFileChip(f, id) {
            const d = document.createElement('div');
            d.innerHTML = `<div class="file-chip fade show" id="file-item-${id}"><div class="progress-ring"></div><span class="file-name">${f.name}</span><button type="button" class="btn-close p-1 remove-btn disabled" data-id="${id}"></button></div>`;
            document.getElementById('upload-list-wrapper').appendChild(d.firstChild);
            return document.getElementById(`file-item-${id}`);
        }

        processQueue() {
            if (this.isUploading || !this.queue.length) return;
            this.isUploading = true;
            this.uploadFile(this.queue.shift());
        }

        uploadFile(job) {
            const fd = new FormData();
            fd.append(APP_CONFIG.csrfName, this.app.csrfHash);
            fd.append('file', job.file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', APP_CONFIG.endpoints.upload, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = () => {
                try {
                    const r = JSON.parse(xhr.responseText);
                    if (r.csrf_token) this.app.refreshCsrf(r.csrf_token);

                    if (xhr.status === 200 && r.status === 'success') {
                        this.updateChipStatus(job.ui, 'success');
                        job.ui.querySelector('.remove-btn').dataset.serverFileId = r.file_id;
                        this.appendHiddenInput(r.file_id, job.id);
                    } else throw new Error(r.message);
                } catch (e) {
                    this.updateChipStatus(job.ui, 'error');
                }
                this.isUploading = false;
                this.processQueue();
            };
            xhr.send(fd);
        }

        updateChipStatus(ui, status) {
            ui.querySelector('.progress-ring').remove();
            ui.querySelector('.remove-btn').classList.remove('disabled');
            const i = document.createElement('i');
            i.className = status === 'success' ? 'bi bi-check-circle-fill text-success me-2' : 'bi bi-exclamation-circle-fill text-danger me-2';
            ui.prepend(i);
            ui.style.borderColor = status === 'success' ? 'var(--bs-success)' : 'var(--bs-danger)';
        }

        appendHiddenInput(fileId, jobId) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'uploaded_media[]';
            hidden.value = fileId;
            hidden.id = `input-${jobId}`;
            document.getElementById('uploaded-files-container').appendChild(hidden);
        }

        async removeFile(btn) {
            const fid = btn.dataset.serverFileId;
            btn.closest('.file-chip').remove();
            document.getElementById(`input-${btn.dataset.id}`)?.remove();

            if (fid) {
                const fd = new FormData();
                fd.append('file_id', fid);
                this.app.sendAjax(APP_CONFIG.endpoints.deleteMedia, fd).catch(() => {});
            }
        }

        clear() {
            document.getElementById('upload-list-wrapper').innerHTML = '';
            document.getElementById('uploaded-files-container').innerHTML = '';
            this.queue = [];
        }
    }

    /**
     * 6. Prompt Manager
     * Handles loading and saving prompts.
     */
    class PromptManager {
        constructor(app) {
            this.app = app;
        }

        init() {
            // Load Prompt
            document.getElementById('usePromptBtn')?.addEventListener('click', () => {
                const sel = document.getElementById('savedPrompts');
                if (!sel || !sel.value) return;

                if (tinymce.get('prompt')) tinymce.get('prompt').setContent(sel.value);
                else document.getElementById('prompt').value = sel.value;
            });

            // Delete Prompt
            const sel = document.getElementById('savedPrompts');
            const delBtn = document.getElementById('deletePromptBtn');
            if (sel && delBtn) {
                sel.onchange = () => delBtn.disabled = !sel.value;
                delBtn.onclick = () => this.deletePrompt(sel);
            }

            // Save Prompt Form
            const form = document.querySelector('#savePromptModal form');
            if (form) {
                document.getElementById('savePromptModal').addEventListener('show.bs.modal', () => {
                    const val = tinymce.get('prompt') ? tinymce.get('prompt').getContent() : document.getElementById('prompt').value;
                    document.getElementById('modalPromptText').value = val;
                });

                form.onsubmit = async (e) => {
                    e.preventDefault();
                    const m = bootstrap.Modal.getInstance(document.getElementById('savePromptModal'));
                    try {
                        const d = await this.app.sendAjax(form.action, new FormData(form));
                        if (d.status === 'success') {
                            m.hide();
                            this.app.ui.showToast('Prompt saved!');

                            // Update UI dynamically
                            if (d.prompt) {
                                this.addPromptToUI(d.prompt);
                            }

                            // Clear form
                            e.target.reset();
                        } else {
                            this.app.ui.showToast('Failed to save.');
                        }
                    } catch (e) {
                        this.app.ui.showToast('Error saving prompt');
                    }
                };
            }
        }

        addPromptToUI(prompt) {
            const select = document.getElementById('savedPrompts');
            const container = document.getElementById('savedPromptsContainer');
            const emptyAlert = document.getElementById('no-prompts-alert');

            // Show container if it was hidden
            if (container.classList.contains('d-none')) {
                container.classList.remove('d-none');
                emptyAlert.classList.add('d-none');
            }

            // Add new option to select
            const option = document.createElement('option');
            option.value = prompt.prompt_text;
            option.dataset.id = prompt.id;
            option.textContent = prompt.title;
            select.appendChild(option);
        }

        async deletePrompt(sel) {
            if (!sel.value || !confirm('Delete this prompt?')) return;
            try {
                const id = sel.options[sel.selectedIndex].dataset.id;
                const d = await this.app.sendAjax(APP_CONFIG.endpoints.deletePromptBase + id);
                if (d.status === 'success') {
                    this.app.ui.showToast('Prompt deleted');
                    this.removePromptFromUI(id);
                } else {
                    this.app.ui.showToast('Delete failed');
                }
            } catch (e) {
                this.app.ui.showToast('Error deleting prompt');
            }
        }

        removePromptFromUI(id) {
            const select = document.getElementById('savedPrompts');
            const option = select.querySelector(`option[data-id="${id}"]`);

            if (option) {
                option.remove();

                // If no prompts left, show empty state
                if (select.options.length <= 1) { // Only the "Select..." option remains
                    document.getElementById('savedPromptsContainer').classList.add('d-none');
                    document.getElementById('no-prompts-alert').classList.remove('d-none');
                    document.getElementById('deletePromptBtn').disabled = true;
                }

                // Reset select
                select.value = '';
            }
        }
    }

    /**
     * 7. History Manager (Memory Stream)
     */
    class HistoryManager {
        static HISTORY_PAGE_SIZE = 5;

        constructor(app) {
            this.app = app;
            this.listEl = document.getElementById('history-list');
            this.loadingEl = document.getElementById('memory-loading');
            this.isLoaded = false;
            this.offset = 0;
            this.limit = HistoryManager.HISTORY_PAGE_SIZE;
            this.hasMore = true;
            this.currentLastDate = ''; // Track last date in memory
        }

        init() {
            // Load history when tab is shown
            const tabBtn = document.getElementById('memory-tab');
            if (tabBtn) {
                tabBtn.addEventListener('shown.bs.tab', () => {
                    if (!this.isLoaded) this.fetchHistory();
                });
            }

            // Bind click events for deletion and load more
            this.listEl.addEventListener('click', (e) => {
                const deleteBtn = e.target.closest('.delete-memory-btn');
                if (deleteBtn) {
                    e.stopPropagation();
                    this.deleteItem(deleteBtn.dataset.id);
                    return;
                }

                const loadMoreBtn = e.target.closest('.load-more-btn');
                if (loadMoreBtn) {
                    e.preventDefault();
                    this.loadMore();
                }
            });
        }

        async fetchHistory(append = false) {
            if (!append) {
                this.loadingEl.classList.remove('d-none');
                this.listEl.classList.add('d-none');
            }

            // Show loading state on Load More button if appending
            const loadMoreBtn = this.listEl.querySelector('.load-more-btn');
            if (append && loadMoreBtn) {
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            }

            try {
                const fd = new FormData();
                fd.append('limit', this.limit);
                fd.append('offset', this.offset);
                const d = await this.app.sendAjax(APP_CONFIG.endpoints.history, fd);

                if (d.status === 'success') {
                    this.renderList(d.history, append);
                    this.isLoaded = true;

                    // Update pagination state
                    this.offset += d.history.length;
                    this.hasMore = d.history.length === this.limit;

                    // Update or remove Load More button
                    this.updateLoadMoreButton();
                }
            } catch (e) {
                if (!append) {
                    this.listEl.innerHTML = '<div class="text-center text-danger mt-4"><small>Failed to load history.</small></div>';
                }
            } finally {
                if (!append) {
                    this.loadingEl.classList.add('d-none');
                    this.listEl.classList.remove('d-none');
                }
            }
        }

        renderList(items, append = false) {
            if (!items || items.length === 0) {
                if (!append) {
                    this.listEl.innerHTML = '<div class="text-center text-muted mt-5 small">No interaction history yet.</div>';
                }
                return;
            }

            if (!append) {
                this.listEl.innerHTML = '';
                this.currentLastDate = ''; // Reset when clearing list
            } else {
                // Remove existing Load More button before appending
                const existingBtn = this.listEl.querySelector('.load-more-btn');
                if (existingBtn) existingBtn.remove();
            }

            let lastDate = this.currentLastDate;

            items.forEach(item => {
                const date = this.formatDate(item.timestamp);

                if (date !== lastDate) {
                    const header = document.createElement('div');
                    header.className = 'memory-date-header mt-3 mb-2 px-2 py-1 rounded shadow-sm';
                    header.textContent = date;
                    this.listEl.appendChild(header);
                    lastDate = date;
                    this.currentLastDate = date; // Update memory tracker
                }

                const el = document.createElement('div');
                el.className = 'memory-item p-3 mb-2 rounded border shadow-sm position-relative';
                el.dataset.id = item.unique_id;
                el.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-truncate fw-medium" style="max-width: 85%; font-size: 0.85rem;" title="${this.escapeHtml(item.user_input_raw)}">
                            ${this.escapeHtml(item.user_input_raw)}
                        </div>
                        <button class="btn btn-link text-danger p-0 delete-memory-btn" style="font-size: 0.8rem;" data-id="${item.unique_id}" title="Forget">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="text-muted text-truncate small" style="opacity: 0.7;">
                        ${this.escapeHtml(item.ai_output)}
                    </div>
                `;
                this.listEl.appendChild(el);
            });
        }

        async deleteItem(id) {
            if (!confirm('Forget this interaction?')) return;

            const itemEl = this.listEl.querySelector(`.memory-item[data-id="${id}"]`);
            if (itemEl) itemEl.style.opacity = '0.5';

            const fd = new FormData();
            fd.append('unique_id', id);

            try {
                const d = await this.app.sendAjax(APP_CONFIG.endpoints.deleteHistory, fd);
                if (d.status === 'success') {
                    if (itemEl) itemEl.remove();
                    // Remove header if no items left under it (simple check)
                    // ... (Implementation optional for brevity)
                } else {
                    if (itemEl) itemEl.style.opacity = '1';
                    this.app.ui.showToast('Failed to delete.');
                }
            } catch (e) {
                if (itemEl) itemEl.style.opacity = '1';
                this.app.ui.showToast('Error deleting item.');
            }
        }

        highlightContext(ids) {
            // Clear previous
            this.listEl.querySelectorAll('.active-context').forEach(el => el.classList.remove('active-context'));

            if (!ids || !ids.length) return;

            // Highlight matches
            let firstMatch = null;
            ids.forEach(id => {
                const el = this.listEl.querySelector(`.memory-item[data-id="${id}"]`);
                if (el) {
                    el.classList.add('active-context');
                    if (!firstMatch) firstMatch = el;
                }
            });

            // Switch to tab and scroll
            if (firstMatch) {
                // If we are in assistant mode, maybe auto-switch tab?
                // For now, just scroll if visible
                const memTab = new bootstrap.Tab(document.getElementById('memory-tab'));
                // Only switch if not already viewing settings? Or always?
                // Let's rely on user intent or small indicator.
                // Requirement said: "Force the Sidebar to switch to the Memory Stream tab if assistant_mode is enabled."
                if (document.getElementById('assistantMode').checked) {
                    // Fix: Use Bootstrap 5 API correctly via instance get or create
                    const tabEl = document.getElementById('memory-tab');
                    const tab = bootstrap.Tab.getInstance(tabEl) || new bootstrap.Tab(tabEl);
                    tab.show();

                    setTimeout(() => firstMatch.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    }), 300);
                }
            }
        }

        addItem(item, aiOutputRaw) {
            // Remove empty state if present
            if (this.listEl.querySelector('.text-center.text-muted')) {
                this.listEl.innerHTML = '';
            }

            const dateStr = this.formatDate(item.timestamp);

            //Check if header exists for today/this date, else create
            // Simple approach: Just prepend for "Today" effectively
            let header = this.listEl.querySelector('.memory-date-header');
            if (!header || header.textContent !== dateStr) {
                // If the top header isn't for this date, insert new header
                const newHeader = document.createElement('div');
                newHeader.className = 'memory-date-header mt-3 mb-2 px-2 py-1 rounded shadow-sm';
                newHeader.textContent = dateStr;
                this.listEl.insertBefore(newHeader, this.listEl.firstChild);
            }

            const el = document.createElement('div');
            el.className = 'memory-item p-3 mb-2 rounded border shadow-sm position-relative';
            el.dataset.id = item.id;
            el.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-truncate fw-medium" style="max-width: 85%; font-size: 0.85rem;" title="${this.escapeHtml(item.user_input)}">
                        ${this.escapeHtml(item.user_input)}
                    </div>
                    <button class="btn btn-link text-danger p-0 delete-memory-btn" style="font-size: 0.8rem;" data-id="${item.id}" title="Forget">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="text-muted text-truncate small" style="opacity: 0.7;">
                    ${this.escapeHtml(typeof aiOutputRaw === 'string' ? aiOutputRaw : JSON.stringify(aiOutputRaw))}
                </div>
            `;

            // Insert after the first header
            const firstHeader = this.listEl.querySelector('.memory-date-header');
            if (firstHeader && firstHeader.nextSibling) {
                this.listEl.insertBefore(el, firstHeader.nextSibling);
            } else {
                this.listEl.appendChild(el);
            }
        }

        async loadMore() {
            await this.fetchHistory(true); // Append mode
        }

        updateLoadMoreButton() {
            // Remove existing button if present
            const existingBtn = this.listEl.querySelector('.load-more-btn');
            if (existingBtn) existingBtn.remove();

            // Add button only if there are more items
            if (this.hasMore) {
                const btnContainer = document.createElement('div');
                btnContainer.className = 'text-center py-3';
                btnContainer.innerHTML = `
                    <button class="btn btn-sm btn-outline-primary load-more-btn">
                        Load More <i class="bi bi-arrow-down-circle ms-1"></i>
                    </button>
                `;
                this.listEl.appendChild(btnContainer);
            }
        }

        formatDate(timestamp) {
            // Handle SQL Date format (YYYY-MM-DD HH:MM:SS) or ISO
            let date;
            if (typeof timestamp === 'string' && timestamp.indexOf(' ') > 0) {
                // Replace space with T for easier parsing in some browsers, 
                // though modern browsers handle SQL format often.
                date = new Date(timestamp.replace(' ', 'T'));
            } else {
                date = new Date(timestamp);
            }

            if (isNaN(date.getTime())) return 'Today'; // Fallback

            return date.toLocaleDateString(undefined, {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
        }

        escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => new GeminiApp().init());
</script>
<?= $this->endSection() ?>