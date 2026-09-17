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

document.querySelectorAll('[data-share-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const selected = Array.from(form.querySelectorAll('[data-share-property]:checked'));
        const feedback = form.querySelector('[data-share-feedback]');

        if (selected.length === 0) {
            if (feedback) feedback.textContent = 'เลือกทรัพย์อย่างน้อย 1 รายการ';
            return;
        }

        const text = selected.map((item) => [
            item.dataset.shareName,
            `${item.dataset.shareType} · ${item.dataset.sharePrice}`,
            `${item.dataset.shareBedrooms} ห้องนอน · ${item.dataset.shareLocation}`,
        ].join('\n')).join('\n\n');

        const copyText = async () => {
            if (window.navigator.clipboard?.writeText) {
                await window.navigator.clipboard.writeText(text);
                if (feedback) feedback.textContent = 'คัดลอกข้อความแล้ว นำไปส่งให้ลูกค้าได้เลย';
                return;
            }

            if (feedback) feedback.textContent = text;
        };

        try {
            if (typeof window.navigator.share === 'function') {
                await Promise.race([
                    window.navigator.share({ title: 'ทรัพย์ที่น่าสนใจ', text }),
                    new Promise((_, reject) => window.setTimeout(() => reject(new Error('share-timeout')), 1500)),
                ]);
                if (feedback) feedback.textContent = 'เปิดหน้าต่างแชร์แล้ว';
                return;
            }
            await copyText();
        } catch (error) {
            if (error?.name !== 'AbortError') await copyText();
        }
    });
});
