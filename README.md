# Game Store

Petite application vitrine pour naviguer dans une collection de jeux vidéo, afficher leurs fiches détaillées et gérer une inscription/connexion basique.

## Démarrage rapide
1) Installe Docker et Docker Compose.  
2) Depuis la racine du projet :  
   ```bash
   docker-compose up --build
   ```  
3) Ouvre le front sur http://localhost (les pages principales sont `index.html`, `games.html`, `game-detail.html`).

## Structure
- `www/` : fichiers statiques (HTML/CSS/JS) et assets.  
- `www/js/games.js` : données des jeux (titres, dates, prix, liens).  
- `www/js/search.js` : recherche avec suggestions.  
- `www/js/cards.js` : navigation vers les fiches depuis les vignettes.  
- `database/` : init SQL pour MySQL si besoin.  
- `docker-compose.yml` : services web + base de données.

## Notes
- Secrets OAuth Google sont définis dans `www/config.php` (à remplacer par tes valeurs locales).  
- Les emails de connexion / creation de compte utilisent les variables `SMTP_HOST`, `SMTP_PORT`, `SMTP_ENCRYPTION`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` via `docker-compose.yml`.  
- Les pages de succès (login/inscription) utilisent les styles de `css/register.css`.  
- Pour ajouter ou corriger un jeu, modifie `www/js/games.js` (slug, nom, date ISO, prix, lien).

Bon dev !
