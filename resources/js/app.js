document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.textContent = visible ? 'แสดง' : 'ซ่อน';
    });
});

const quickAddSheet = document.getElementById('quick-add-sheet');

document.querySelectorAll('[data-quick-add-open]').forEach((button) => {
    button.addEventListener('click', () => quickAddSheet?.showModal());
});

document.querySelectorAll('[data-quick-add-close]').forEach((button) => {
    button.addEventListener('click', () => quickAddSheet?.close());
});

quickAddSheet?.addEventListener('click', (event) => {
    if (event.target === quickAddSheet) {
        quickAddSheet.close();
    }
});
