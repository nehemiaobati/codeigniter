<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">User Details</h4>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> <?= esc($user->username) ?></p>
                    <p><strong>Email:</strong> <?= esc($user->email) ?></p>
                    <p><strong>Balance:</strong> $<?= esc(number_format($user->balance, 2)) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Transaction History</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($transactions)): ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= esc($transaction->created_at) ?></td>
                                        <td>$<?= esc(number_format($transaction->amount, 2)) ?></td>
                                        <td><?= esc(ucfirst($transaction->status)) ?></td>
                                        <td><?= esc($transaction->reference) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No transactions found yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
