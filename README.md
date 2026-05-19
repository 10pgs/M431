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
- `www/data/games.json` : catalogue des jeux (titres, dates, prix, images, liens).  
- `www/data/team.json` : liens sociaux affichés dans le footer.  
- `www/js/games.js` : charge le catalogue JSON pour les pages.  
- `www/js/search.js` : recherche avec suggestions.  
- `www/js/cards.js` : affiche les cartes de jeux depuis le JSON.  
- `www/js/layout.js` : affiche le footer commun depuis le JSON.  
- `database/` : init SQL pour MySQL si besoin.  
- `docker-compose.yml` : services web + base de données.

## Notes
- Secrets OAuth Google sont définis dans `www/config.php` (à remplacer par tes valeurs locales).  
- Les emails de connexion / creation de compte utilisent les variables `SMTP_HOST`, `SMTP_PORT`, `SMTP_ENCRYPTION`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` via `docker-compose.yml`.  
- Les pages de succès (login/inscription) utilisent les styles de `css/register.css`.  
- Pour ajouter ou corriger un jeu, modifie `www/data/games.json`.

Bon dev !
