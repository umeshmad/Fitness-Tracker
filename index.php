<?php
require_once __DIR__ . '/includes/functions.php';

$base = '';
$pageTitle = 'Home';
$active = 'home';
$extraScripts = ['js/slider.js'];
// Sample week used only for the illustration bars on this page (not real user data).
$week = [['Mon', 45], ['Tue', 70], ['Wed', 30], ['Thu', 85], ['Fri', 55], ['Sat', 100], ['Sun', 40]];
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 hero-copy">
                <h1>Track the work. See the progress.</h1>
                <p class="lead mt-3">Stride Log keeps your steps, workouts and calories in one place, so you can see what your week actually looked like.</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php if (is_logged_in()): ?>
                        <a href="dashboard.php" class="btn btn-sun btn-lg">Open your dashboard</a>
                    <?php else: ?>
                        <a href="auth/register.php" class="btn btn-sun btn-lg">Create your free account</a>
                    <?php endif; ?>
                    <a href="#how" class="btn btn-outline-ink btn-lg">See how it works</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="slider" id="heroSlider" tabindex="0" aria-roledescription="carousel" aria-label="Fitness tracker highlights">
                    <div class="slide">
                        <img src="images/slide-steps.svg" alt="Footprints along curved running lanes under a yellow sun">
                        <div class="slide-caption">Log every session in under a minute</div>
                    </div>
                    <div class="slide">
                        <img src="images/slide-strength.svg" alt="A yellow barbell resting on a dark blue background">
                        <div class="slide-caption">Count strength days as well as steps</div>
                    </div>
                    <div class="slide">
                        <img src="images/slide-progress.svg" alt="Rising bars and a trend line on a warm gradient">
                        <div class="slide-caption">Watch your weekly totals climb</div>
                    </div>
                    <button type="button" class="slider-btn slider-prev" aria-label="Previous slide">&#8249;</button>
                    <button type="button" class="slider-btn slider-next" aria-label="Next slide">&#8250;</button>
                    <div class="slider-dots"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="how">
    <div class="container">
        <h2 class="section-title reveal">How it works</h2>
        <p class="text-secondary reveal">Three steps, and the third one happens on its own.</p>
        <div class="row g-4 mt-1">
            <div class="col-md-4 reveal">
                <div class="step-mark">1</div>
                <h3 class="h5">Log a workout</h3>
                <p>Pick the activity, enter the minutes, calories and steps, and save. Past days are easy to add too.</p>
            </div>
            <div class="col-md-4 reveal">
                <div class="step-mark">2</div>
                <h3 class="h5">Review your week</h3>
                <p>The dashboard charts the last seven days and lets you switch between calories, steps and active minutes.</p>
            </div>
            <div class="col-md-4 reveal">
                <div class="step-mark">3</div>
                <h3 class="h5">Spot the pattern</h3>
                <p>Filter your history by activity to see which sessions you repeat and which ones you skip.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section-mist" id="week">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 reveal">
                <h2 class="section-title">Your week at a glance</h2>
                <p>Every bar is one day. The tallest day is highlighted so you can see what a strong session looked like and aim for it again.</p>
                <a href="features.php" class="btn btn-outline-ink">Explore all features</a>
            </div>
            <div class="col-lg-7 reveal">
                <div class="week-bars" role="img" aria-label="Example week of active minutes, with Saturday the highest">
                    <?php foreach ($week as $day): ?>
                        <div class="week-bar<?= $day[1] === 100 ? ' peak' : '' ?>">
                            <span class="fill" data-height="<?= (int) $day[1] ?>"></span>
                            <small><?= e($day[0]) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="small text-secondary mt-2 mb-0">Example week shown for illustration.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h2 class="h3 mb-0">Ready to log your first workout?</h2>
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php" class="btn btn-sun btn-lg">Log a workout</a>
        <?php else: ?>
            <a href="auth/register.php" class="btn btn-sun btn-lg">Create your free account</a>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
