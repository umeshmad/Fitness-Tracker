// Client-side validation for every form marked with data-validate.
// Rules come from the field's own attributes (required, min/max, data-pattern, data-match).
// The server repeats these checks, so this is only for quick feedback.
(function () {
    // Simple email shape check: text@domain.tld
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    function feedbackFor(field) {
        var holder = field.parentElement;
        var box = holder.querySelector('.invalid-feedback');
        if (!box) {
            box = document.createElement('div');
            box.className = 'invalid-feedback';
            holder.appendChild(box);
        }
        return box;
    }

    // Returns an error message for one field, or an empty string when the value is valid.
    function errorFor(field) {
        var value = field.value.trim();
        var label = field.getAttribute('data-label') || 'This field';

        if (field.hasAttribute('required') && value === '') {
            return label + ' is required.';
        }
        if (value === '') {
            return '';
        }
        if (field.type === 'email' && !emailPattern.test(value)) {
            return 'Enter a valid email address, like name@example.com.';
        }
        if (field.minLength > 0 && value.length < field.minLength) {
            return label + ' must be at least ' + field.minLength + ' characters.';
        }
        if (field.maxLength > 0 && value.length > field.maxLength) {
            return label + ' must be at most ' + field.maxLength + ' characters.';
        }
        if (field.getAttribute('data-pattern')) {
            var rule = new RegExp(field.getAttribute('data-pattern'));
            if (!rule.test(field.value)) {
                return field.getAttribute('data-pattern-message') || label + ' is not in the right format.';
            }
        }
        if (field.type === 'number') {
            var number = Number(value);
            if (isNaN(number) || !/^-?\d+$/.test(value)) {
                return label + ' must be a whole number.';
            }
            if (field.min !== '' && number < Number(field.min)) {
                return label + ' must be at least ' + field.min + '.';
            }
            if (field.max !== '' && number > Number(field.max)) {
                return label + ' must be at most ' + field.max + '.';
            }
        }
        if (field.type === 'date' && field.max && value > field.max) {
            return label + ' cannot be in the future.';
        }
        if (field.getAttribute('data-match')) {
            var other = document.querySelector(field.getAttribute('data-match'));
            if (other && other.value !== field.value) {
                return 'Passwords do not match.';
            }
        }
        return '';
    }

    // Validate one field and show or clear its message and Bootstrap valid/invalid styling.
    function check(field) {
        var message = errorFor(field);
        var box = feedbackFor(field);
        if (message) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            box.textContent = message;
            return false;
        }
        field.classList.remove('is-invalid');
        if (field.value.trim() !== '') {
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
        }
        box.textContent = '';
        return true;
    }

    function fieldsOf(form) {
        return Array.prototype.slice.call(form.querySelectorAll('input:not([type=hidden]), select, textarea'));
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        // Turn off the browser's default pop-ups so our own messages show instead.
        form.setAttribute('novalidate', 'novalidate');
        var fields = fieldsOf(form);

        fields.forEach(function (field) {
            field.addEventListener('blur', function () {
                check(field);
            });
            field.addEventListener('input', function () {
                if (field.classList.contains('is-invalid') || field.classList.contains('is-valid')) {
                    check(field);
                }
                // Re-check the confirm box when the password changes.
                if (field.id === 'password') {
                    var confirm = form.querySelector('[data-match="#password"]');
                    if (confirm && confirm.value !== '') {
                        check(confirm);
                    }
                }
            });
        });

        form.addEventListener('submit', function (event) {
            var firstBad = null;
            fields.forEach(function (field) {
                if (!check(field) && !firstBad) {
                    firstBad = field;
                }
            });
            // Block submission and move focus to the first invalid field.
            if (firstBad) {
                event.preventDefault();
                firstBad.focus();
            }
        });
    });
})();
