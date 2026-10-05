<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Logged-in users have no reason to see this page.
if (is_logged_in()) {
    redirect('../dashboard.php');
}

$errors = [];
// Keep what the user typed so the form can be refilled after an error.
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email = strtolower(clean($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    $old = ['username' => $username, 'email' => $email];

    // Reject the request if the form's CSRF token is missing or wrong.
    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Reload the page and try again.';
    }
    if ($username === '') {
        $errors['username'] = 'Username is required.';
    } elseif (!valid_username($username)) {
        $errors['username'] = 'Use 3 to 30 letters, numbers or underscores.';
    }
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!valid_email($email)) {
        $errors['email'] = 'Enter a valid email address, like name@example.com.';
    }
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (!valid_password($password)) {
        $errors['password'] = 'Use at least 8 characters with a letter and a number.';
    }
    if ($confirm !== $password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check for an existing username or email before inserting.
    if (!$errors) {
        $stmt = $conn->prepare('SELECT username, email FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (strcasecmp($row['username'], $username) === 0) {
                $errors['username'] = 'That username is taken. Choose another.';
            }
            if (strcasecmp($row['email'], $email) === 0) {
                $errors['email'] = 'An account with this email already exists. Log in instead.';
            }
        }
        $stmt->close();
    }

    if (!$errors) {
        // Never store the plain password; only the hash is saved.
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $hash);
        try {
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Account created. Log in to start tracking.');
            redirect('login.php');
        } catch (mysqli_sql_exception $ex) {
            // Fallback for a duplicate that slipped past the check above (unique keys in the table).
            $errors['form'] = 'That username or email is already registered.';
        }
    }
}

$base = '../';
$pageTitle = 'Create account';
$active = 'register';
require __DIR__ . '/../includes/header.php';
?>
<section class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="auth-card">
                    <h1 class="h3 mb-1">Create your account</h1>
                    <p class="text-secondary">Start logging workouts in under a minute.</p>
                    <?php if (isset($errors['form'])): ?>
                        <div class="alert alert-danger"><?= e($errors['form']) ?></div>
                    <?php endif; ?>
                    <form method="post" action="register.php" data-validate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>" id="username" name="username" value="<?= e($old['username']) ?>" required minlength="3" maxlength="30" data-label="Username" data-pattern="^[A-Za-z0-9_]+$" data-pattern-message="Use only letters, numbers and underscores." autocomplete="username">
                            <div class="invalid-feedback"><?= e($errors['username'] ?? '') ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= e($old['email']) ?>" required maxlength="120" data-label="Email" autocomplete="email">
                            <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="password" name="password" required minlength="8" data-label="Password" data-pattern="^(?=.*[A-Za-z])(?=.*[0-9]).+$" data-pattern-message="Use at least one letter and one number." autocomplete="new-password">
                            <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                            <div class="form-text">At least 8 characters, with a letter and a number.</div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm password</label>
                            <input type="password" class="form-control<?= isset($errors['confirm_password']) ? ' is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required data-label="Confirm password" data-match="#password" autocomplete="new-password">
                            <div class="invalid-feedback"><?= e($errors['confirm_password'] ?? '') ?></div>
                        </div>
                        <button type="submit" class="btn btn-teal w-100">Create account</button>
                    </form>
                    <p class="mt-3 mb-0 text-center">Already have an account? <a href="login.php">Log in</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
