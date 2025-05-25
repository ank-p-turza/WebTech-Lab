function validateName(name) {
    return name && name.trim().length >= 2 && name.includes(' ');
}

function validateEmail(email) {
    if (!email) return false;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}


function validatePassword(password) {
    if (!password) return false;
    return password.length >= 8;
}


function updateFormValidity() {
    const isNameValid = validateName(fullnameInput.value);
    const isEmailValid = validateEmail(emailInput.value);
    const isPasswordValid = validatePassword(passwordInput.value);
    const doPasswordsMatch = passwordInput.value === confirmPasswordInput.value && confirmPasswordInput.value;
    const termsAccepted = termsCheckbox.checked;
    
    registerBtn.disabled = !(isNameValid && isEmailValid && isPasswordValid && doPasswordsMatch && termsAccepted);
}

// Input elements
const fullnameInput = document.getElementById('fullname');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm-password');
const termsCheckbox = document.getElementById('terms');

// Error messages
const fullnameError = document.getElementById('fullname-error');
const emailError = document.getElementById('email-error');
const passwordError = document.getElementById('password-error');
const confirmPasswordError = document.getElementById('confirm-password-error');
const termsError = document.getElementById('terms-error');

// Validation icons
const fullnameIcon = document.getElementById('fullname-icon');
const emailIcon = document.getElementById('email-icon');
const passwordIcon = document.getElementById('password-icon');
const confirmPasswordIcon = document.getElementById('confirm-password-icon');

// Register button
const registerBtn = document.getElementById('register-btn');

// Real-time name validation
fullnameInput.addEventListener('input', () => {
    const isValid = validateName(fullnameInput.value);
    
    if (fullnameInput.value) {
        fullnameInput.classList.toggle('error', !isValid);
        fullnameInput.classList.toggle('valid', isValid);
        fullnameError.classList.toggle('visible', !isValid);
        
        fullnameIcon.classList.toggle('error', !isValid);
        fullnameIcon.classList.toggle('valid', isValid);
        fullnameIcon.textContent = isValid ? '✓' : '✗';
    } else {
        fullnameInput.classList.remove('error', 'valid');
        fullnameError.classList.remove('visible');
        fullnameIcon.classList.remove('error', 'valid');
    }
    
    updateFormValidity();
});

// Real-time email validation
emailInput.addEventListener('input', () => {
    const isValid = validateEmail(emailInput.value);
    
    if (emailInput.value) {
        emailInput.classList.toggle('error', !isValid);
        emailInput.classList.toggle('valid', isValid);
        emailError.classList.toggle('visible', !isValid);
        
        emailIcon.classList.toggle('error', !isValid);
        emailIcon.classList.toggle('valid', isValid);
        emailIcon.textContent = isValid ? '✓' : '✗';
    } else {
        emailInput.classList.remove('error', 'valid');
        emailError.classList.remove('visible');
        emailIcon.classList.remove('error', 'valid');
    }
    
    updateFormValidity();
});

// Real-time password validation and strength meter
passwordInput.addEventListener('input', () => {
    const isValid = validatePassword(passwordInput.value);
    
    if (passwordInput.value) {
        passwordInput.classList.toggle('error', !isValid);
        passwordInput.classList.toggle('valid', isValid);
        passwordError.classList.toggle('visible', !isValid);
        
        passwordIcon.classList.toggle('error', !isValid);
        passwordIcon.classList.toggle('valid', isValid);
        passwordIcon.textContent = isValid ? '✓' : '✗';
        
    
        
        // Also update confirm password validation if it has content
        if (confirmPasswordInput.value) {
            const doPasswordsMatch = confirmPasswordInput.value === passwordInput.value;
            confirmPasswordInput.classList.toggle('error', !doPasswordsMatch);
            confirmPasswordInput.classList.toggle('valid', doPasswordsMatch);
            confirmPasswordError.classList.toggle('visible', !doPasswordsMatch);
            
            confirmPasswordIcon.classList.toggle('error', !doPasswordsMatch);
            confirmPasswordIcon.classList.toggle('valid', doPasswordsMatch);
            confirmPasswordIcon.textContent = doPasswordsMatch ? '✓' : '✗';
        }
    } else {
        passwordInput.classList.remove('error', 'valid');
        passwordError.classList.remove('visible');
        passwordIcon.classList.remove('error', 'valid');
    }
    
    updateFormValidity();
});

// Real-time confirm password validation
confirmPasswordInput.addEventListener('input', () => {
    const doPasswordsMatch = confirmPasswordInput.value === passwordInput.value;
    
    if (confirmPasswordInput.value) {
        confirmPasswordInput.classList.toggle('error', !doPasswordsMatch);
        confirmPasswordInput.classList.toggle('valid', doPasswordsMatch);
        confirmPasswordError.classList.toggle('visible', !doPasswordsMatch);
        
        confirmPasswordIcon.classList.toggle('error', !doPasswordsMatch);
        confirmPasswordIcon.classList.toggle('valid', doPasswordsMatch);
        confirmPasswordIcon.textContent = doPasswordsMatch ? '✓' : '✗';
    } else {
        confirmPasswordInput.classList.remove('error', 'valid');
        confirmPasswordError.classList.remove('visible');
        confirmPasswordIcon.classList.remove('error', 'valid');
    }
    
    updateFormValidity();
});

// Terms checkbox validation
termsCheckbox.addEventListener('change', () => {
    termsError.classList.toggle('visible', !termsCheckbox.checked);
    updateFormValidity();
});

// Form submission
// registerBtn.addEventListener('click', (e) => {
//     //e.preventDefault();
    
//     // Final validation check
//     if (!termsCheckbox.checked) {
//         termsError.classList.add('visible');
//         return;
//     }
    
//     //alert('Registration successful!');
//     // Here you would typically send the form data to your server
// });



// exclusively for login
const authError = document.getElementById('auth-error');
const authErrorMessage = document.getElementById('auth-error-message');

const loginEmailInput = document.getElementById('login-email');
const loginEmailError = document.getElementById('login-email-error');
const loginEmailIcon =  document.getElementById('login-email-icon');

const loginPasswordInput = document.getElementById('login-password');
const loginPasswordError = document.getElementById('login-password-error');
const loginPasswordIcon =  document.getElementById('login-password-icon');


const loginBtn = document.getElementById('login-btn');


function validateLoginPassword(password) {
    return password && password.length > 0;
}


function updateLoginFormValidity() {
    const isLoginEmailValid = validateEmail(loginEmailInput.value);
    const isLoginPasswordValid = validatePassword(loginPasswordInput.value);
    console.log('::',isLoginEmailValid, ";", isLoginPasswordValid);
    loginBtn.disabled = !(isLoginEmailValid && isLoginPasswordValid);
}

loginEmailInput.addEventListener('input', () => {
    const isValid = validateEmail(loginEmailInput.value);
    
    if (loginEmailInput.value) {
        loginEmailInput.classList.toggle('error', !isValid);
        loginEmailInput.classList.toggle('valid', isValid);
        loginEmailError.classList.toggle('visible', !isValid);
        
        loginEmailIcon.classList.toggle('error', !isValid);
        loginEmailIcon.classList.toggle('valid', isValid);
        loginEmailIcon.textContent = isValid ? '✓' : '✗';
    } else {
        loginEmailInput.classList.remove('error', 'valid');
        loginEmailError.classList.remove('visible');
        loginEmailIcon.classList.remove('error', 'valid');
    }
    
    // Hide auth error when user starts typing again
    authError.classList.remove('visible');
    
    updateLoginFormValidity();
});


loginPasswordInput.addEventListener('input', () => {
    const isValid = validateLoginPassword(loginPasswordInput.value);
    
    if (loginPasswordInput.value) {
        loginPasswordInput.classList.toggle('error', !isValid);
        loginPasswordInput.classList.toggle('valid', isValid);
        loginPasswordError.classList.toggle('visible', !isValid);
        
        loginPasswordIcon.classList.toggle('error', !isValid);
        loginPasswordIcon.classList.toggle('valid', isValid);
        loginPasswordIcon.textContent = isValid ? '✓' : '✗';
    } else {
        loginPasswordInput.classList.remove('error', 'valid');
        loginPasswordError.classList.remove('visible');
        loginPasswordIcon.classList.remove('error', 'valid');
    }
    
    // Hide auth error when user starts typing again
    authError.classList.remove('visible');
    
    updateLoginFormValidity();
});

loginBtn.addEventListener('click', (e)=>{
    console.log("email: ", loginEmailInput.value, "pass: ", loginPasswordInput.value);
    if((loginEmailInput.value === 'a@a.com') && (loginPasswordInput.value === 'asdfghjkl')){
        authError.style.display = 'none';
    }
    else{
        authError.style.display = 'block';
    }
});