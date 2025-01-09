function editUser(userId) {
    fetch(`get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editName').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editStatus').value = user.status;
            modal.show();
        });
}

function updateUser(formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    fetch('process_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showAlert('User updated successfully', 'success');
            location.reload();
        } else {
            showAlert(data.message, 'error');
        }
    });
}

function deleteUser(userId) {
    if(confirm('Are you sure you want to delete this user?')) {
        fetch('process_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showAlert('User deleted successfully', 'success');
                location.reload();
            } else {
                showAlert(data.message, 'error');
            }
        });
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.container-fluid').insertAdjacentElement('afterbegin', alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
