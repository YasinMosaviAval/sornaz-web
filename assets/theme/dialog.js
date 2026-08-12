(function () {
    const queue = [];
    let active = false;

    const text = (fa, en) => document.documentElement.lang === 'en' ? en : fa;
    const icon = type => ({ success:'fa-check', error:'fa-xmark', warning:'fa-triangle-exclamation', info:'fa-info' }[type] || 'fa-info');
    const detectType = message => /^\s*[✅✔]/u.test(message) ? 'success' : /^\s*[❌✖]/u.test(message) ? 'error' : 'info';
    const clean = message => String(message ?? '').replace(/^\s*[✅✔❌✖]\s*/u, '');

    function ensureHost() {
        let host = document.getElementById('appDialogHost');
        if (!host) {
            host = document.createElement('div');
            host.id = 'appDialogHost';
            document.body.appendChild(host);
        }
        return host;
    }

    function next() {
        if (active || !queue.length) return;
        active = true;
        const item = queue.shift();
        const host = ensureHost();
        const isPrompt = item.kind === 'prompt';
        const isConfirm = item.kind === 'confirm';
        host.innerHTML = `<div class="app-dialog-backdrop" role="presentation">
            <section class="app-dialog app-dialog--${item.type}" role="dialog" aria-modal="true" aria-labelledby="appDialogTitle" aria-describedby="appDialogMessage">
                <button type="button" class="app-dialog__close" data-dialog-result="cancel" aria-label="${text('بستن','Close')}">×</button>
                <div class="app-dialog__icon"><i class="fas ${icon(item.type)}"></i></div>
                <h2 id="appDialogTitle" class="app-dialog__title">${escapeHtml(item.title)}</h2>
                <p id="appDialogMessage" class="app-dialog__message">${escapeHtml(clean(item.message))}</p>
                ${isPrompt ? `<input class="app-dialog__input" data-dialog-input type="text" value="${escapeHtml(item.defaultValue)}" autocomplete="off">` : ''}
                <div class="app-dialog__actions">
                    ${isConfirm || isPrompt ? `<button type="button" class="app-dialog__button app-dialog__button--cancel" data-dialog-result="cancel">${escapeHtml(item.cancelText)}</button>` : ''}
                    <button type="button" class="app-dialog__button app-dialog__button--confirm" data-dialog-result="confirm">${escapeHtml(item.confirmText)}</button>
                </div>
            </section>
        </div>`;
        requestAnimationFrame(() => host.firstElementChild?.classList.add('is-visible'));
        document.body.classList.add('app-dialog-open');
        const input = host.querySelector('[data-dialog-input]');
        (input || host.querySelector('[data-dialog-result="confirm"]'))?.focus();
        input?.select();

        const finish = result => {
            const backdrop = host.firstElementChild;
            backdrop?.classList.remove('is-visible');
            window.setTimeout(() => {
                host.innerHTML = '';
                document.body.classList.remove('app-dialog-open');
                active = false;
                item.resolve(result);
                next();
            }, 140);
        };
        host.onclick = event => {
            const action = event.target.closest('[data-dialog-result]')?.dataset.dialogResult;
            if (action === 'confirm') finish(isPrompt ? input.value : true);
            if (action === 'cancel' || event.target.classList.contains('app-dialog-backdrop')) finish(isPrompt ? null : false);
        };
        host.onkeydown = event => {
            if (event.key === 'Escape') finish(isPrompt ? null : false);
            if (event.key === 'Enter' && (isPrompt || document.activeElement?.dataset.dialogResult === 'confirm')) finish(isPrompt ? input.value : true);
        };
    }

    function escapeHtml(value) {
        const node = document.createElement('div');
        node.textContent = String(value ?? '');
        return node.innerHTML;
    }

    function open(options) {
        return new Promise(resolve => {
            queue.push({
                kind: options.kind || 'alert', type: options.type || 'info',
                title: options.title || text('پیام سرناز','Sornaz message'), message: options.message || '',
                defaultValue: options.defaultValue || '', confirmText: options.confirmText || text('تأیید','Confirm'),
                cancelText: options.cancelText || text('انصراف','Cancel'), resolve
            });
            next();
        });
    }

    window.AppDialog = {
        open,
        alert: (message, options = {}) => open({ ...options, kind:'alert', message, type:options.type || detectType(message) }),
        confirm: (message, options = {}) => open({ ...options, kind:'confirm', message, type:options.type || 'warning' }),
        prompt: (message, defaultValue = '', options = {}) => open({ ...options, kind:'prompt', message, defaultValue }),
        confirmDelete: (collection, id, label = text('مورد','item'), options = {}) => {
            const row = Array.isArray(collection) ? collection.find(item => String(item?.id) === String(id)) : collection;
            const title = row && (row.title || row.name || row.full_name || row.fullName || row.subject || row.label || row.member_name || row.user_name || row.instrument_title || row.lesson_title || row.course_title || row.classroom_name || row.branch_name || row.role_name || row.permission_name || row.rule_title || row.day || row.date);
            const display = String(title || `${label} #${id}`).replace(/["“”]/g, '');
            return open({ ...options, kind:'confirm', type:'warning', message:options.message || text(`آیا از حذف "${display}" مطمئن هستید؟`, `Are you sure you want to delete "${display}"?`) });
        },
        confirmSubmit: (event, message, options = {}) => {
            const form = event.currentTarget;
            if (form.dataset.dialogConfirmed === '1') { delete form.dataset.dialogConfirmed; return true; }
            event.preventDefault();
            open({ ...options, kind:'confirm', type:'warning', message }).then(confirmed => {
                if (!confirmed) return;
                form.dataset.dialogConfirmed = '1';
                form.requestSubmit(event.submitter || undefined);
            });
            return false;
        }
    };
    window.alert = message => window.AppDialog.alert(message);
})();
