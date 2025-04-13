const form = document.getElementById('registrationForm');
const fullNameInput = document.getElementById('full_name');
const emailInput = document.getElementById('email_field');
const passwordInput = document.getElementById('pass_field');
const confirmPasswordInput = document.getElementById('cpass_field');
const cityInput = document.getElementById('location_city');
const zipInput = document.getElementById('zip');
const preferredCitySelect = document.getElementById('p_city');
const termsCheckbox = document.getElementById('terms_checked');
//const errorMsgElement = document.getElementById('error_msg');

//import { openPopup } from "./popup";


if (form) {
    form.addEventListener('submit', function(event) {
       
        //errorMsgElement.innerHTML = '';
        let errors = []; 
        
        
        if (fullNameInput.value.trim() === '') {
            errors.push('Full Name is required.');
        }

        
        const emailValue = emailInput.value.trim();
        if (emailValue === '') {
            errors.push('Email Address is required.');
        } else {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailValue)) {
                errors.push('Please enter a valid Email Address.');
            }
        }

        
        const passwordValue = passwordInput.value;
        if (passwordValue === '') {
            errors.push('Password is required.');
        } else if (passwordValue.length < 8) {
            errors.push('Password must be at least 8 characters long.');
        }

        
        const confirmPasswordValue = confirmPasswordInput.value;
        if (confirmPasswordValue === '') {
            errors.push('Please retype your password.');
        } else if (passwordValue !== confirmPasswordValue) {
            errors.push('Passwords do not match.');
        }

        
        if (cityInput.value.trim() === '') {
            errors.push('Your city is required.');
        }

        
        const zipValue = zipInput.value.trim();
        if (zipValue === '') {
            errors.push('Zip Code is required.');
        } else if (isNaN(zipValue)) {
            errors.push('Zip Code must be numeric.');
        }

        
        if (!termsCheckbox.checked) {
            errors.push('You must accept the terms of service.');
        }

        
        if (errors.length > 0) {
            let error_message;
            errors.forEach(error=>{
                console.log(error);
            });
            alert(errors[0]);
            event.preventDefault(); 
            //errorMsgElement.innerHTML = errors.join('<br>');
        } else {
            
            //openPopup(fullNameInput.value.trim(), emailValue, cityInput.value.trim());
            console.log('Form submitted successfully! (from external JS)');

        }
    });
} else {
    console.error("Registration form not found!"); // Log an error if the form doesn't exist
}