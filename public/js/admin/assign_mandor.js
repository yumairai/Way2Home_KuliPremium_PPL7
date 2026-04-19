let selectedMandorId = null;
let selectedProjectId = null;

function openDocModal(mandorId) {
    selectedMandorId = mandorId;
    if (window.projectsPending && window.projectsPending.length > 0) {
        selectedProjectId = window.projectsPending[0].id;
    }
    document.getElementById('list-proyek-modal').style.display = 'flex';
    // Reset ke item pertama setiap kali modal dibuka
    document.querySelectorAll('.proyek-item').forEach(i => i.classList.remove('active'));
    const firstItem = document.querySelector('.proyek-item');
    if (firstItem) {
        firstItem.classList.add('active');
    }
}

function closeDocModal() {
    document.getElementById('list-proyek-modal').style.display = 'none';
    selectedMandorId = null;
    selectedProjectId = null;
}

function selectProject(element) {
    document.querySelectorAll('.proyek-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    selectedProjectId = element.getAttribute('data-project-id') ||
        element.querySelector('.project-id').textContent.split('ID: ')[1];
}

function assignMandor() {
    if (selectedMandorId && selectedProjectId) {
        alert(`Assign Mandor ${selectedMandorId} untuk Proyek ${selectedProjectId}`);
        alert(`Mandor ${selectedMandorId} berhasil ditugaskan ke Proyek ${selectedProjectId}`);
        closeDocModal();
    };
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.proyek-item').forEach(item => {
        item.addEventListener('click', function () {
            selectProject(this);
        });
    });

    // Tombol Submit
    const submitBtn = document.querySelector('.modal-btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', assignMandor);
    }
});