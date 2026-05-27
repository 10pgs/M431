(() => {
    const dataUrl = 'data/games.json';
    const databaseUrl = 'api/games.php';

    window.GAME_LIST = [];
    window.GAME_DETAILS = {};
    window.GAMES_READY = loadGames();

    async function loadGames() {
        const response = await fetch(dataUrl);
        if (!response.ok) {
            throw new Error(`Impossible de charger ${dataUrl}`);
        }

        const localGames = await response.json();
        const databaseGames = await loadDatabaseGames();
        const games = mergeGames(localGames, databaseGames).sort((a, b) => {
            const dateDiff = (Date.parse(b.date || '') || 0) - (Date.parse(a.date || '') || 0);
            return dateDiff || a.name.localeCompare(b.name);
        });

        window.GAME_LIST = games;
        window.GAME_DETAILS = Object.fromEntries(games.map((game) => [game.slug, game]));
        return games;
    }

    async function loadDatabaseGames() {
        try {
            const response = await fetch(databaseUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) return [];

            const payload = await response.json();
            return payload.ok && Array.isArray(payload.games) ? payload.games : [];
        } catch (error) {
            return [];
        }
    }

    function mergeGames(localGames, databaseGames) {
        const merged = new Map();

        localGames.forEach((game) => {
            merged.set(game.slug, game);
        });

        databaseGames.forEach((game) => {
            const existing = merged.get(game.slug) || {};
            merged.set(game.slug, { ...existing, ...game });
        });

        return Array.from(merged.values());
    }
})();
