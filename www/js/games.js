(() => {
    const dataUrl = 'data/games.json';

    window.GAME_LIST = [];
    window.GAME_DETAILS = {};
    window.GAMES_READY = loadGames();

    async function loadGames() {
        const response = await fetch(dataUrl);
        if (!response.ok) {
            throw new Error(`Impossible de charger ${dataUrl}`);
        }

        const games = await response.json();
        window.GAME_LIST = games;
        window.GAME_DETAILS = Object.fromEntries(games.map((game) => [game.slug, game]));
        return games;
    }
})();
