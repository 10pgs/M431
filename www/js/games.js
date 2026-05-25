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

        const games = (await response.json()).sort((a, b) => {
            const dateDiff = (Date.parse(b.date || '') || 0) - (Date.parse(a.date || '') || 0);
            return dateDiff || a.name.localeCompare(b.name);
        });
        window.GAME_LIST = games;
        window.GAME_DETAILS = Object.fromEntries(games.map((game) => [game.slug, game]));
        return games;
    }
})();
