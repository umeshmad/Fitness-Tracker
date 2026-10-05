// Hero image slider: auto-advances every 5 seconds, with dots, arrow buttons and arrow keys.
(function () {
    var slider = document.getElementById('heroSlider');
    if (!slider) {
        return;
    }
    var slides = slider.querySelectorAll('.slide');
    var dotsBox = slider.querySelector('.slider-dots');
    var current = 0;
    var timer = null;
    var dots = [];

    // Create one navigation dot per slide.
    slides.forEach(function (slide, index) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'slider-dot';
        dot.setAttribute('aria-label', 'Show slide ' + (index + 1));
        dot.addEventListener('click', function () {
            show(index);
            restart();
        });
        dotsBox.appendChild(dot);
        dots.push(dot);
    });

    // Show one slide; the modulo makes the slider wrap around at both ends.
    function show(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            slide.classList.toggle('is-active', i === current);
            slide.setAttribute('aria-hidden', i === current ? 'false' : 'true');
        });
        dots.forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === current);
        });
    }

    function start() {
        timer = window.setInterval(function () {
            show(current + 1);
        }, 5000);
    }

    function stop() {
        window.clearInterval(timer);
    }

    // Restart the timer after manual navigation so the slide does not change right after a click.
    function restart() {
        stop();
        start();
    }

    slider.querySelector('.slider-prev').addEventListener('click', function () {
        show(current - 1);
        restart();
    });
    slider.querySelector('.slider-next').addEventListener('click', function () {
        show(current + 1);
        restart();
    });
    // Pause the automatic change while the pointer is over the slider.
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') {
            show(current - 1);
        } else if (event.key === 'ArrowRight') {
            show(current + 1);
        }
    });

    show(0);
    start();
})();
