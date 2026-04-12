(() => {
    const searchBar = document.getElementById('search-bar');
    if (!searchBar) return;

    const games = window.GAME_DETAILS || {};
    const entries = Object.entries(games).map(([slug, game]) => ({
        slug,
        name: game.name,
        normalized: normalize(game.name)
    }));

    // Inject lightweight styles for the dropdown (works across all pages using the shared search bar)
    if (!document.getElementById('search-suggestion-styles')) {
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
                background: #fff;
                border: 1.5px solid #d1d5db;
                border-radius: 12px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
                list-style: none;
                padding: 6px 0;
                max-height: 280px;
                overflow-y: auto;
            }
            .search-suggestions li {
                padding: 10px 14px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                color: #111827;
            }
            .search-suggestions li:hover,
            .search-suggestions li:focus {
                background: linear-gradient(135deg, rgba(37,99,235,0.08), rgba(255,122,24,0.07));
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

    const container = searchBar.closest('form') || searchBar.parentElement;
    const suggestionList = document.createElement('ul');
    suggestionList.className = 'search-suggestions';
    suggestionList.hidden = true;
    container?.appendChild(suggestionList);

    function normalize(str) {
        return (str || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function levenshtein(a, b) {
        const m = a.length;
        const n = b.length;
        const dp = Array.from({ length: m + 1 }, () => new Array(n + 1).fill(0));
        for (let i = 0; i <= m; i++) dp[i][0] = i;
        for (let j = 0; j <= n; j++) dp[0][j] = j;
        for (let i = 1; i <= m; i++) {
            for (let j = 1; j <= n; j++) {
                const cost = a[i - 1] === b[j - 1] ? 0 : 1;
                dp[i][j] = Math.min(
                    dp[i - 1][j] + 1,
                    dp[i][j - 1] + 1,
                    dp[i - 1][j - 1] + cost
                );
            }
        }
        return dp[m][n];
    }

    function bestMatch(query) {
        const qNorm = normalize(query);
        if (!qNorm) return null;
        let best = null;
        let bestScore = Infinity;
        entries.forEach((entry) => {
            const distance = levenshtein(qNorm, entry.normalized);
            const normDistance = distance / Math.max(qNorm.length, entry.normalized.length, 1);
            if (normDistance < bestScore) {
                bestScore = normDistance;
                best = entry;
            }
        });
        return best;
    }

    function renderSuggestions(query) {
        const qNorm = normalize(query);
        suggestionList.innerHTML = '';

        if (!qNorm) {
            suggestionList.hidden = true;
            return;
        }

        const matches = entries
            .filter((entry) => entry.normalized.includes(qNorm))
            .sort((a, b) => {
                const ai = a.normalized.indexOf(qNorm);
                const bi = b.normalized.indexOf(qNorm);
                if (ai !== bi) return ai - bi;
                return a.name.localeCompare(b.name);
            })
            .slice(0, 6);

        if (!matches.length) {
            const li = document.createElement('li');
            li.textContent = 'Aucun jeu trouvé';
            li.className = 'empty';
            li.tabIndex = -1;
            suggestionList.appendChild(li);
            suggestionList.hidden = false;
            return;
        }

        matches.forEach(({ slug, name }) => {
            const li = document.createElement('li');
            li.dataset.slug = slug;
            li.textContent = name;
            li.tabIndex = 0;
            suggestionList.appendChild(li);
        });

        suggestionList.hidden = false;
    }

    function goToGame(slug) {
        if (!slug) {
            window.location.href = 'no-games-found.html';
            return;
        }
        window.location.href = `jeu-detail.html?id=${encodeURIComponent(slug)}`;
    }

    searchBar.addEventListener('input', (event) => {
        renderSuggestions(event.target.value);
    });

    searchBar.addEventListener('focus', () => {
        if (suggestionList.children.length) {
            suggestionList.hidden = false;
        }
    });

    document.addEventListener('click', (event) => {
        if (!suggestionList.contains(event.target) && event.target !== searchBar) {
            suggestionList.hidden = true;
        }
    });

    suggestionList.addEventListener('click', (event) => {
        const item = event.target.closest('li');
        if (!item) return;
        if (item.classList.contains('empty')) {
            goToGame(null);
            return;
        }
        goToGame(item.dataset.slug);
    });

    searchBar.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const query = searchBar.value.trim();
            if (!query) {
                return;
            }
            const match = bestMatch(query);
            if (match) {
                goToGame(match.slug);
            } else {
                goToGame(null);
            }
        }
    });
})();
