(function () {
    // Smooth scrolling for in-page links such as #how.
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            var id = link.getAttribute('href');
            if (id.length < 2) {
                return;
            }
            var target = document.querySelector(id);
            if (!target) {
                return;
            }
            event.preventDefault();
            // Leave room for the sticky navbar so the heading is not hidden under it.
            var offset = 70;
            var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });

    // Fade elements in when they scroll into view. Older browsers show them straight away.
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var revealer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        reveals.forEach(function (item) {
            revealer.observe(item);
        });
    } else {
        reveals.forEach(function (item) {
            item.classList.add('is-visible');
        });
    }

    // Home page example chart: the bars grow to their set height when scrolled into view.
    var bars = document.querySelectorAll('.week-bar .fill');
    if (bars.length) {
        var growBars = function () {
            bars.forEach(function (bar) {
                bar.style.height = bar.getAttribute('data-height') + '%';
            });
        };
        if ('IntersectionObserver' in window) {
            var barWatcher = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    growBars();
                    barWatcher.disconnect();
                }
            }, { threshold: 0.3 });
            barWatcher.observe(document.querySelector('.week-bars'));
        } else {
            growBars();
        }
    }

    // Bootstrap tooltips must be switched on with JavaScript.
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (item) {
        new bootstrap.Tooltip(item);
    });

    // Features page: the category buttons show or hide matching cards without reloading.
    var filterButtons = document.querySelectorAll('.filter-btn');
    var featureItems = document.querySelectorAll('.feature-item');
    var emptyNote = document.getElementById('featureEmpty');
    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var group = button.getAttribute('data-filter');
            filterButtons.forEach(function (other) {
                other.classList.remove('active');
                other.setAttribute('aria-pressed', 'false');
            });
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
            var shown = 0;
            featureItems.forEach(function (item) {
                var match = group === 'all' || item.getAttribute('data-category') === group;
                item.classList.toggle('is-hidden', !match);
                if (match) {
                    shown += 1;
                }
            });
            if (emptyNote) {
                emptyNote.classList.toggle('d-none', shown !== 0);
            }
        });
    });

    // Fill the shared pop-up with the details of whichever card button was clicked.
    var modalElement = document.getElementById('featureModal');
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            document.getElementById('featureModalTitle').textContent = trigger.getAttribute('data-title');
            document.getElementById('featureModalBody').textContent = trigger.getAttribute('data-detail');
        });
    }

    // FAQ: click a question to open or close its answer.
    document.querySelectorAll('.faq-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var panel = document.getElementById(toggle.getAttribute('aria-controls'));
            var open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            panel.classList.toggle('d-none', open);
        });
    });

    // Ask for confirmation before submitting forms that carry a data-confirm message (workout delete).
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    // Live character counter under the contact message box.
    var counter = document.getElementById('messageCount');
    var messageBox = document.getElementById('message');
    if (counter && messageBox) {
        var update = function () {
            counter.textContent = messageBox.value.length + ' / ' + messageBox.maxLength;
        };
        messageBox.addEventListener('input', update);
        update();
    }
})();
