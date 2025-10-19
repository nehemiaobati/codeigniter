<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use App\Libraries\CryptoService;

class CryptoController extends BaseController
{
    protected CryptoService $cryptoService;
    protected UserModel $userModel;

    /**
     * Constructor.
     * Initializes the CryptoService and UserModel.
     */
    public function __construct()
    {
        $this->cryptoService = service('cryptoService');
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Crypto Query',
            'result' => session()->getFlashdata('result'),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('crypto/query_form', $data); // View name updated
    }

    public function query(): RedirectResponse
    {
        $rules = [
            'asset' => 'required|in_list[btc,ltc]',
            'query_type' => 'required|in_list[balance,tx]',
            'address' => 'required|min_length[26]|max_length[55]',
            'limit' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[50]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $asset = $this->request->getPost('asset');
        $queryType = $this->request->getPost('query_type');
        $address = $this->request->getPost('address');
        $limit = $this->request->getPost('limit');

        $result = [];
        $errors = [];

        try {
            if ($asset === 'btc') {
                if ($queryType === 'balance') {
                    $result = $this->cryptoService->getBtcBalance($address);
                } else {
                    $result = $this->cryptoService->getBtcTransactions($address, $limit);
                }
            } elseif ($asset === 'ltc') {
                if ($queryType === 'balance') {
                    $result = $this->cryptoService->getLtcBalance($address);
                } else {
                    $result = $this->cryptoService->getLtcTransactions($address, $limit);
                }
            }

            if (isset($result['error'])) {
                $errors[] = $result['error'];
            }

            if (empty($errors)) {
                $userId = (int) session()->get('userId'); // Cast userId to integer
                // Assuming a fixed USD cost for crypto queries for conversion to KSH
                $totalCostUSD = 0.01; // Placeholder for crypto query cost in USD
                $usdToKshRate = 129; // Exchange rate from USD to KSH

                $costInKSH = $totalCostUSD * $usdToKshRate;
                $deductionAmount = max(0.01, ceil($costInKSH * 100) / 100); // Ensure a minimum deduction
                $costMessage = "KSH " . number_format($deductionAmount, 2) . " deducted for your AI query.";

                if ($userId > 0) {
                    if ($this->userModel->deductBalance($userId, (string)$deductionAmount)) {
                        session()->setFlashdata('success', $costMessage);
                    } else {
                        // This error message covers insufficient balance or other deduction failures
                        $errors[] = 'Insufficient balance or failed to update balance.';
                    }
                } else {
                    $errors[] = 'User not logged in or invalid user ID. Cannot deduct balance.';
                    log_message('error', 'User not logged in or invalid user ID during balance deduction.');
                }
            }

        } catch (\Exception $e) {
            $errors[] = 'An unexpected error occurred: ' . $e->getMessage();
            log_message('error', 'Crypto query error: ' . $e->getMessage());
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        return redirect()->back()->withInput()->with('result', $result);
    }
}
