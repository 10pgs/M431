(() => {
    const grid = document.querySelector('.game-block-main');
    if (!grid) return;

    window.GAMES_READY
        .then(renderCards)
        .catch(() => {
            grid.innerHTML = '<p class="empty-state">Impossible de charger le catalogue.</p>';
        });

    function renderCards(games) {
        grid.innerHTML = '';
        games.forEach((game) => grid.appendChild(createCard(game)));
    }

    function createCard(game) {
        const card = document.createElement('section');
        card.className = 'game-card';
        card.dataset.name = game.name;
        card.dataset.price = game.price;
        card.dataset.date = game.date;
        card.dataset.desc = game.shortDesc;
        card.tabIndex = 0;
        card.role = 'link';

        const image = document.createElement('img');
        image.src = game.image;
        image.alt = game.alt || game.name;

        const action = document.createElement('div');
        action.className = 'card-action';
        action.innerHTML = '<a href="login.html" class="btn-main">Acheter</a>';

        const openDetails = () => {
            window.location.href = `game-detail.html?id=${encodeURIComponent(game.slug)}`;
        };

        card.addEventListener('click', (event) => {
            if (!event.target.closest('.btn-main')) openDetails();
        });

        card.addEventListener('keydown', (event) => {
            if (!['Enter', ' '].includes(event.key)) return;
            event.preventDefault();
            openDetails();
        });

        card.append(image, action);
        return card;
    }
})();
