<?php
/**
 * includes/validation.php
 * Fonctions de validation et de gestion d'erreur communes.
 *
 * CORRECTION P2 : uniformise la façon dont les fichiers d'action
 * (save.php, save_creation.php...) rejettent une requête invalide, au lieu
 * de multiplier des die("Erreur : ...") au texte et au comportement
 * différents d'un fichier à l'autre.
 */

/**
 * Arrête l'exécution avec un message d'erreur utilisateur neutre et un code
 * HTTP 400. Le message doit rester compréhensible par un utilisateur final
 * (pas de détail technique/SQL) : voir config/database.php pour les erreurs
 * de connexion, qui suivent la même logique de séparation
 * message-technique-en-log / message-utilisateur-neutre.
 */
function erreur_utilisateur(string $message): void
{
    http_response_code(400);
    die(htmlspecialchars($message));
}

/**
 * Valide qu'une valeur est un entier positif (ex. un identifiant de
 * stagiaire venant de $_POST ou $_GET). Retourne l'entier si valide,
 * ou null sinon.
 */
function valider_id_positif($valeur): ?int
{
    $id = filter_var($valeur, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    return $id !== false ? $id : null;
}
