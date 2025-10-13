<?php $title = 'Accounts - Personal Finance Tracker'; ?>
<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Accounts</h2>
    <a href="/accounts-create.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Account
    </a>
</div>

<!-- Total Balance Card -->
<div class="card mb-4 bg-primary text-white">
    <div class="card-body">
        <h5 class="card-title">Total Balance</h5>
        <h2 class="mb-0"><?= number_format($totalBalance, 2) ?> KES</h2>
    </div>
</div>

<!-- Accounts List -->
<div class="row">
    <?php if (empty($accounts)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                No accounts found. <a href="/accounts-create.php">Create your first account</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($accounts as $account): ?>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title"><?= htmlspecialchars($account['name']) ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-<?= $this->getAccountIcon($account['type']) ?>"></i>
                                    <?= ucfirst(str_replace('_', ' ', $account['type'])) ?>
                                </p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="/accounts-edit.php?id=<?= $account['id'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="/accounts-delete.php?id=<?= $account['id'] ?>" 
                                           onclick="return confirm('Are you sure you want to delete this account?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h3 class="mb-0 <?= $account['balance'] < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($account['balance'], 2) ?> KES
                        </h3>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php

// Helper function for account icons
function getAccountIcon($type) {
    $icons = [
        'checking' => 'wallet2',
        'savings' => 'piggy-bank',
        'credit_card' => 'credit-card',
        'investment' => 'graph-up'
    ];
    return $icons[$type] ?? 'wallet2';
}

include __DIR__ . '/../layout.php';
?>