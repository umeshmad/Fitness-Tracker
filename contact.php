<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
// Keep what the user typed so the form can be refilled after an error.
$old = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $email = strtolower(clean($_POST['email'] ?? ''));
    $message = clean($_POST['message'] ?? '');
    $old = ['name' => $name, 'email' => $email, 'message' => $message];

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Reload the page and try again.';
    }
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    } elseif (text_length($name) > 80) {
        $errors['name'] = 'Name must be at most 80 characters.';
    }
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!valid_email($email)) {
        $errors['email'] = 'Enter a valid email address, like name@example.com.';
    }
    if ($message === '') {
        $errors['message'] = 'Message is required.';
    } elseif (text_length($message) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    } elseif (text_length($message) > 1000) {
        $errors['message'] = 'Message must be at most 1000 characters.';
    }

    // Messages are stored in the messages table and read through phpMyAdmin.
    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Message sent. We will reply to ' . $email . ' soon.');
        redirect('contact.php');
    }
}

$base = '';
$pageTitle = 'Contact';
$active = 'contact';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <h1>Contact us</h1>
        <p class="lead mb-0">Questions, bugs or ideas for a new feature? Send a message and it lands straight in our inbox.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h2 class="h4">Before you write</h2>
                <p>Most questions are answered on the <a href="features.php#faq">Features page</a>. If you cannot find your answer there, tell us what you were trying to do and what happened instead.</p>
                <p class="mb-0">Messages are stored securely and read by the project team.</p>
            </div>
            <div class="col-lg-7">
                <?php if (isset($errors['form'])): ?>
                    <div class="alert alert-danger"><?= e($errors['form']) ?></div>
                <?php endif; ?>
                <form method="post" action="contact.php" data-validate class="panel">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Your name</label>
                        <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="80" data-label="Name" autocomplete="name">
                        <div class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= e($old['email']) ?>" required maxlength="120" data-label="Email" autocomplete="email">
                        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>" id="message" name="message" rows="6" required minlength="10" maxlength="1000" data-label="Message"><?= e($old['message']) ?></textarea>
                        <div class="invalid-feedback"><?= e($errors['message'] ?? '') ?></div>
                        <div class="form-text text-end" id="messageCount"></div>
                    </div>
                    <button type="submit" class="btn btn-teal">Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
