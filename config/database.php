<?php
/**
 * config/database.php
 * Connexion PDO unique à la base "notation".
 *
 * CORRECTION P2 : remplace les blocs de connexion auparavant dupliqués dans
 * sept fichiers (index.php, save.php, save_creation.php, liste.php,
 * fiche_stagiaire.php, fiche_stagiaire_detail.php, graphique.php).
 * Toute modification de serveur, d'identifiants ou d'options PDO ne se fait
 * plus qu'à un seul endroit.
 *
 * Usage : require_once __DIR__ . '/config/database.php';
 * Fournit la variable $pdo, prête à l'emploi.
 *
 * ATTENTION SÉCURITÉ : ce fichier utilise encore le compte "root" sans mot
 * de passe, hérité du projet d'origine. Pour un usage réel, créer un compte
 * MySQL dédié avec uniquement les droits nécessaires sur la base "notation"
 * (SELECT, INSERT, UPDATE, DELETE), et ne jamais utiliser root en production.
 */

$dsn     = "mysql:host=localhost;dbname=notation;charset=utf8";
$db_user = "root";
$db_pass = "";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    // CORRECTION P2 : le message technique PDO (serveur, base, compte...)
    // n'est plus jamais affiché à l'utilisateur. Il est journalisé côté
    // serveur, et un message neutre est montré à la place.
    error_log("Erreur connexion base de données : " . $e->getMessage());
    http_response_code(500);
    die("Une erreur technique est survenue. Merci de réessayer plus tard.");
}
