(() => {
    const games = window.GAME_DETAILS || {};

    const params = new URLSearchParams(window.location.search);
    const gameId = params.get('id') || '';
    const game = games[gameId] || {
        name: 'Jeu introuvable',
        price: '-',
        date: '-',
        image: './img/games/CS2.png',
        downloadUrl: 'games.html',
        longDesc: "Le jeu demande n'a pas ete trouve. Retourne a la liste pour en selectionner un autre."
    };

    const title = document.getElementById('game-title');
    const meta = document.getElementById('game-meta');
    const desc = document.getElementById('game-desc');
    const image = document.getElementById('game-image');
    const downloadLink = document.getElementById('download-link');

    if (title) title.textContent = game.name;
    if (meta) meta.textContent = `${game.price} - Sortie: ${game.date}`;
    if (desc) desc.textContent = game.longDesc;
    if (image) {
        image.src = game.image;
        image.alt = game.name;
    }
    if (downloadLink) downloadLink.href = game.downloadUrl;

    updateTrailer(game);

    function updateTrailer(g) {
        const zone = document.querySelector('.trailer-video');
        if (!zone) return;
        const embedUrl = toEmbedUrl(g.trailer);
        zone.innerHTML = embedUrl
            ? `<iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`
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
