<?php
/**
 * includes/auth.php
 * Contrôle d'accès commun basé sur le code d'accès partagé.
 *
 * CORRECTION P2 : mutualise la vérification de session qui était répétée
 * (et parfois oubliée, cf. save_creation.php avant correction) dans
 * plusieurs fichiers. Toute page ou action sensible doit appeler
 * exiger_acces_creation() dès le début du traitement.
 *
 * LIMITE CONNUE : ce mécanisme reste un code partagé sans notion
 * d'utilisateur individuel, de rôle ou de déconnexion. Une authentification
 * par comptes est prévue en Phase 3 de la revue structurelle.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Bloque l'accès et redirige vers code_acces.php si l'autorisation
 * "acces_creation" n'a pas été validée dans la session en cours.
 */
function exiger_acces_creation(): void
{
    if (empty($_SESSION['acces_creation'])) {
        header("Location: code_acces.php");
        exit;
    }
}
