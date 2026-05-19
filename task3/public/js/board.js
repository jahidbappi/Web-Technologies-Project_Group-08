(function () {
    const board = document.querySelector('.board');
    if (!board) return;

    const apiUrl = board.dataset.apiUrl;
    const columns = {
        'todo':        board.querySelector('[data-status="todo"] [data-column-body]'),
        'in-progress': board.querySelector('[data-status="in-progress"] [data-column-body]'),
        'done':        board.querySelector('[data-status="done"] [data-column-body]'),
    };

    function applyOverdueHighlight(card) {
        card.classList.remove('border-red-500');
        const status = card.dataset.status;
        const due    = card.dataset.dueDate;
        if (!due || status === 'done') return;

        const today    = new Date();
        today.setHours(0, 0, 0, 0);
        const dueDate  = new Date(due + 'T00:00:00');
        if (isNaN(dueDate.getTime())) return;
        if (dueDate < today) {
            card.classList.add('border-red-500');
        }
    }
    document.querySelectorAll('.card').forEach(applyOverdueHighlight);

    const TRANSITIONS = {
        'todo':        { forward: 'in-progress', back: null },
        'in-progress': { forward: 'done',        back: 'todo' },
        'done':        { forward: null,          back: 'in-progress' },
    };

    function nextStatus(card, direction) {
        const current = card.dataset.status;
        return TRANSITIONS[current] ? TRANSITIONS[current][direction] : null;
    }

    function updateCardControls(card) {
        const status = card.dataset.status;
        const t = TRANSITIONS[status] || { forward: null, back: null };
        const actions = card.querySelector('.card__actions');
        if (!actions) return;
        actions.innerHTML = '';

        const backEl = document.createElement(t.back ? 'button' : 'span');
        backEl.className = 'icon-btn' + (t.back ? '' : ' icon-btn--disabled');
        if (t.back) {
            backEl.type = 'button';
            backEl.dataset.move = 'back';
            backEl.title = 'Move backward';
        }
        backEl.innerHTML = '&larr;';
        actions.appendChild(backEl);

        const fwdEl = document.createElement(t.forward ? 'button' : 'span');
        fwdEl.className = 'icon-btn' + (t.forward ? '' : ' icon-btn--disabled');
        if (t.forward) {
            fwdEl.type = 'button';
            fwdEl.dataset.move = 'forward';
            fwdEl.title = 'Move forward';
        }
        fwdEl.innerHTML = '&rarr;';
        actions.appendChild(fwdEl);
    }

    function refreshColumnEmptyState(body) {
        if (!body) return;
        const hasCards   = body.querySelector('.card');
        const emptyNode  = body.querySelector('[data-empty]');
        if (!hasCards && !emptyNode) {
            const p = document.createElement('p');
            p.className = 'board-col__empty';
            p.dataset.empty = '';
            p.textContent = 'No tasks here yet.';
            body.appendChild(p);
        } else if (hasCards && emptyNode) {
            emptyNode.remove();
        }
    }

    function refreshColumnCount(body) {
        const col   = body.closest('.board-col');
        const count = col.querySelector('[data-count]');
        if (count) count.textContent = body.querySelectorAll('.card').length;
    }

    async function moveTask(card, direction) {
        const id     = card.dataset.taskId;
        const target = nextStatus(card, direction);
        if (!target) return;

        const buttons = card.querySelectorAll('.icon-btn');
        buttons.forEach(b => b.setAttribute('disabled', 'disabled'));

        let resp;
        try {
            resp = await fetch(`${apiUrl}&task_id=${encodeURIComponent(id)}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ status: target }),
            });
        } catch (err) {
            buttons.forEach(b => b.removeAttribute('disabled'));
            alert('Network error: ' + err.message);
            return;
        }

        let data = {};
        try { data = await resp.json(); } catch (e) {}

        if (!resp.ok || !data.ok) {
            buttons.forEach(b => b.removeAttribute('disabled'));
            alert(data.error || `Failed to move task (HTTP ${resp.status})`);
            return;
        }

        const oldBody = card.parentElement;
        const newBody = columns[data.new_status];
        if (!newBody) {
            location.reload();
            return;
        }

        card.dataset.status = data.new_status;
        newBody.appendChild(card);
        applyOverdueHighlight(card);
        updateCardControls(card);

        refreshColumnEmptyState(oldBody);
        refreshColumnEmptyState(newBody);
        refreshColumnCount(oldBody);
        refreshColumnCount(newBody);
    }

    board.addEventListener('click', (ev) => {
        const btn = ev.target.closest('[data-move]');
        if (!btn) return;
        const card = btn.closest('.card');
        if (!card) return;
        moveTask(card, btn.dataset.move);
    });

    const modal   = document.getElementById('new-task-modal');
    const openBtn = document.getElementById('open-new-task');

    function openModal() {
        modal.setAttribute('data-open', '1');
        modal.setAttribute('aria-hidden', 'false');
        setTimeout(() => {
            const titleInput = modal.querySelector('input[name="title"]');
            if (titleInput) titleInput.focus();
        }, 30);
    }
    function closeModal() {
        modal.removeAttribute('data-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (modal) {
        const panel = modal.querySelector('.modal__panel');
        const backdrop = modal.querySelector('.modal__backdrop');
        const form = modal.querySelector('form');

        if (panel) {
            panel.addEventListener('click', (ev) => ev.stopPropagation());
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeModal);
        }
        modal.querySelectorAll('[data-close-modal]').forEach((el) => {
            if (el === backdrop) return;
            el.addEventListener('click', closeModal);
        });

        if (form) {
            form.addEventListener('submit', () => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Creating…';
                }
            });
        }

        document.addEventListener('keydown', (ev) => {
            if (ev.key === 'Escape' && modal.hasAttribute('data-open')) closeModal();
        });
        if (modal.hasAttribute('data-open')) {
            openModal();
        }
    }
})();
