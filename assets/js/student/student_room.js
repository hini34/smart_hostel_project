 function errorText(message) {
        return `<span class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>${message}</span>`;
    }

    function setStatusBadge(text, type = 'info') {
        const statusEl = document.getElementById('room-status-text');
        statusEl.innerHTML = `<span class="status-badge status-${type}">${text}</span>`;
    }

    function setRoomFieldErrors(messages = {}) {
        document.getElementById('room-no').innerHTML =
            errorText(messages.room_no || 'Room number could not be loaded.');

        document.getElementById('floor-no').innerHTML =
            errorText(messages.floor_no || 'Floor details could not be loaded.');

        document.getElementById('block-name').innerHTML =
            errorText(messages.block_name || 'Block details could not be loaded.');

        document.getElementById('occupancy-text').innerHTML =
            errorText(messages.occupancy_text || 'Occupancy details could not be loaded.');

        document.getElementById('capacity').innerHTML =
            errorText(messages.capacity || 'Room capacity could not be loaded.');

        document.getElementById('occupied').innerHTML =
            errorText(messages.occupied || 'Occupied count could not be loaded.');
    }

    function setWardenFieldErrors(messages = {}) {
        document.getElementById('warden-name').innerHTML =
            errorText(messages.name || 'Warden name is not added by admin yet.');

        document.getElementById('warden-mobile').innerHTML =
            errorText(messages.mobile || 'Warden mobile number is not added yet.');

        document.getElementById('warden-email').innerHTML =
            errorText(messages.email || 'Warden email address is not available.');
    }

    function showRoomErrorState(serverMessage = '') {
        setStatusBadge('Room Data Error', 'danger');

        const roomErrorBox = document.getElementById('room-details-error');
        roomErrorBox.classList.remove('d-none');
        roomErrorBox.innerHTML = serverMessage
            ? `<i class="fa-solid fa-triangle-exclamation me-1"></i>${serverMessage}`
            : `<i class="fa-solid fa-triangle-exclamation me-1"></i>Room details could not be loaded because the server response failed. Please try again later.`;

        document.getElementById('mates-empty').innerHTML =
            `<i class="fa-solid fa-circle-exclamation me-1"></i>Roommates list could not be loaded because room data is unavailable right now.`;
        document.getElementById('mates-empty').className = 'error-box';
        document.getElementById('mates-empty').classList.remove('d-none');

        document.getElementById('mates-table-wrapper').classList.add('d-none');
        document.getElementById('mates-body').innerHTML = '';

        document.getElementById('history-list').innerHTML =
            '<li class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>Room allotment history could not be loaded.</li>';

        document.getElementById('warden-error').classList.remove('d-none');
        document.getElementById('warden-error').innerHTML =
            '<i class="fa-solid fa-triangle-exclamation me-1"></i>Warden contact could not be loaded because room-related contact data is missing or server response failed.';

        setRoomFieldErrors();
        setWardenFieldErrors();
    }

    function clearErrorState() {
        document.getElementById('room-details-error').classList.add('d-none');
        document.getElementById('warden-error').classList.add('d-none');
    }

    function loadMyRoom() {
        fetch('student_room_data.php')
            .then(async res => {
                const text = await res.text();

                if (!res.ok) {
                    throw new Error('Server returned HTTP error');
                }

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.log(text);
                    throw new Error('Invalid JSON response');
                }
            })
            .then(data => {
                clearErrorState();

                const r = data.room || {};
                const mates = Array.isArray(data.roommates) ? data.roommates : [];
                const w = data.warden || {};
                const history = Array.isArray(data.history) ? data.history : [];

                if (!data.success) {
                    setStatusBadge('Room Not Assigned', 'warning');

                    document.getElementById('room-details-error').classList.remove('d-none');
                    document.getElementById('room-details-error').innerHTML =
                        `<i class="fa-solid fa-circle-info me-1"></i>${data.message || 'No room is currently assigned to your profile by hostel admin.'}`;

                    document.getElementById('room-no').innerHTML =
                        errorText('Room has not been assigned yet.');

                    document.getElementById('floor-no').innerHTML =
                        errorText('Floor details will appear after room allotment.');

                    document.getElementById('block-name').innerHTML =
                        errorText('Block details will appear after room allotment.');

                    document.getElementById('occupancy-text').innerHTML =
                        errorText('Occupancy type will appear after room allotment.');

                    document.getElementById('capacity').innerHTML =
                        errorText('Capacity details will appear after room allotment.');

                    document.getElementById('occupied').innerHTML =
                        errorText('Occupied count will appear after room allotment.');

                    document.getElementById('mates-empty').innerHTML =
                        '<i class="fa-solid fa-circle-info me-1"></i>No roommates can be shown because no room has been assigned yet.';
                    document.getElementById('mates-empty').className = 'empty-box';
                    document.getElementById('mates-empty').classList.remove('d-none');
                    document.getElementById('mates-table-wrapper').classList.add('d-none');
                    document.getElementById('mates-body').innerHTML = '';

                    document.getElementById('history-list').innerHTML =
                        '<li class="field-error"><i class="fa-solid fa-circle-info me-1"></i>No room allotment history is available yet.</li>';

                    document.getElementById('warden-name').innerHTML =
                        w.name ? w.name : errorText('Warden name is not added by admin yet.');

                    document.getElementById('warden-mobile').innerHTML =
                        w.mobile ? w.mobile : errorText('Warden mobile number is not available.');

                    document.getElementById('warden-email').innerHTML =
                        w.email ? w.email : errorText('Warden email address is not available.');

                    return;
                }

                document.getElementById('room-no').textContent =
                    r.room_no || 'Room number not found';

                document.getElementById('floor-no').textContent =
                    r.floor_no || 'Floor not added';

                document.getElementById('block-name').textContent =
                    r.block_name || 'Block not added';

                document.getElementById('occupancy-text').textContent =
                    r.occupancy_text || r.room_type || 'Occupancy type not available';

                document.getElementById('capacity').textContent =
                    (r.capacity ?? '') !== '' ? r.capacity : 'Capacity not added';

                document.getElementById('occupied').textContent =
                    (r.occupied ?? '') !== '' ? r.occupied : 'Occupied count not available';

                const statusText = r.status_text || 'Room Assigned';
                const lowerStatus = statusText.toLowerCase();

                if (lowerStatus.includes('available') || lowerStatus.includes('sharing')) {
                    setStatusBadge(statusText, 'success');
                } else if (lowerStatus.includes('occupied')) {
                    setStatusBadge(statusText, 'info');
                } else {
                    setStatusBadge(statusText, 'success');
                }

                const historyList = document.getElementById('history-list');
                historyList.innerHTML = '';

                if (history.length === 0) {
                    historyList.innerHTML =
                        '<li class="field-error"><i class="fa-solid fa-circle-info me-1"></i>No room allotment history found for your profile.</li>';
                } else {
                    history.forEach(h => {
                        const li = document.createElement('li');
                        const to = h.to_date ? (' to ' + h.to_date) : ' (Current)';
                        li.textContent = 'Room ' + (h.room_no || '-') + ': ' + (h.from_date || '-') + to;
                        historyList.appendChild(li);
                    });
                }

                const emptyMsg = document.getElementById('mates-empty');
                const tableWrap = document.getElementById('mates-table-wrapper');
                const tbody = document.getElementById('mates-body');

                tbody.innerHTML = '';

                if (mates.length === 0) {
                    emptyMsg.innerHTML =
                        '<i class="fa-solid fa-circle-info me-1"></i>No roommates are assigned in this room right now. You may be the only student in this room.';
                    emptyMsg.className = 'empty-box';
                    emptyMsg.classList.remove('d-none');
                    tableWrap.classList.add('d-none');
                } else {
                    emptyMsg.classList.add('d-none');
                    tableWrap.classList.remove('d-none');

                    mates.forEach(m => {
                        const tr = document.createElement('tr');

                        const tdPhoto = document.createElement('td');
                        const avatar = document.createElement('div');
                        avatar.className = 'avatar-sm';

                        if (m.photo) {
                            const img = document.createElement('img');
                            img.src = '../' + m.photo.replace(/\\/g, '/');
                            img.alt = m.fullname || 'Student';
                            img.onerror = function () {
                                avatar.innerHTML = '';
                                avatar.textContent = (m.fullname || '?').charAt(0).toUpperCase();
                            };
                            avatar.appendChild(img);
                        } else {
                            avatar.textContent = (m.fullname || '?').charAt(0).toUpperCase();
                        }

                        tdPhoto.appendChild(avatar);

                        const tdName = document.createElement('td');
                        tdName.textContent = m.fullname || 'Name not available';

                        const tdEnroll = document.createElement('td');
                        tdEnroll.textContent = m.enrollment || 'Enrollment not available';

                        const tdMobile = document.createElement('td');
                        tdMobile.textContent = m.mobile || 'Mobile not added';

                        tr.appendChild(tdPhoto);
                        tr.appendChild(tdName);
                        tr.appendChild(tdEnroll);
                        tr.appendChild(tdMobile);

                        tbody.appendChild(tr);
                    });
                }

                document.getElementById('warden-name').innerHTML =
                    w.name ? w.name : errorText('Warden name is not added by admin yet.');

                document.getElementById('warden-mobile').innerHTML =
                    w.mobile ? w.mobile : errorText('Warden mobile number is not available.');

                document.getElementById('warden-email').innerHTML =
                    w.email ? w.email : errorText('Warden email address is not available.');
            })
            .catch(err => {
                console.error(err);
                showRoomErrorState('Room details could not be loaded because the system could not connect to the room data service.');
            });
    }

    loadMyRoom();
    setInterval(loadMyRoom, 10000);

