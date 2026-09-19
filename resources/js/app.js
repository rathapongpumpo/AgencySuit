document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';

        const eyeIcon = button.querySelector('.toggle-icon-eye');
        const eyeOffIcon = button.querySelector('.toggle-icon-eye-off');

        if (eyeIcon && eyeOffIcon) {
            eyeIcon.classList.toggle('hidden', !visible);
            eyeOffIcon.classList.toggle('hidden', visible);
            button.setAttribute('aria-label', visible ? 'แสดงรหัสผ่าน' : 'ซ่อนรหัสผ่าน');
        } else {
            button.textContent = visible ? 'แสดง' : 'ซ่อน';
        }
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

document.querySelectorAll('[data-share-single-property]').forEach((button) => {
    button.addEventListener('click', async () => {
        const feedback = document.querySelector('[data-share-single-feedback]');
        const parts = [
            button.dataset.name,
            `${button.dataset.type} · ${button.dataset.price}`,
            button.dataset.size ? `${button.dataset.bedrooms} · ${button.dataset.size}` : button.dataset.bedrooms,
            `ทำเล: ${button.dataset.location}`,
        ];
        if (button.dataset.notes) {
            parts.push(`หมายเหตุ: ${button.dataset.notes}`);
        }
        const text = parts.join('\n');

        const copyText = async () => {
            if (window.navigator.clipboard?.writeText) {
                await window.navigator.clipboard.writeText(text);
                if (feedback) feedback.textContent = 'คัดลอกข้อมูลทรัพย์แล้ว นำไปส่งต่อได้เลย ✓';
                return;
            }
            if (feedback) feedback.textContent = text;
        };

        try {
            if (typeof window.navigator.share === 'function') {
                await Promise.race([
                    window.navigator.share({ title: button.dataset.name, text }),
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

// Helper to bind follow-up row actions (complete and delete)
function bindFollowUpRow(row) {
    const completeForm = row.querySelector('form[data-followup-complete], form[action*="/complete"]');
    if (completeForm && !completeForm._bound) {
        completeForm._bound = true;
        completeForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = completeForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await fetch(completeForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(completeForm),
                });

                if (res.ok) {
                    showToast('ทำรายการติดตามแล้ว ✓');
                    row.classList.add('as-row-leaving');
                    setTimeout(() => {
                        row.remove();
                        updateFollowUpCounters();
                    }, 280);
                } else {
                    completeForm.submit();
                }
            } catch {
                completeForm.submit();
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    }

    const destroyForm = row.querySelector('form[data-followup-destroy], form[action*="/follow-ups/"]:not([action*="/complete"])');
    if (destroyForm && !destroyForm._bound) {
        destroyForm._bound = true;
        destroyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = destroyForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await fetch(destroyForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(destroyForm),
                });

                if (res.ok) {
                    showToast('ลบรายการติดตามแล้ว ✓');
                    row.classList.add('as-row-leaving');
                    setTimeout(() => {
                        row.remove();
                        updateFollowUpCounters();
                    }, 280);
                } else {
                    destroyForm.submit();
                }
            } catch {
                destroyForm.submit();
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    }
}

function updateFollowUpCounters() {
    const pendingList = document.getElementById('pending-followup-list');
    if (pendingList) {
        const count = pendingList.querySelectorAll('li:not(.as-row-leaving)').length;
        const countEl = document.getElementById('followup-pending-count');
        if (countEl) countEl.textContent = count.toString();
        const emptyEl = document.getElementById('no-pending-followup');
        if (emptyEl) emptyEl.classList.toggle('hidden', count > 0);
    }

    const completedList = document.getElementById('completed-followup-list');
    if (completedList) {
        const count = completedList.querySelectorAll('li:not(.as-row-leaving)').length;
        const countEl = document.getElementById('followup-completed-count');
        if (countEl) countEl.textContent = count.toString();
        if (count === 0) {
            const details = document.getElementById('completed-followups-details');
            if (details) details.remove();
        }
    }

    // Today page section counters
    document.querySelectorAll('.as-work-section').forEach((section) => {
        const countEl = section.querySelector('.as-count');
        if (countEl) {
            const rows = section.querySelectorAll('li:not(.as-row-leaving)').length;
            countEl.textContent = rows.toString();
            if (rows === 0) {
                section.remove();
            }
        }
    });
}

// Bind all existing follow-up rows
document.querySelectorAll('#pending-followup-list li, #completed-followup-list li, .as-work-row').forEach(bindFollowUpRow);

// Clear All Follow-ups In-Place (No page reload)
const destroyAllForm = document.getElementById('destroy-all-followups-form');
if (destroyAllForm) {
    destroyAllForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = destroyAllForm.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        try {
            const res = await fetch(destroyAllForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(destroyAllForm),
            });

            if (res.ok) {
                showToast('ล้างประวัติติดตามทั้งหมดแล้ว ✓');
                const pendingList = document.getElementById('pending-followup-list');
                if (pendingList) pendingList.innerHTML = '';
                const completedDetails = document.getElementById('completed-followups-details');
                if (completedDetails) completedDetails.remove();
                const countEl = document.getElementById('followup-pending-count');
                if (countEl) countEl.textContent = '0';
                const emptyEl = document.getElementById('no-pending-followup');
                if (emptyEl) emptyEl.classList.remove('hidden');
                destroyAllForm.style.display = 'none';
            } else {
                destroyAllForm.submit();
            }
        } catch {
            destroyAllForm.submit();
        } finally {
            if (btn) btn.disabled = false;
        }
    });
}

// Add Follow-up In-Place (No page reload, instant UI update)
const addFollowupForm = document.getElementById('add-followup-form');
if (addFollowupForm) {
    addFollowupForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitter = e.submitter;
        const formData = new FormData(addFollowupForm);
        if (submitter && submitter.name) {
            formData.set(submitter.name, submitter.value);
        }

        const buttons = addFollowupForm.querySelectorAll('button[type="submit"]');
        buttons.forEach((b) => { b.disabled = true; });

        try {
            const res = await fetch(addFollowupForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (res.ok) {
                const data = await res.json();
                showToast(data.message || 'ตั้งเวลาติดตามแล้ว ✓');

                const pendingList = document.getElementById('pending-followup-list');
                if (pendingList && data.follow_up) {
                    const existingRow = document.getElementById(`followup-row-${data.follow_up.id}`);
                    if (existingRow) {
                        // Update existing row
                        const dateSpan = existingRow.querySelector('.followup-date');
                        if (dateSpan) dateSpan.textContent = data.follow_up.due_date;
                        let noteSpan = existingRow.querySelector('.followup-note');
                        if (data.follow_up.note) {
                            if (noteSpan) {
                                noteSpan.textContent = data.follow_up.note;
                            } else {
                                const noteEl = document.createElement('span');
                                noteEl.className = 'block truncate text-xs text-stone-500 followup-note';
                                noteEl.textContent = data.follow_up.note;
                                existingRow.querySelector('.min-w-0')?.appendChild(noteEl);
                            }
                        } else if (noteSpan) {
                            noteSpan.remove();
                        }
                    } else {
                        // Create new row
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between py-2 text-sm';
                        li.id = `followup-row-${data.follow_up.id}`;
                        li.dataset.followupId = data.follow_up.id;
                        li.dataset.dueDate = data.follow_up.due_date_raw;

                        const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
                        const noteHtml = data.follow_up.note ? `<span class="block truncate text-xs text-stone-500 followup-note">${escapeHtml(data.follow_up.note)}</span>` : '';

                        li.innerHTML = `
                            <div class="min-w-0 pr-2">
                                <span class="font-semibold text-stone-800 followup-date">${data.follow_up.due_date}</span>
                                ${noteHtml}
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <form method="POST" action="/follow-ups/${data.follow_up.id}/complete" data-followup-complete>
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="PATCH">
                                    <button class="as-inline-action px-2 py-1 text-xs" type="submit">ทำแล้ว</button>
                                </form>
                                <form method="POST" action="/follow-ups/${data.follow_up.id}" data-followup-destroy>
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="text-xs text-red-500 hover:text-red-700 p-1" type="submit" title="ลบรายการนี้">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        `;
                        pendingList.prepend(li);
                        bindFollowUpRow(li);
                    }

                    // Reset form inputs
                    const dateInput = addFollowupForm.querySelector('input[name="due_date"]');
                    if (dateInput) dateInput.value = '';
                    const noteInput = addFollowupForm.querySelector('input[name="note"]');
                    if (noteInput) noteInput.value = '';

                    const emptyMsg = document.getElementById('no-pending-followup');
                    if (emptyMsg) emptyMsg.classList.add('hidden');

                    const countEl = document.getElementById('followup-pending-count');
                    if (countEl) {
                        const total = pendingList.querySelectorAll('li:not(.as-row-leaving)').length;
                        countEl.textContent = total.toString();
                    }

                    if (destroyAllForm) destroyAllForm.style.display = '';
                }
            } else {
                addFollowupForm.submit();
            }
        } catch {
            addFollowupForm.submit();
        } finally {
            buttons.forEach((b) => { b.disabled = false; });
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

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

// In-place Photo Delete
document.querySelectorAll('form[data-photo-destroy]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const dialog = form.closest('dialog');
        if (dialog) dialog.close();

        const figure = form.closest('figure');
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
                showToast('ลบรูปแล้ว ✓');
                if (figure) {
                    figure.classList.add('as-row-leaving');
                    setTimeout(() => figure.remove(), 280);
                }
            } else {
                form.submit();
            }
        } catch {
            form.submit();
        }
    });
});

// In-place Feedback Delete in Admin
document.querySelectorAll('form[data-feedback-destroy]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
        const confirmMsg = form.dataset.confirm || 'ต้องการลบข้อเสนอแนะนี้ใช่หรือไม่?';
        if (!window.confirm(confirmMsg)) {
            e.preventDefault();
            return;
        }

        e.preventDefault();

        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'กำลังลบ...';
        }

        const card = form.closest('[data-feedback-card]') || form.closest('.as-card');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (res.ok || res.status === 404) {
                showToast('ลบข้อเสนอแนะแล้ว ✓');
                if (card) {
                    card.classList.add('as-row-leaving');
                    setTimeout(() => {
                        card.remove();
                        const list = document.querySelector('[data-feedback-list]');
                        if (list && list.querySelectorAll('[data-feedback-card]').length === 0) {
                            window.location.reload();
                        }
                    }, 280);
                } else {
                    window.location.reload();
                }
            } else {
                showToast('ไม่สามารถลบข้อเสนอแนะได้');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'ลบรายการ';
                }
            }
        } catch {
            showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'ลบรายการ';
            }
        }
    });
});

// Appearance Mode Switcher (System / Light / Dark)
const initThemeSwitcher = () => {
    const switcher = document.querySelector('[data-theme-switcher]');
    if (!switcher) return;

    const buttons = switcher.querySelectorAll('[data-theme-value]');

    const updateActiveButton = (mode) => {
        buttons.forEach((btn) => {
            const isActive = btn.dataset.themeValue === mode;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const applyTheme = (mode) => {
        localStorage.setItem('as_theme', mode);
        document.documentElement.setAttribute('data-theme', mode);

        const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', isDark);

        const metaTheme = document.querySelector('meta[name="theme-color"]');
        if (metaTheme) {
            metaTheme.setAttribute('content', isDark ? '#171427' : '#f6f5fb');
        }

        updateActiveButton(mode);
    };

    const currentMode = localStorage.getItem('as_theme') || 'system';
    updateActiveButton(currentMode);

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            applyTheme(btn.dataset.themeValue);
        });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const mode = localStorage.getItem('as_theme') || 'system';
        if (mode === 'system') {
            document.documentElement.classList.toggle('dark', e.matches);
            const metaTheme = document.querySelector('meta[name="theme-color"]');
            if (metaTheme) {
                metaTheme.setAttribute('content', e.matches ? '#171427' : '#f6f5fb');
            }
        }
    });
};

initThemeSwitcher();

