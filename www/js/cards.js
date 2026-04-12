(() => {
    const cards = document.querySelectorAll('.game-card');
    if (!cards.length) return;

    const slugify = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    cards.forEach((card) => {
        card.style.cursor = 'pointer';
        card.setAttribute('role', 'link');
        card.setAttribute('tabindex', '0');

        const gameName = card.dataset.name || '';
        const id = slugify(gameName);

        const goToDetails = () => {
            window.location.href = `game-detail.html?id=${encodeURIComponent(id)}`;
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('.btn-main')) return;
            goToDetails();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                goToDetails();
            }
        });
    });
})();
