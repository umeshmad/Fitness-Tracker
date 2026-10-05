<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Only logged-in users may view the dashboard.
require_login();

// Every query below filters by this ID so users only ever see their own data.
$userId = (int) $_SESSION['user_id'];
$today = date('Y-m-d');
$errors = [];
$old = ['activity' => '', 'duration' => '', 'calories_burned' => '', 'steps' => '', 'workout_date' => $today];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // One handler for both forms on this page: 'add' saves a workout, 'delete' removes one.
    $action = $_POST['action'] ?? '';

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Reload the page and try again.';
    } elseif ($action === 'delete') {
        $workoutId = filter_var($_POST['workout_id'] ?? '', FILTER_VALIDATE_INT);
        if ($workoutId !== false) {
            // user_id in the WHERE clause stops users deleting each other's workouts.
            $stmt = $conn->prepare('DELETE FROM workouts WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $workoutId, $userId);
            $stmt->execute();
            $deleted = $stmt->affected_rows;
            $stmt->close();
            set_flash($deleted ? 'success' : 'warning', $deleted ? 'Workout deleted.' : 'That workout was not found.');
        }
        redirect('dashboard.php');
    } elseif ($action === 'add') {
        $old = [
            'activity' => clean($_POST['activity'] ?? ''),
            'duration' => clean($_POST['duration'] ?? ''),
            'calories_burned' => clean($_POST['calories_burned'] ?? ''),
            'steps' => clean($_POST['steps'] ?? ''),
            'workout_date' => clean($_POST['workout_date'] ?? ''),
        ];

        if ($old['activity'] === '') {
            $errors['activity'] = 'Choose an activity.';
        } elseif (!in_array($old['activity'], activity_options(), true)) {
            $errors['activity'] = 'Choose an activity from the list.';
        }

        // filter_var confirms each value is a whole number inside its allowed range.
        $duration = filter_var($old['duration'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 600]]);
        if ($old['duration'] === '') {
            $errors['duration'] = 'Duration is required.';
        } elseif ($duration === false) {
            $errors['duration'] = 'Duration must be a whole number from 1 to 600 minutes.';
        }

        $calories = filter_var($old['calories_burned'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 5000]]);
        if ($old['calories_burned'] === '') {
            $errors['calories_burned'] = 'Calories is required.';
        } elseif ($calories === false) {
            $errors['calories_burned'] = 'Calories must be a whole number from 0 to 5000.';
        }

        $steps = 0;
        if ($old['steps'] !== '') {
            $steps = filter_var($old['steps'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100000]]);
            if ($steps === false) {
                $errors['steps'] = 'Steps must be a whole number from 0 to 100000.';
            }
        }

        if ($old['workout_date'] === '') {
            $errors['workout_date'] = 'Date is required.';
        } elseif (!valid_date($old['workout_date'])) {
            $errors['workout_date'] = 'Enter a valid date.';
        } elseif ($old['workout_date'] > $today) {
            $errors['workout_date'] = 'Date cannot be in the future.';
        }

        // Server-side checks repeat the JavaScript ones, which can be bypassed.
        if (!$errors) {
            $stmt = $conn->prepare('INSERT INTO workouts (user_id, activity, duration, calories_burned, steps, workout_date) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isiiis', $userId, $old['activity'], $duration, $calories, $steps, $old['workout_date']);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Workout saved.');
            redirect('dashboard.php');
        }
    }
}

// Totals and chart data cover the last 7 days, including today.
$weekStart = date('Y-m-d', strtotime('-6 days'));

$stmt = $conn->prepare('SELECT COUNT(*) AS sessions, COALESCE(SUM(duration), 0) AS minutes, COALESCE(SUM(calories_burned), 0) AS calories, COALESCE(SUM(steps), 0) AS steps FROM workouts WHERE user_id = ? AND workout_date >= ?');
$stmt->bind_param('is', $userId, $weekStart);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare('SELECT workout_date, SUM(duration) AS minutes, SUM(calories_burned) AS calories, SUM(steps) AS steps FROM workouts WHERE user_id = ? AND workout_date >= ? GROUP BY workout_date');
$stmt->bind_param('is', $userId, $weekStart);
$stmt->execute();
$daily = [];
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $daily[$row['workout_date']] = $row;
}
$stmt->close();

// Build one value per day (0 when nothing was logged) so the chart always shows 7 bars.
$chart = ['labels' => [], 'calories' => [], 'steps' => [], 'minutes' => []];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime('-' . $i . ' days'));
    $chart['labels'][] = date('D j', strtotime($day));
    $chart['calories'][] = isset($daily[$day]) ? (int) $daily[$day]['calories'] : 0;
    $chart['steps'][] = isset($daily[$day]) ? (int) $daily[$day]['steps'] : 0;
    $chart['minutes'][] = isset($daily[$day]) ? (int) $daily[$day]['minutes'] : 0;
}

// Workout history table: newest first, limited to 100 rows.
$stmt = $conn->prepare('SELECT id, activity, duration, calories_burned, steps, workout_date FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT 100');
$stmt->bind_param('i', $userId);
$stmt->execute();
$workouts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$base = '';
$pageTitle = 'Dashboard';
$active = 'dashboard';
$extraScripts = ['js/chart.min.js', 'js/dashboard.js'];
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <h1>Hi, <?= e($_SESSION['username']) ?></h1>
        <p class="lead mb-0">Your last seven days at a glance.</p>
    </div>
</section>

<section class="section pb-0">
    <div class="container">
        <?php if (isset($errors['form'])): ?>
            <div class="alert alert-danger"><?= e($errors['form']) ?></div>
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= number_format((int) $totals['sessions']) ?></div><div class="stat-label">Workouts this week</div></div></div>
            <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= number_format((int) $totals['minutes']) ?></div><div class="stat-label">Minutes active</div></div></div>
            <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= number_format((int) $totals['calories']) ?></div><div class="stat-label">Calories burned</div></div></div>
            <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= number_format((int) $totals['steps']) ?></div><div class="stat-label">Steps</div></div></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="panel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h5 mb-0">Last 7 days</h2>
                        <div class="btn-group" role="group" aria-label="Chart metric">
                            <button type="button" class="btn btn-sm btn-outline-ink metric-btn active" data-metric="calories" aria-pressed="true">Calories</button>
                            <button type="button" class="btn btn-sm btn-outline-ink metric-btn" data-metric="steps" aria-pressed="false">Steps</button>
                            <button type="button" class="btn btn-sm btn-outline-ink metric-btn" data-metric="minutes" aria-pressed="false">Minutes</button>
                        </div>
                    </div>
                    <div class="chart-box"><canvas id="weekChart" aria-label="Bar chart of the last seven days" role="img"></canvas></div>
                    <?php // Chart values are passed to JavaScript as JSON; the JSON_HEX flags keep it safe inside a script tag. ?>
                    <script type="application/json" id="chartData"><?= json_encode($chart, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
                </div>
            </div>
            <div class="col-lg-5">
                <form method="post" action="dashboard.php" class="panel" data-validate>
                    <h2 class="h5 mb-3">Log a workout</h2>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="activity" class="form-label">Activity</label>
                        <select class="form-select<?= isset($errors['activity']) ? ' is-invalid' : '' ?>" id="activity" name="activity" required data-label="Activity">
                            <option value="">Choose an activity</option>
                            <?php foreach (activity_options() as $option): ?>
                                <option value="<?= e($option) ?>"<?= $old['activity'] === $option ? ' selected' : '' ?>><?= e($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['activity'] ?? '') ?></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="duration" class="form-label">Minutes</label>
                            <input type="number" class="form-control<?= isset($errors['duration']) ? ' is-invalid' : '' ?>" id="duration" name="duration" value="<?= e($old['duration']) ?>" required min="1" max="600" data-label="Duration">
                            <div class="invalid-feedback"><?= e($errors['duration'] ?? '') ?></div>
                        </div>
                        <div class="col-6">
                            <label for="calories_burned" class="form-label">Calories</label>
                            <input type="number" class="form-control<?= isset($errors['calories_burned']) ? ' is-invalid' : '' ?>" id="calories_burned" name="calories_burned" value="<?= e($old['calories_burned']) ?>" required min="0" max="5000" data-label="Calories">
                            <div class="invalid-feedback"><?= e($errors['calories_burned'] ?? '') ?></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label for="steps" class="form-label">Steps (optional)</label>
                            <input type="number" class="form-control<?= isset($errors['steps']) ? ' is-invalid' : '' ?>" id="steps" name="steps" value="<?= e($old['steps']) ?>" min="0" max="100000" data-label="Steps">
                            <div class="invalid-feedback"><?= e($errors['steps'] ?? '') ?></div>
                        </div>
                        <div class="col-6">
                            <label for="workout_date" class="form-label">Date</label>
                            <input type="date" class="form-control<?= isset($errors['workout_date']) ? ' is-invalid' : '' ?>" id="workout_date" name="workout_date" value="<?= e($old['workout_date']) ?>" required max="<?= e($today) ?>" data-label="Date">
                            <div class="invalid-feedback"><?= e($errors['workout_date'] ?? '') ?></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal w-100">Save workout</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section section-mist pt-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h4 mb-0">Workout history</h2>
            <div class="d-flex align-items-center gap-2">
                <label for="activityFilter" class="form-label mb-0">Show</label>
                <select id="activityFilter" class="form-select form-select-sm w-auto">
                    <option value="all">All activities</option>
                    <?php foreach (activity_options() as $option): ?>
                        <option value="<?= e($option) ?>"><?= e($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php if (!$workouts): ?>
            <div class="panel text-center">No workouts yet. Use the form above to log your first one.</div>
        <?php else: ?>
            <div class="table-wrap panel p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Date</th><th>Activity</th><th class="text-end">Minutes</th><th class="text-end">Calories</th><th class="text-end">Steps</th><th></th></tr>
                    </thead>
                    <tbody id="workoutRows">
                        <?php foreach ($workouts as $w): ?>
                            <tr data-activity="<?= e($w['activity']) ?>">
                                <td><?= e(date('D, j M Y', strtotime($w['workout_date']))) ?></td>
                                <td><?= e($w['activity']) ?></td>
                                <td class="text-end"><?= (int) $w['duration'] ?></td>
                                <td class="text-end"><?= number_format((int) $w['calories_burned']) ?></td>
                                <td class="text-end"><?= number_format((int) $w['steps']) ?></td>
                                <td class="text-end">
                                    <form method="post" action="dashboard.php" data-confirm="Delete this workout?" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="workout_id" value="<?= (int) $w['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="noMatchRow" class="d-none"><td colspan="6" class="text-center text-secondary py-4">No workouts match this activity.</td></tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
