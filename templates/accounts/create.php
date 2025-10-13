<?php $title = 'Create Account - Personal Finance Tracker'; ?>
<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create New Account</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($data['name'] ?? '') ?>" 
                               placeholder="e.g., Main Checking" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Account Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select type...</option>
                            <option value="checking" <?= ($data['type'] ?? '') === 'checking' ? 'selected' : '' ?>>Checking</option>
                            <option value="savings" <?= ($data['type'] ?? '') === 'savings' ? 'selected' : '' ?>>Savings</option>
                            <option value="credit_card" <?= ($data['type'] ?? '') === 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                            <option value="investment" <?= ($data['type'] ?? '') === 'investment' ? 'selected' : '' ?>>Investment</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="balance" class="form-label">Initial Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" id="balance" name="balance" 
                                   value="