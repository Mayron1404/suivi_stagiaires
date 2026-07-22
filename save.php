<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/config/database.php';

// Vérification que le formulaire a bien été envoyé en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: home.php");
    exit;
}

// Autorisation : même contrôle que pour la création de fiche, faute d'un
// système de comptes dédié (voir Phase 3 pour une authentification complète).
exiger_acces_creation();

// Jeton CSRF envoyé par le formulaire.
if (!verifier_jeton_csrf($_POST['csrf_token'] ?? null)) {
    erreur_utilisateur("Erreur : requête invalide (jeton de sécurité manquant ou expiré). Merci de revenir en arrière et de réessayer.");
}

// CORRECTION P2 : l'identifiant n'était auparavant pas validé comme entier
// positif ; une valeur invalide pouvait provoquer un comportement inattendu.
$id = valider_id_positif($_POST['id'] ?? null);
if ($id === null) {
    erreur_utilisateur("Erreur : identifiant du stagiaire invalide ou manquant.");
}

$appreciation = trim($_POST['appreciation'] ?? '');

// Liste des compétences notées — source unique (voir includes/competences.php)
$competences = array_keys(require __DIR__ . '/includes/competences.php');

// Récupération des notes (0 à 5 étoiles), avec sécurisation des valeurs
$notes = [];
foreach ($competences as $comp) {
    $valeur = (int)($_POST[$comp] ?? 0);
    // On force la valeur entre 0 et 5 par sécurité
    $notes[$comp] = max(0, min(5, $valeur));
}

// Calcul de la note finale sur 20
// 11 compétences notées sur 5 étoiles max = 55 points maximum
$totalEtoiles = array_sum($notes);
$noteMax = count($competences) * 5; // 55
$note20 = round(($totalEtoiles / $noteMax) * 20, 1);

// Une fiche existe déjà (créée via creer_stagiaire.php) → on la met à jour avec les notes
$sql = "UPDATE stagiaires_notes SET
    note20 = :note20,
    appreciation = :appreciation,
    travail_equipe = :travail_equipe,
    communication = :communication,
    ecoute = :ecoute,
    telephone = :telephone,
    gestion_temps = :gestion_temps,
    autonomie = :autonomie,
    ponctualite = :ponctualite,
    politesse = :politesse,
    apprentissage = :apprentissage,
    curiosite = :curiosite,
    montee_comp = :montee_comp
    WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id'             => $id,
    ':note20'         => $note20,
    ':appreciation'   => $appreciation,
    ':travail_equipe' => $notes['travail_equipe'],
    ':communication'  => $notes['communication'],
    ':ecoute'         => $notes['ecoute'],
    ':telephone'      => $notes['telephone'],
    ':gestion_temps'  => $notes['gestion_temps'],
    ':autonomie'      => $notes['autonomie'],
    ':ponctualite'    => $notes['ponctualite'],
    ':politesse'      => $notes['politesse'],
    ':apprentissage'  => $notes['apprentissage'],
    ':curiosite'      => $notes['curiosite'],
    ':montee_comp'    => $notes['montee_comp'],
]);

// CORRECTION P2 : on vérifie désormais que la ligne existait bien avant de
// rediriger comme si tout s'était bien passé (un id inexistant mettait
// silencieusement à jour zéro ligne auparavant).
if ($stmt->rowCount() === 0) {
    erreur_utilisateur("Erreur : ce stagiaire n'existe pas ou a déjà été supprimé.");
}

// Redirection vers la liste des stagiaires notés
header("Location: liste.php");
exit;
