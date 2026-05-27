(() => {
    const form = document.getElementById('review-form');
    const gameSelect = document.getElementById('review-game');
    const noteSelect = document.getElementById('review-note');
    const commentInput = document.getElementById('review-comment');
    const feedback = document.getElementById('review-feedback');
    const list = document.getElementById('reviews-list');
    const empty = document.getElementById('reviews-empty');

    if (!form || !gameSelect || !noteSelect || !commentInput || !feedback || !list || !empty || !window.GAMES_READY) {
        return;
    }

    window.GAMES_READY
        .then((loadedGames) => {
            fillGames(loadedGames);
            return loadReviews();
        })
        .catch(() => showFeedback('Impossible de charger les jeux ou les avis.', true));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        await publishReview();
    });

    function fillGames(items) {
        gameSelect.innerHTML = '<option value="">Choisir un jeu</option>';
        items
            .slice()
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach((game) => {
                const option = document.createElement('option');
                option.value = game.slug;
                option.textContent = game.name;
                gameSelect.appendChild(option);
            });
    }

    async function loadReviews() {
        const response = await fetch('api/reviews.php', { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        if (!payload.ok) throw new Error(payload.error || 'reviews_error');
        renderReviews(payload.reviews || []);
    }

    async function publishReview() {
        clearFeedback();
        const data = new FormData(form);

        if (!data.get('slug') || !data.get('note') || !String(data.get('commentaire') || '').trim()) {
            showFeedback('Choisis un jeu, une note et écris ton avis.', true);
            return;
        }

        const response = await fetch('api/reviews.php', {
            method: 'POST',
            body: data,
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json();

        if (!payload.ok) {
            showFeedback(payload.message || 'Impossible de publier ton avis.', true);
            if (response.status === 401) {
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 900);
            }
            return;
        }

        showFeedback(payload.message || 'Ton avis a été publié.');
        noteSelect.value = '5';
        commentInput.value = '';
        await loadReviews();
    }

    function renderReviews(reviews) {
        list.innerHTML = '';
        empty.hidden = reviews.length > 0;

        reviews.forEach((review) => {
            const article = document.createElement('article');
            article.className = 'review-card';

            const note = Math.max(0, Math.min(5, Number(review.note || 0)));
            const stars = '★'.repeat(note).padEnd(5, '☆');
            const date = review.updated_at || review.created_at || '';

            article.innerHTML = [
                '<div class="review-card__top">',
                '<div>',
                '<h3>' + escapeHtml(review.jeu || 'Jeu') + '</h3>',
                '<p>' + escapeHtml(review.username || 'Joueur') + '</p>',
                '</div>',
                '<strong aria-label="Note ' + note + ' sur 5">' + stars + '</strong>',
                '</div>',
                '<p class="review-card__comment">' + escapeHtml(review.commentaire || '') + '</p>',
                '<time>' + escapeHtml(formatDate(date)) + '</time>',
            ].join('');
            list.appendChild(article);
        });
    }

    function showFeedback(message, isError = false) {
        feedback.textContent = message;
        feedback.className = isError ? 'review-feedback is-error' : 'review-feedback is-success';
        feedback.hidden = false;
    }

    function clearFeedback() {
        feedback.textContent = '';
        feedback.hidden = true;
    }

    function formatDate(value) {
        if (!value) return '';
        const parsed = new Date(String(value).replace(' ', 'T'));
        return Number.isNaN(parsed.getTime())
            ? value
            : parsed.toLocaleDateString('fr-CH', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
})();
