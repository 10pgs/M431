(() => {
    if (!window.GAMES_READY) return;

    window.GAMES_READY
        .then(showGame)
        .catch(() => showGame(null));

    function showGame() {
        const gameId = new URLSearchParams(window.location.search).get('id') || '';
        const game = window.GAME_DETAILS[gameId] || {
            name: 'Jeu introuvable',
            price: '-',
            date: '-',
            image: 'img/games/CS2.png',
            downloadUrl: 'games.html',
            longDesc: "Le jeu demande n'a pas ete trouve. Retourne a la liste pour en selectionner un autre."
        };

        setText('game-title', game.name);
        setText('game-meta', `${game.price} - Sortie: ${game.date}`);
        setText('game-desc', game.longDesc);

        const image = document.getElementById('game-image');
        if (image) {
            image.src = game.image;
            image.alt = game.name;
        }

        const downloadLink = document.getElementById('download-link');
        if (downloadLink) downloadLink.href = game.downloadUrl;

        updateTrailer(game.trailer);
    }

    function setText(id, value) {
        const node = document.getElementById(id);
        if (node) node.textContent = value;
    }

    function updateTrailer(url) {
        const zone = document.querySelector('.trailer-video');
        if (!zone) return;

        const embedUrl = toEmbedUrl(url);
        zone.innerHTML = embedUrl
            ? `<iframe src="${embedUrl}" title="Bande-annonce" frameborder="0" allowfullscreen></iframe>`
            : '<p>Aucune bande-annonce disponible.</p>';
    }

    function toEmbedUrl(url) {
        if (!url) return '';
        if (url.includes('youtube.com/watch?v=')) {
            const id = new URL(url).searchParams.get('v');
            return id ? `https://www.youtube.com/embed/${id}` : '';
        }
        if (url.includes('youtu.be/')) {
            const id = url.split('youtu.be/')[1].split('?')[0];
            return `https://www.youtube.com/embed/${id}`;
        }
        return url;
    }
})();
