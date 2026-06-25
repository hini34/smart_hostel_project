function fillStaffForm(data){
    document.getElementById('form_action').value = 'edit_staff';
    document.querySelector('#staffModal .modal-title').innerText = 'Edit Staff';

    document.getElementById('staff_id').value       = data.id || '';
    document.getElementById('name').value           = data.name || '';
    document.getElementById('mobile').value         = data.mobile || '';
    document.getElementById('alt_mobile').value     = data.alt_mobile || '';
    document.getElementById('email').value          = data.email || '';
    document.getElementById('gender').value         = data.gender || 'Male';
    document.getElementById('dob').value            = data.dob || '';
    document.getElementById('address').value        = data.address || '';
    document.getElementById('role').value           = data.role || '';
    document.getElementById('shift').value          = data.shift || 'Full Day';
    document.getElementById('salary').value         = data.salary || '';
    document.getElementById('joining_date').value   = data.joining_date || '';
    document.getElementById('username').value       = data.username || '';
    document.getElementById('status').value         = data.status || 'Active';
    document.getElementById('password').value       = ''; // edit par blank hi rahega

    var myModal = new bootstrap.Modal(document.getElementById('staffModal'));
    myModal.show();
}

// Page load hone ke baad saare edit buttons par click listener lagao
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-staff').forEach(function(btn){
        btn.addEventListener('click', function () {
            const json = this.getAttribute('data-staff');
            try {
                const data = JSON.parse(json);
                fillStaffForm(data);
            } catch (e) {
                console.error('Invalid staff JSON', e);
            }
        });
    });


    document.getElementById('staffModal').addEventListener('hidden.bs.modal', function () {
        document.querySelector('#staffModal .modal-title').innerText = 'Add Staff';
        document.getElementById('form_action').value = 'add_staff';
        document.querySelector('#staffModal form').reset();
        document.getElementById('staff_id').value = '';
    });
});