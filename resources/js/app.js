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

document.querySelectorAll('[data-photo-input]').forEach((input) => {
    const preview = input.closest('[data-photo-upload]')?.querySelector('[data-photo-preview]');

    input.addEventListener('change', () => {
        if (!preview) {
            return;
        }

        preview.replaceChildren();
        Array.from(input.files ?? []).forEach((file) => {
            if (!file.type.startsWith('image/')) {
                return;
            }

            const image = document.createElement('img');
            image.className = 'aspect-square w-full rounded-lg border border-stone-200 object-cover';
            image.alt = `ตัวอย่าง ${file.name}`;
            image.file = file;
            preview.append(image);

            const reader = new FileReader();
            reader.addEventListener('load', () => { image.src = reader.result; });
            reader.readAsDataURL(file);
        });
    });
});
