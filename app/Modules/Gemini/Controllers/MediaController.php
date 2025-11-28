<?php

namespace App\Modules\Gemini\Controllers;

use App\Controllers\BaseController;
use App\Modules\Gemini\Libraries\MediaGenerationService;
use CodeIgniter\API\ResponseTrait;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */
class MediaController extends BaseController
{
    use ResponseTrait;

    protected $mediaService;

    public function __construct()
    {
        $this->mediaService = service('mediaGenerationService');
    }

    public function generate()
    {
        $rules = [
            'prompt' => 'required|min_length[3]|max_length[1000]',
            'model_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userId = (int) session()->get('userId');
        $prompt = $this->request->getVar('prompt');
        $modelId = $this->request->getVar('model_id');

        // Optional: Check if model ID is valid
        $configs = $this->mediaService->getMediaConfig();
        if (!array_key_exists($modelId, $configs)) {
            return $this->fail('Invalid model ID selected.');
        }

        try {
            $result = $this->mediaService->generateMedia($userId, $prompt, $modelId);

            // Service returns ['status' => 'error/success', ...]. 
            // We return this directly so the frontend can handle 'status' === 'error' correctly.
            return $this->respond($result);
        } catch (\Exception $e) {
            log_message('error', '[MediaController::generate] ' . $e->getMessage());
            return $this->failServerError('An unexpected error occurred.');
        }
    }

    public function poll()
    {
        $opId = $this->request->getVar('op_id');

        if (!$opId) {
            return $this->fail('Operation ID is required.');
        }

        try {
            $result = $this->mediaService->pollVideoStatus($opId);
            return $this->respond($result);
        } catch (\Exception $e) {
            log_message('error', '[MediaController::poll] ' . $e->getMessage());
            return $this->failServerError('Polling failed.');
        }
    }

    public function serve($filename)
    {
        $userId = (int) session()->get('userId');
        $path = WRITEPATH . 'uploads/generated/' . $userId . '/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
