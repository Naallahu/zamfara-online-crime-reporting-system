function validateUserForm(type) {
    const form = type === 'add' ? 'addUserForm' : 'editUserForm';
    const formData = new FormData(document.getElementById(form));
    
    // Validate name
    const name = formData.get('name');
    if (name.length < 3) {
        showAlert('Name must be at least 3 characters long', 'error');
        return false;
    }
    
    // Validate email
    const email = formData.get('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Please enter a valid email address', 'error');
        return false;
    }
    
    // Validate password for new users
    if (type === 'add') {
        const password = formData.get('password');
        if (password.length < 6) {
            showAlert('Password must be at least 6 characters long', 'error');
            return false;
        }
    }
    
    // Submit form if validation passes
    submitUserForm(type, formData);
    return false;
}

function submitUserForm(type, formData) {
    formData.append('action', type === 'add' ? 'add' : 'update');
    
    fetch('process_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            document.querySelector('.modal.show').querySelector('.btn-close').click();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    });
}
