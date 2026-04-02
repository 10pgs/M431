-- Schema MySQL 8.0 - GameStore
-- Cree a partir du MLD dans database/modele-donnees.md

DROP DATABASE IF EXISTS gamestore;
CREATE DATABASE IF NOT EXISTS gamestore
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gamestore;

CREATE TABLE IF NOT EXISTS utilisateur (
  id_utilisateur BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(255) NULL,
  password_hash VARCHAR(255) NULL,
  auth_provider ENUM('local', 'google') NOT NULL DEFAULT 'local',
  google_sub VARCHAR(64) NULL,
  card_last4 CHAR(4) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT uq_utilisateur_username UNIQUE (username),
  CONSTRAINT uq_utilisateur_email UNIQUE (email),
  CONSTRAINT uq_utilisateur_google_sub UNIQUE (google_sub),

  CONSTRAINT chk_provider_data CHECK (
    (auth_provider = 'local' AND password_hash IS NOT NULL)
    OR
    (auth_provider = 'google' AND google_sub IS NOT NULL)
  ),
  CONSTRAINT chk_card_last4 CHECK (card_last4 IS NULL OR card_last4 REGEXP '^[0-9]{4}$')
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jeu (
  id_jeu BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL,
  titre VARCHAR(150) NOT NULL,
  description_courte VARCHAR(255) NULL,
  description_longue TEXT NULL,
  prix DECIMAL(10,2) NOT NULL,
  date_sortie DATE NULL,
  image_url VARCHAR(255) NULL,
  download_url VARCHAR(255) NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT uq_jeu_slug UNIQUE (slug),
  CONSTRAINT chk_jeu_prix CHECK (prix >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS achat (
  id_achat BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur BIGINT UNSIGNED NOT NULL,
  id_jeu BIGINT UNSIGNED NOT NULL,
  date_achat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  prix_achat DECIMAL(10,2) NOT NULL,
  statut ENUM('paid', 'refunded', 'cancelled') NOT NULL DEFAULT 'paid',

  CONSTRAINT fk_achat_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_achat_jeu
    FOREIGN KEY (id_jeu) REFERENCES jeu(id_jeu)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_achat_prix CHECK (prix_achat >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS avis (
  id_avis BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur BIGINT UNSIGNED NOT NULL,
  id_jeu BIGINT UNSIGNED NOT NULL,
  note TINYINT UNSIGNED NOT NULL,
  commentaire TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_avis_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_avis_jeu
    FOREIGN KEY (id_jeu) REFERENCES jeu(id_jeu)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uq_avis_user_jeu UNIQUE (id_utilisateur, id_jeu),
  CONSTRAINT chk_avis_note CHECK (note BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE INDEX idx_achat_utilisateur ON achat(id_utilisateur);
CREATE INDEX idx_achat_jeu ON achat(id_jeu);
CREATE INDEX idx_avis_utilisateur ON avis(id_utilisateur);
CREATE INDEX idx_avis_jeu ON avis(id_jeu);
CREATE INDEX idx_jeu_titre ON jeu(titre);

-- Donnees minimales pour tester rapidement
INSERT INTO jeu (slug, titre, description_courte, prix, date_sortie, image_url, download_url)
VALUES
  ('counter-strike-2', 'Counter-Strike 2', 'FPS competitif nouvelle generation', 0.00, '2023-09-27', './img/jeux/CS2.png', 'https://store.steampowered.com/app/730/CounterStrike_2/'),
  ('red-dead-redemption-2', 'Red Dead Redemption 2', 'Western epique en monde ouvert', 59.00, '2018-10-26', './img/jeux/RDR2.jpg', 'https://store.steampowered.com/app/1174180/Red_Dead_Redemption_2/'),
  ('cyberpunk-2077', 'Cyberpunk 2077', 'RPG futuriste en monde ouvert', 39.00, '2020-12-10', './img/jeux/cyberpunk2077.jpg', 'https://store.steampowered.com/app/1091500/Cyberpunk_2077/')
ON DUPLICATE KEY UPDATE
  titre = VALUES(titre),
  prix = VALUES(prix),
  description_courte = VALUES(description_courte);