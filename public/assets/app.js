document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-password-toggle]');
    if (!button) {
        return;
    }

    var field = button.closest('.password-field');
    if (!field) {
        return;
    }

    var input = field.querySelector('input');
    if (!input) {
        return;
    }

    var shouldShow = input.type === 'password';
    input.type = shouldShow ? 'text' : 'password';
    button.classList.toggle('is-visible', shouldShow);
    button.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
});

function updateDoctorFields() {
    var roleSelect = document.querySelector('[data-role-select]');
    var doctorFields = document.querySelector('[data-doctor-fields]');
    if (!roleSelect || !doctorFields) {
        return;
    }

    doctorFields.hidden = roleSelect.value !== 'doctor';
}

document.addEventListener('change', function (event) {
    if (event.target.matches('[data-role-select]')) {
        updateDoctorFields();
    }
});

document.addEventListener('DOMContentLoaded', updateDoctorFields);
