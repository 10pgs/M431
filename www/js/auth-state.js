(() => {
    const guestSelector = '[data-auth-guest]';
    const userSelector = '[data-auth-user]';

    window.AUTH_READY = fetch('api/session.php', { headers: { Accept: 'application/json' } })
        .then((response) => response.ok ? response.json() : { authenticated: false })
        .catch(() => ({ authenticated: false }));

    window.AUTH_READY.then((session) => {
        const isAuthenticated = Boolean(session && session.authenticated);
        document.documentElement.classList.toggle('is-authenticated', isAuthenticated);
        document.documentElement.classList.toggle('is-guest', !isAuthenticated);

        document.querySelectorAll(guestSelector).forEach((element) => {
            element.hidden = isAuthenticated;
        });

        document.querySelectorAll(userSelector).forEach((element) => {
            element.hidden = !isAuthenticated;
        });

        document.querySelectorAll('[data-auth-href-user]').forEach((element) => {
            if (isAuthenticated) element.setAttribute('href', element.dataset.authHrefUser);
        });

        document.querySelectorAll('[data-auth-text-user]').forEach((element) => {
            if (isAuthenticated) element.textContent = element.dataset.authTextUser;
        });

        const redirectTarget = document.body?.dataset.authRedirect;
        if (isAuthenticated && redirectTarget) {
            window.location.replace(redirectTarget);
        }
    });
})();
