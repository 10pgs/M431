# Game Store

Game Store est une petite application web type boutique de jeux. Elle permet de parcourir un catalogue, consulter une fiche de jeu, créer un compte, se connecter, gérer un profil et publier des avis.

## Démarrage rapide
1. Installe Docker et Docker Compose.
2. Lance les services depuis la racine du projet :
   ```bash
   docker compose up --build
   ```
3. Ouvre le site sur http://localhost:8082.

Les pages PHP doivent être ouvertes via Docker/Apache. Si tu ouvres directement les fichiers du dossier `www/` dans le navigateur, les pages comme `profile.php` risquent d'être téléchargées au lieu d'être exécutées.

## Notes
- Cree un fichier `.env` local et renseigne les identifiants OAuth Google uniquement dans ce fichier, qui n'est pas versionne.
- Les emails de connexion / creation de compte utilisent les variables `SMTP_HOST`, `SMTP_PORT`, `SMTP_ENCRYPTION`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` via `docker-compose.yml`.  
- Les pages de succès (login/inscription) utilisent les styles de `css/register.css`.  
- Pour ajouter ou corriger un jeu, modifie `www/data/games.json`.

## URLs utiles
- Site : http://localhost:8082
- Profil : http://localhost:8082/profile.php
- Avis : http://localhost:8082/reviews.html
- phpMyAdmin : http://localhost:8081

## Fonctionnalités
- Accueil avec présentation du projet.
- Catalogue de jeux alimenté par `www/data/games.json` et par la base de données.
- Recherche de jeux avec suggestions.
- Fiche détaillée d'un jeu avec lien de téléchargement externe.
- Inscription, connexion, déconnexion et connexion Google si les identifiants OAuth sont configurés.
- Profil utilisateur avec informations de compte et derniers achats.
- Page d'avis avec sélection du jeu, note, commentaire et affichage des avis publiés.
- Footer commun alimenté par `www/data/team.json`.

## Structure du projet
- `www/` : pages HTML/PHP, styles, scripts, images et données JSON.
- `www/api/` : endpoints PHP utilisés par le front.
- `www/css/` : styles des pages.
- `www/js/` : scripts front, chargement du catalogue, recherche et avis.
- `www/data/games.json` : catalogue local des jeux.
- `www/data/team.json` : liens sociaux affichés dans le footer.
- `database/schema.sql` : création des tables et données principales.
- `database/seed-games.sql` : données de jeux supplémentaires.
- `database/modele-donnees.md` : description du modèle de données.
- `docker-compose.yml` : services web, MySQL et phpMyAdmin.
- `Dockerfile` : image PHP/Apache utilisée par le service web.

## Configuration
Crée un fichier `.env` local si tu veux configurer Google OAuth ou l'envoi d'emails. Ne versionne pas ce fichier.

Variables utiles :
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI`, par défaut `http://localhost:8082/google-callback.php`
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_ENCRYPTION`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

Pour activer la connexion Google :
1. Ouvre Google Cloud Console et crée un projet.
2. Va dans `APIs et services` puis `Identifiants`.
3. Crée un `ID client OAuth` de type `Application Web`.
4. Ajoute cette URI dans les redirections autorisées :
   ```text
   http://localhost:8082/google-callback.php
   ```
5. Copie l'ID client et le secret client dans `.env` :
   ```env
   GOOGLE_CLIENT_ID=ton-client-id-google
   GOOGLE_CLIENT_SECRET=ton-secret-google
   GOOGLE_REDIRECT_URI=http://localhost:8082/google-callback.php
   ```

Copie `.env.example` vers `.env`, puis remplace les valeurs SMTP par celles de ton fournisseur :
```env
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=ton-compte
SMTP_PASSWORD=ton-mot-de-passe
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME=Game Store
```

Si tu utilises Gmail, il faut en général créer un mot de passe d'application et utiliser :
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=ton-adresse@gmail.com
SMTP_PASSWORD=mot-de-passe-application
MAIL_FROM_ADDRESS=ton-adresse@gmail.com
```

La base de données est configurée dans `docker-compose.yml` avec :
- base : `gamestore`
- utilisateur : `user`
- mot de passe : `userpassword`
- root : `GamestoreFunctional`

## Commandes utiles
```bash
docker compose up --build
docker compose up -d --build
docker compose ps
docker compose logs web
docker compose down
```

Tester l'envoi SMTP réel :
```bash
docker compose exec web php tools/test-mail-smtp.php ton-adresse@example.com
```

Ce test envoie un email réel avec la configuration de ton fichier `.env`.

## Dépannage
Si `profile.php` se télécharge quand tu cliques sur Profil, vérifie que tu utilises bien http://localhost:8082 et pas un chemin local du type `file:///...`.

Si le site ne démarre pas, vérifie que le port `8082` est libre, puis relance :
```bash
docker compose up -d --build web
```

Si les données ne semblent pas à jour, redémarre les conteneurs. Pour repartir avec une base complètement neuve, supprime aussi le volume Docker MySQL avant de relancer le projet.

## Modifier le catalogue
Pour corriger ou ajouter un jeu statique, modifie `www/data/games.json`.

Pour modifier les jeux stockés en base, mets à jour `database/schema.sql` ou `database/seed-games.sql`, puis recrée la base si nécessaire.
