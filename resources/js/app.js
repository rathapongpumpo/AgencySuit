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

document.querySelectorAll('[data-photo-delete-open]').forEach((button) => {
    button.addEventListener('click', () => document.getElementById(button.dataset.photoDeleteOpen)?.showModal());
});

document.querySelectorAll('[data-photo-delete-close]').forEach((button) => {
    button.addEventListener('click', () => button.closest('dialog')?.close());
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

/* ==========================================================================
   Smooth In-Place Actions & Scroll Preservation (No Jitter, No Full Reload)
   ========================================================================== */

function showToast(message) {
    let toast = document.getElementById('as-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'as-toast';
        toast.className = 'as-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('as-toast--visible');
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.classList.remove('as-toast--visible');
    }, 2400);
}

// In-place Follow-up Complete (Today page & Client show page)
document.querySelectorAll('form[action*="/follow-ups/"]').forEach((form) => {
    if (!form.action.includes('/complete')) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const row = form.closest('.as-work-row') || form.closest('li');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (res.ok) {
                showToast('ทำรายการติดตามแล้ว ✓');
                if (row) {
                    const section = row.closest('.as-work-section') || row.closest('section');
                    if (section) {
                        const countEl = section.querySelector('.as-count');
                        if (countEl) {
                            const current = parseInt(countEl.textContent, 10);
                            if (!isNaN(current) && current > 0) {
                                countEl.textContent = (current - 1).toString();
                            }
                        }
                    }

                    row.classList.add('as-row-leaving');
                    setTimeout(() => {
                        row.remove();
                    }, 300);
                }
            } else {
                if (submitBtn) submitBtn.disabled = false;
                form.submit();
            }
        } catch {
            if (submitBtn) submitBtn.disabled = false;
            form.submit();
        }
    });
});

// In-place Property Status Change
document.querySelectorAll('form[action*="/properties/"][action*="/status"]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (res.ok) {
                const data = await res.json();
                showToast(data.message || 'เปลี่ยนสถานะแล้ว ✓');

                const statusDd = document.querySelector('dt:has(+ dd) + dd.as-text-link') ||
                                 document.querySelector('dd.as-text-link');
                if (statusDd && data.status_label) {
                    statusDd.textContent = data.status_label;
                }

                const subtitle = document.querySelector('.as-detail-subtitle');
                if (subtitle && data.status_label) {
                    const parts = subtitle.textContent.split('·');
                    if (parts.length > 1) {
                        subtitle.textContent = `${parts[0].trim()} · ${data.status_label}`;
                    }
                }

                const details = form.closest('details');
                if (details) details.open = false;
            } else {
                form.submit();
            }
        } catch {
            form.submit();
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});

// In-place Appointment Cancel
document.querySelectorAll('form[action*="/appointments/"][action*="/cancel"]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (res.ok) {
                showToast('ยกเลิกนัดดูแล้ว ✓');
                const actionsContainer = form.closest('.as-form-actions');
                if (actionsContainer) {
                    const alertEl = document.createElement('p');
                    alertEl.className = 'as-alert mt-6';
                    alertEl.textContent = 'นัดนี้ถูกยกเลิกแล้ว';
                    actionsContainer.replaceWith(alertEl);
                }
            } else {
                form.submit();
            }
        } catch {
            form.submit();
        }
    });
});

// In-place Photo Actions (Primary & Delete)
document.querySelectorAll('form[action*="/photos/"][action*="/primary"]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (res.ok) {
                showToast('เปลี่ยนภาพหลักแล้ว ✓');
                const photoGrid = form.closest('[data-photo-grid]');
                if (photoGrid) {
                    photoGrid.querySelectorAll('.as-text-link.text-\\[11px\\]').forEach((badge) => {
                        badge.replaceWith(badge); // keep or re-render
                    });
                }
                // Reload photo grid section seamlessly if needed or let user see badge
                window.location.reload();
            } else {
                form.submit();
            }
        } catch {
            form.submit();
        }
    });
});

// Scroll Position Preservation for any traditional form post/redirect
const savedScroll = sessionStorage.getItem('as_scroll_y');
if (savedScroll !== null) {
    sessionStorage.removeItem('as_scroll_y');
    const scrollTarget = parseInt(savedScroll, 10);
    if (!isNaN(scrollTarget) && scrollTarget > 0) {
        window.scrollTo({ top: scrollTarget, behavior: 'instant' });
    }
}

window.addEventListener('beforeunload', () => {
    if (window.scrollY > 0) {
        sessionStorage.setItem('as_scroll_y', window.scrollY.toString());
    }
});
