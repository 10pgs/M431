(() => {
    const slugify = (text) => text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    document.querySelectorAll('.game-card').forEach((card) => {
        const id = slugify(card.dataset.name || '');
        const openDetails = () => {
            window.location.href = `game-detail.html?id=${encodeURIComponent(id)}`;
        };

        card.style.cursor = 'pointer';
        card.tabIndex = 0;
        card.setAttribute('role', 'link');

        card.addEventListener('click', (event) => {
            if (!event.target.closest('.btn-main')) openDetails();
        });

        card.addEventListener('keydown', (event) => {
            if (!['Enter', ' '].includes(event.key)) return;
            event.preventDefault();
            openDetails();
        });
    });
})();
