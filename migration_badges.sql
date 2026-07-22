-- ============================================================
-- Migration : ajout établissement / prof référent + système de badges
-- À exécuter une seule fois sur la base "notation"
-- ============================================================

-- 1. Nouvelles colonnes sur la fiche du stagiaire
ALTER TABLE stagiaires_notes
    ADD COLUMN etablissement VARCHAR(255) NULL AFTER classe,
    ADD COLUMN prof_referent_nom VARCHAR(255) NULL AFTER etablissement,
    ADD COLUMN prof_referent_contact VARCHAR(255) NULL AFTER prof_referent_nom;

-- 2. Table des badges disponibles (le catalogue)
CREATE TABLE IF NOT EXISTS badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    icone VARCHAR(10) NOT NULL DEFAULT '🏅'
);

-- 3. Table de liaison : quel stagiaire a reçu quel badge
CREATE TABLE IF NOT EXISTS stagiaire_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    badge_id INT NOT NULL,
    date_obtention DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires_notes(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

-- 4. Exemple (à adapter / remplacer quand tu m'enverras la vraie liste des badges)
-- INSERT INTO badges (nom, description, icone) VALUES
-- ('Ponctuel', 'Toujours arrivé à l\'heure durant tout le stage', '⏰'),
-- ('Esprit d\'équipe', 'A particulièrement bien collaboré avec les autres', '🤝');
