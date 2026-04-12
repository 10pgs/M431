(() => {
    const searchBar = document.getElementById('search-bar');
    if (!searchBar) return;

    const games = window.GAME_DETAILS || {};

    searchBar.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const match = Object.entries(games).find(([_, game]) =>
            game.name.toLowerCase().includes(query)
        );
        if (match) {
            const [slug] = match;
            window.location.href = `jeu-detail.html?id=${slug}`;
        }
    });
})();
