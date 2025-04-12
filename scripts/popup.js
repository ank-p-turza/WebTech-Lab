let popup = document.getElementById('popup');
let popup_title = document.getElementById('popup_title');
let popup_message = document.getElementById('popup_message');

function openPopup(){
    popup_title.innerText = 'Correct these errors!';
    popup_message.innerText = `Welcone Mr. ${arguments[0]} from ${arguments[2]}. An email has been sent to ${arguments[1]}.`;
    popup.classList.add('open-popup');
}

function openPopupWithEorrors(errors){
    popup_title.innerText = 'Correct these errors!';
    popup.classList.add('popup-open');
}

function closePopup(){
    popup.classList.remove('open-popup');
}