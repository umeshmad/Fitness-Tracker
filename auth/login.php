<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Logged-in users go straight to the dashboard.
if (is_logged_in()) {
    redirect('../dashboard.php');
}

$errors = [];
$identity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = clean($_POST['identity'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Reload the page and try again.';
    }
    if ($identity === '') {
        $errors['identity'] = 'Username or email is required.';
    }
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (!$errors) {
        // Users can log in with either their username or their email.
        $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $identity, $identity);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // New session ID after login prevents session fixation.
            session_regenerate_id(true);
            // These two values mark the user as logged in (see is_logged_in()).
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            set_flash('success', 'Welcome back, ' . $user['username'] . '.');
            redirect('../dashboard.php');
        }
        // One generic message so attackers cannot tell which part was wrong.
        $errors['form'] = 'Username, email or password is incorrect.';
    }
}

$base = '../';
$pageTitle = 'Log in';
$active = 'login';
require __DIR__ . '/../includes/header.php';
?>
<section class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="auth-card">
                    <h1 class="h3 mb-1">Log in</h1>
                    <p class="text-secondary">Pick up where your last workout left off.</p>
                    <?php if (isset($errors['form'])): ?>
                        <div class="alert alert-danger"><?= e($errors['form']) ?></div>
                    <?php endif; ?>
                    <form method="post" action="login.php" data-validate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="identity" class="form-label">Username or email</label>
                            <input type="text" class="form-control<?= isset($errors['identity']) ? ' is-invalid' : '' ?>" id="identity" name="identity" value="<?= e($identity) ?>" required data-label="Username or email" autocomplete="username">
                            <div class="invalid-feedback"><?= e($errors['identity'] ?? '') ?></div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="password" name="password" required data-label="Password" autocomplete="current-password">
                            <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                        </div>
                        <button type="submit" class="btn btn-teal w-100">Log in</button>
                    </form>
                    <p class="mt-3 mb-0 text-center">New here? <a href="register.php">Create an account</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
