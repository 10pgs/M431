(() => {
    const input = document.getElementById('search-bar');
    if (!input || !window.GAMES_READY) return;

    window.GAMES_READY.then((games) => initSearch(input, games));

    function initSearch(input, gamesData) {
        const games = gamesData.map((game) => ({
            slug: game.slug,
            name: game.name,
            key: clean(game.name),
            date: Date.parse(game.date || '') || 0
        }));

        const list = document.createElement('ul');
        list.className = 'search-suggestions';
        list.hidden = true;
        input.closest('form')?.appendChild(list);
        addSuggestionStyles();

        input.addEventListener('input', () => showSuggestions(input.value));
        input.addEventListener('focus', () => {
            list.hidden = !list.children.length;
        });

        input.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();
            openGame(findGames(input.value)[0]?.slug);
        });

        list.addEventListener('click', (event) => {
            const item = event.target.closest('li');
            if (item) openGame(item.dataset.slug);
        });

        document.addEventListener('click', (event) => {
            if (event.target !== input && !list.contains(event.target)) {
                list.hidden = true;
            }
        });

        function findGames(value) {
            const query = clean(value.trim());
            if (!query) return [];

            return games
                .filter((game) => game.key.includes(query))
                .sort((a, b) => {
                    const startDiff = Number(b.key.startsWith(query)) - Number(a.key.startsWith(query));
                    if (startDiff) return startDiff;
                    if (a.date !== b.date) return b.date - a.date;
                    return a.name.localeCompare(b.name);
                });
        }

        function showSuggestions(value) {
            const matches = findGames(value).slice(0, 6);
            list.innerHTML = '';

            if (!value.trim()) {
                list.hidden = true;
                return;
            }

            if (!matches.length) {
                list.appendChild(makeItem('Aucun jeu trouve'));
                list.hidden = false;
                return;
            }

            matches.forEach((game) => list.appendChild(makeItem(game.name, game.slug)));
            list.hidden = false;
        }
    }

    function clean(value) {
        return (value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function makeItem(text, slug = '') {
        const item = document.createElement('li');
        item.textContent = text;
        item.dataset.slug = slug;
        item.tabIndex = slug ? 0 : -1;
        if (!slug) item.className = 'empty';
        return item;
    }

    function openGame(slug) {
        window.location.href = slug
            ? `game-detail.html?id=${encodeURIComponent(slug)}`
            : 'no-games-found.html';
    }

    function addSuggestionStyles() {
        if (document.getElementById('search-suggestion-styles')) return;

        const style = document.createElement('style');
        style.id = 'search-suggestion-styles';
        style.textContent = `
            .search-form { position: relative; }
            .search-suggestions {
                position: absolute;
                z-index: 30;
                left: 0;
                right: 0;
                top: calc(100% + 8px);
                max-height: 280px;
                margin: 0;
                padding: 6px 0;
                overflow-y: auto;
                list-style: none;
                background: #fff;
                border: 1.5px solid #d1d5db;
                border-radius: 12px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            }
            .search-suggestions li {
                padding: 10px 14px;
                cursor: pointer;
                font-weight: 600;
                color: #111827;
            }
            .search-suggestions li:hover,
            .search-suggestions li:focus {
                background: rgba(37, 99, 235, 0.08);
                outline: none;
            }
            .search-suggestions li.empty {
                cursor: default;
                color: #6b7280;
                font-weight: 500;
            }
        `;
        document.head.appendChild(style);
    }
})();
