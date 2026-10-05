<?php
require_once __DIR__ . '/includes/functions.php';

$base = '';
$pageTitle = 'Features';
$active = 'features';
// Feature cards: [category, title, short text, pop-up detail]. The category drives the filter buttons.
$features = [
    ['tracking', 'Quick workout log', 'Record an activity with its duration, calories and steps in one short form.', 'Choose from running, walking, cycling, swimming, strength training, yoga, HIIT or other. Dates can be today or any day in the past, so you can catch up after a busy week.'],
    ['tracking', 'Step counter entries', 'Add the steps from your phone or watch to any workout.', 'Steps are stored per workout and added up per day, so a morning walk and an evening run both count toward the same daily total.'],
    ['insights', 'Weekly charts', 'See calories, steps and active minutes for the last seven days.', 'Use the metric buttons above the chart to switch between calories burned, steps and minutes active. Days without a workout show as zero.'],
    ['insights', 'Activity filter', 'Narrow your history to one kind of workout.', 'Pick an activity from the filter above your workout table and the rows update immediately, with no page reload.'],
    ['account', 'Private accounts', 'Your workouts are visible only to you.', 'Passwords are stored as secure hashes, and every workout query is limited to the logged-in account.'],
    ['account', 'Delete anytime', 'Remove a mistaken entry with one click.', 'Each row has a delete button with a confirmation step, so a stray click does not wipe out a workout.'],
];
// FAQ entries: [question, answer].
$questions = [
    ['Is Stride Log free?', 'Yes. Creating an account and logging workouts costs nothing.'],
    ['Can I log a workout from last week?', 'Yes. Pick any past date in the log form. Future dates are not accepted.'],
    ['Where is my data stored?', 'In a MySQL database on the server running this site. Only your account can read your workouts.'],
    ['What if I forget my password?', 'Send us a message from the Contact page and include the email on your account.'],
];
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <h1>Features</h1>
        <p class="lead mb-0">Everything you need to log training and see how it adds up.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 mb-4" role="group" aria-label="Filter features">
            <button type="button" class="btn btn-outline-ink filter-btn active" data-filter="all" aria-pressed="true">All features</button>
            <button type="button" class="btn btn-outline-ink filter-btn" data-filter="tracking" aria-pressed="false">Tracking</button>
            <button type="button" class="btn btn-outline-ink filter-btn" data-filter="insights" aria-pressed="false">Insights</button>
            <button type="button" class="btn btn-outline-ink filter-btn" data-filter="account" aria-pressed="false">Account</button>
        </div>
        <div class="row g-4" id="featureGrid">
            <?php foreach ($features as $feature): ?>
                <div class="col-md-6 col-lg-4 feature-item" data-category="<?= e($feature[0]) ?>">
                    <div class="feature-card d-flex flex-column">
                        <span class="badge text-bg-light align-self-start mb-2" data-bs-toggle="tooltip" data-bs-title="Filter by this group using the buttons above"><?= e(ucfirst($feature[0])) ?></span>
                        <h3><?= e($feature[1]) ?></h3>
                        <p class="flex-grow-1"><?= e($feature[2]) ?></p>
                        <button type="button" class="btn btn-sm btn-outline-ink align-self-start" data-bs-toggle="modal" data-bs-target="#featureModal" data-title="<?= e($feature[1]) ?>" data-detail="<?= e($feature[3]) ?>">Read details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-secondary d-none mt-3" id="featureEmpty">No features in this group yet.</p>
    </div>
</section>

<section class="section section-mist" id="faq">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="section-title">Common questions</h2>
                <?php foreach ($questions as $i => $item): ?>
                    <div class="faq-item">
                        <button type="button" class="faq-toggle" aria-expanded="false" aria-controls="faq-panel-<?= $i ?>"><?= e($item[0]) ?></button>
                        <div class="faq-panel d-none" id="faq-panel-<?= $i ?>"><?= e($item[1]) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="featureModal" tabindex="-1" aria-labelledby="featureModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="featureModalTitle"></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="featureModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-ink" data-bs-dismiss="modal">Close</button>
                <a href="auth/register.php" class="btn btn-sun">Create your free account</a>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
