(() => {
    const footer = document.getElementById('site-footer');
    if (!footer) return;
    addFooterStyles();

    fetch('data/team.json')
        .then((response) => response.json())
        .then(renderFooter)
        .catch(() => {
            footer.innerHTML = `
                <p>&copy; Tout droit réservé à Thierry Tavares da Costa, Dan Zorev et Jason Haran.</p>
                ${renderLegalLinks()}
            `;
        });

    function renderFooter(team) {
        footer.innerHTML = `
            <p>&copy; Tout droit réservé à Thierry Tavares da Costa, Dan Zorev et Jason Haran.</p>
            <div class="social-footer">
                ${team.map(renderPerson).join('')}
            </div>
            ${renderLegalLinks()}
        `;
    }

    function renderPerson(person) {
        return `
            <section class="social-person">
                <p class="social-person__name">${person.name}</p>
                <div class="social-links">
                    ${renderLink(person.instagram, `Instagram de ${person.name}`, instagramIcon())}
                    ${renderLink(person.tiktok, `TikTok de ${person.name}`, tiktokIcon())}
                    ${renderLink(person.github, `GitHub de ${person.name}`, githubIcon())}
                </div>
            </section>
        `;
    }

    function renderLink(href, label, icon) {
        const external = href !== '#';
        const attrs = external ? ' target="_blank" rel="noopener noreferrer"' : '';
        return `<a href="${href}" class="social-link" aria-label="${label}" title="${label.split(' de ')[0]}"${attrs}>${icon}</a>`;
    }

    function renderLegalLinks() {
        return `
            <nav class="legal-footer" aria-label="Liens légaux">
                <a href="cgu.html">Conditions générales d'utilisation</a>
                <a href="cgv.html">Conditions générales de vente</a>
            </nav>
        `;
    }

    function addFooterStyles() {
        if (document.getElementById('footer-legal-styles')) return;

        const style = document.createElement('style');
        style.id = 'footer-legal-styles';
        style.textContent = `
            .legal-footer {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px 22px;
                margin-top: 22px;
                font-size: 0.92rem;
            }

            .legal-footer a {
                color: #4b5563;
                font-weight: 700;
                text-decoration: none;
            }

            .legal-footer a:hover {
                color: #111827;
                text-decoration: underline;
                text-underline-offset: 4px;
            }
        `;
        document.head.appendChild(style);
    }

    function instagramIcon() {
        return `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect>
                <circle cx="12" cy="12" r="4"></circle>
                <circle cx="17.5" cy="6.5" r="1.1" class="social-link__fill"></circle>
            </svg>
        `;
    }

    function tiktokIcon() {
        return `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M14 4c1 2 2.5 3.3 4.5 3.7V11c-1.5-.1-2.9-.6-4.1-1.4v4.9a5.4 5.4 0 1 1-5.4-5.4c.4 0 .8 0 1.2.1v3.1a2.3 2.3 0 1 0 1.8 2.2V4H14Z" class="social-link__fill"></path>
            </svg>
        `;
    }

    function githubIcon() {
        return `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 2.5a9.5 9.5 0 0 0-3 18.5c.5.1.7-.2.7-.5v-1.8c-2.8.6-3.4-1.2-3.4-1.2-.4-1-.9-1.3-.9-1.3-.8-.5.1-.5.1-.5.9.1 1.4.9 1.4.9.8 1.3 2 1 2.5.7.1-.6.3-1 .6-1.3-2.3-.3-4.7-1.1-4.7-5A4 4 0 0 1 6.4 8a3.7 3.7 0 0 1 .1-2.7s.8-.3 2.8 1A9.6 9.6 0 0 1 12 6c.9 0 1.8.1 2.7.4 2-1.3 2.8-1 2.8-1A3.7 3.7 0 0 1 17.6 8a4 4 0 0 1 1.1 2.8c0 3.9-2.4 4.7-4.8 5 .3.2.7.8.7 1.7v2.5c0 .3.2.6.7.5A9.5 9.5 0 0 0 12 2.5Z" class="social-link__fill"></path>
            </svg>
        `;
    }
})();
