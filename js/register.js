function checkUsername() {
    var username = document.getElementById('username').value;
    var usernameError = document.getElementById('usernameError');

    usernameError.innerHTML = '';

    if (username.trim() === '') {
        usernameError.innerHTML = 'Username cannot be empty';
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_username', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText.trim() !== '') {
                usernameError.innerHTML = xhr.responseText;
            }
        }
    };
    xhr.send('username=' + encodeURIComponent(username));
}

function checkEmail() {
    var email = document.getElementById('email').value;
    var emailError = document.getElementById('emailError');

    emailError.innerHTML = '';

    if (email.trim() === '') {
        emailError.innerHTML = 'Email cannot be empty';
        return;
    }
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_email', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText.trim() !== '') {
                emailError.innerHTML = xhr.responseText;
            }
        }
    };
    xhr.send('email=' + encodeURIComponent(email));
}

function validateForm() {
    var usernameError = document.getElementById('usernameError').innerHTML;
    var emailError = document.getElementById('emailError').innerHTML;
    var passwordError = document.getElementById('passwordError').innerHTML;

    if (usernameError || emailError || passwordError) {
        alert('Please fix the errors before submitting the form.');
        return false;
    }
    return true;
}