// Get the modal and the terms link
const modal = document.getElementById('default-modal');
const changepassword = document.getElementById('change-password');
const close = document.getElementById('close-form');
const done = document.getElementById('done');
const decline = document.getElementById('decline');

changepassword.addEventListener('click', function (event) {
    event.preventDefault();
    modal.classList.remove('hidden');
});

// Close modal when clicking the close button or decline
close.addEventListener('click', function () {
    modal.classList.add('hidden');
});

decline.addEventListener('click', function () {
    modal.classList.add('hidden');
    document.getElementById('terms').checked = false;
});

// Handle "Accept" button behavior
done.addEventListener('click', function () {
    modal.classList.add('hidden');
});
