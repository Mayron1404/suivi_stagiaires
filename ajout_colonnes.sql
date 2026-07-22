-- A exécuter UNE SEULE FOIS dans phpMyAdmin (onglet SQL) sur la base "notation"
-- Ajoute les nouvelles infos du stagiaire + rend les notes optionnelles
-- pour permettre de créer une fiche stagiaire sans notes pour l'instant.

ALTER TABLE stagiaires_notes
  ADD COLUMN prenom VARCHAR(100) NULL AFTER nom,
  ADD COLUMN date_naissance DATE NULL AFTER prenom,
  ADD COLUMN classe VARCHAR(50) NULL AFTER date_naissance,
  ADD COLUMN sexe ENUM('M','F') NULL AFTER classe,
  ADD COLUMN entreprise VARCHAR(150) NULL AFTER sexe,
  ADD COLUMN tuteur_nom VARCHAR(150) NULL AFTER entreprise,
  ADD COLUMN tuteur_contact VARCHAR(150) NULL AFTER tuteur_nom;

-- Si l'insertion échoue avec un message du type
-- "Field 'note20' doesn't have a default value" (ou pareil pour les colonnes d'étoiles),
-- exécute aussi ceci :

ALTER TABLE stagiaires_notes
  MODIFY note20 DECIMAL(4,1) NULL DEFAULT NULL,
  MODIFY travail_equipe INT NOT NULL DEFAULT 0,
  MODIFY communication INT NOT NULL DEFAULT 0,
  MODIFY ecoute INT NOT NULL DEFAULT 0,
  MODIFY telephone INT NOT NULL DEFAULT 0,
  MODIFY gestion_temps INT NOT NULL DEFAULT 0,
  MODIFY autonomie INT NOT NULL DEFAULT 0,
  MODIFY ponctualite INT NOT NULL DEFAULT 0,
  MODIFY politesse INT NOT NULL DEFAULT 0,
  MODIFY apprentissage INT NOT NULL DEFAULT 0,
  MODIFY curiosite INT NOT NULL DEFAULT 0,
  MODIFY montee_comp INT NOT NULL DEFAULT 0;
