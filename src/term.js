// Get the modal and the terms link
const modal = document.getElementById('default-modal');
const termsLink = document.getElementById('terms-link');
const closeModal = document.getElementById('close-modal');
const acceptTerms = document.getElementById('accept-terms');
const declineTerms = document.getElementById('decline-terms');

// Open modal when the Terms and Conditions link is clicked
termsLink.addEventListener('click', function (event) {
    event.preventDefault();
    modal.classList.remove('hidden');
});

// Close modal when clicking the close button or decline
closeModal.addEventListener('click', function () {
    modal.classList.add('hidden');
});

declineTerms.addEventListener('click', function () {
    modal.classList.add('hidden');
    document.getElementById('terms').checked = false;
});

// Handle "Accept" button behavior
acceptTerms.addEventListener('click', function () {
    modal.classList.add('hidden');
    // Check the "I accept" checkbox after clicking accept
    document.getElementById('terms').checked = true;
});

// Optional: Close modal when clicking outside the modal content
window.addEventListener('click', function (event) {
    if (event.target === modal) {
        modal.classList.add('hidden');
    }
});