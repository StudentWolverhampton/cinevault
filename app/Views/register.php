<?= $this->include('layout/header') ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card border-0 shadow">
            <div class="card-header text-center border-0" style="background-color:#0a0a0a;">
                <h3 class="text-warning mb-0 py-2">🎬 Create Account</h3>
            </div>
            <div class="card-body p-4">

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/register') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label" style="color: var(--text-main);">Username</label>
                        <input type="text" name="username" id="username"
                               class="form-control <?= (isset($validation) && $validation && $validation->hasError('username')) ? 'is-invalid' : '' ?>"
                               style="background: var(--bg-input); color: var(--text-main); border-color: var(--border);"
                               value="<?= esc($old['username'] ?? '') ?>"
                               placeholder="Choose a username" autocomplete="username">
                        <?php if (isset($validation) && $validation && $validation->hasError('username')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                        <?php else: ?>
                            <div class="form-text" style="color: var(--text-muted);">3–50 characters.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" style="color: var(--text-main);">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control <?= (isset($validation) && $validation && $validation->hasError('password')) ? 'is-invalid' : '' ?>"
                                   style="background: var(--bg-input); color: var(--text-main); border-color: var(--border);"
                                   placeholder="At least 6 characters" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">👁</button>
                            <?php if (isset($validation) && $validation && $validation->hasError('password')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label" style="color: var(--text-main);">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password"
                               class="form-control <?= (isset($validation) && $validation && $validation->hasError('confirm_password')) ? 'is-invalid' : '' ?>"
                               style="background: var(--bg-input); color: var(--text-main); border-color: var(--border);"
                               placeholder="Re-enter your password" autocomplete="new-password">
                        <?php if (isset($validation) && $validation && $validation->hasError('confirm_password')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('confirm_password') ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold">Create Account</button>
                </form>

                <hr style="border-color: var(--border);" class="mt-4">
                <p class="text-center mb-0" style="color: var(--text-muted);">
                    Already have an account?
                    <a href="<?= base_url('user/login') ?>" class="text-warning text-decoration-none fw-bold">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd = document.getElementById('password');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
        this.textContent = pwd.type === 'password' ? '👁' : '🙈';
    });
</script>

<?= $this->include('layout/footer') ?>
