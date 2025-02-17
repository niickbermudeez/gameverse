const params = new URLSearchParams(window.location.search);
const success = params.get('success');
const error = params.get('error');

const title = document.getElementById('status-title');
const message = document.getElementById('status-message');

if (success) {
    title.textContent = 'Account Activated!';
    message.textContent = success;
    title.classList.add('success');
} else if (error) {
    title.textContent = 'Activation Failed!';
    message.textContent = error;
    title.classList.add('error');
} else {
    title.textContent = 'Invalid Request';
    message.textContent = 'No activation parameters were provided.';
    title.classList.add('error');
}
