<?php
// Helper CSRF minimal — Phase 1 de la revue structurelle.
//
// CORRECTION P2 : démarre lui-même la session si besoin, pour éviter les
// oublis de session_start() dans les pages qui n'incluent pas includes/auth.php
// (ex. index.php, dont la vue n'est pas encore protégée par autorisation —
// voir Phase 3 pour une protection globale des pages métier).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//
// Usage dans un formulaire :
//   <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generer_jeton_csrf()) ?>">
//
// Usage dans le script de traitement (action) :
//   if (!verifier_jeton_csrf($_POST['csrf_token'] ?? '')) {
//       die("Erreur : requête invalide (jeton de sécurité manquant ou expiré).");
//   }

function generer_jeton_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifier_jeton_csrf(?string $jeton): bool
{
    return !empty($_SESSION['csrf_token'])
        && !empty($jeton)
        && hash_equals($_SESSION['csrf_token'], $jeton);
}
