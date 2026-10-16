document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const sidebar = document.querySelector('#sidebar');
    const menuClose = document.querySelector('[data-menu-close]');

    const closeMenu = () => {
        sidebar?.classList.remove('is-open');
        menuToggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
    };

    menuToggle?.addEventListener('click', () => {
        const isOpen = sidebar?.classList.toggle('is-open') ?? false;
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        document.body.classList.toggle('menu-open', isOpen);
    });
    menuClose?.addEventListener('click', closeMenu);

    document.querySelectorAll('[data-dialog-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.dialogOpen);
            if (dialog instanceof HTMLDialogElement) dialog.showModal();
        });
    });

    document.querySelectorAll('[data-dialog-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
    });

    const query = new URLSearchParams(window.location.search);
    if (query.get('new') === '1') {
        const dialog = document.querySelector('#booking-dialog, #vehicle-dialog');
        if (dialog instanceof HTMLDialogElement) dialog.showModal();
        query.delete('new');
        const updatedQuery = query.toString();
        history.replaceState(null, '', `${window.location.pathname}${updatedQuery ? `?${updatedQuery}` : ''}`);
    }

    const startDate = document.querySelector('[data-start-date]');
    const endDate = document.querySelector('[data-end-date]');
    startDate?.addEventListener('change', () => {
        if (!endDate) return;
        endDate.min = startDate.value;
        if (endDate.value && endDate.value < startDate.value) endDate.value = startDate.value;
    });

    document.querySelectorAll('[data-table-search]').forEach((input) => {
        input.addEventListener('input', () => {
            const table = input.closest('.table-section')?.querySelector('[data-search-table]');
            if (!table) return;
            const value = input.value.trim().toLocaleLowerCase();
            const rows = [...table.querySelectorAll('[data-search-row]')];
            let visibleRows = 0;
            rows.forEach((row) => {
                const matches = row.textContent.toLocaleLowerCase().includes(value);
                row.hidden = !matches;
                if (matches) visibleRows += 1;
            });
            const noResults = input.closest('.table-section')?.querySelector('[data-no-results]');
            if (noResults) noResults.hidden = visibleRows > 0 || rows.length === 0;
        });
    });

    document.querySelectorAll('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => select.form.requestSubmit());
    });

    document.querySelectorAll('[data-notice]').forEach((notice) => {
        notice.querySelector('.notice-dismiss')?.addEventListener('click', () => notice.remove());
    });
});