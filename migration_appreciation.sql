-- ============================================================
-- Migration : ajout de la colonne "appreciation" (appréciation globale)
-- À exécuter une seule fois sur la base "notation"
-- ============================================================

ALTER TABLE stagiaires_notes
    ADD COLUMN appreciation TEXT NULL AFTER note20;
