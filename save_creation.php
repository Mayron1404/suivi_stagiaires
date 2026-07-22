<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/config/database.php';

// Vérification que le formulaire a bien été envoyé en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: creer_stagiaire.php");
    exit;
}

// Autorisation : cette action doit être protégée au même titre que le
// formulaire qui l'appelle (creer_stagiaire.php).
exiger_acces_creation();

// Jeton CSRF envoyé par le formulaire.
if (!verifier_jeton_csrf($_POST['csrf_token'] ?? null)) {
    erreur_utilisateur("Erreur : requête invalide (jeton de sécurité manquant ou expiré). Merci de revenir en arrière et de réessayer.");
}

// Récupération et nettoyage des champs
$nom            = trim($_POST['nom'] ?? '');
$prenom         = trim($_POST['prenom'] ?? '');
$date_naissance = trim($_POST['date_naissance'] ?? '');
$classe         = trim($_POST['classe'] ?? '');
$etablissement  = trim($_POST['etablissement'] ?? '');
$sexe           = trim($_POST['sexe'] ?? '');
$entreprise     = trim($_POST['entreprise'] ?? '');
$tuteur_nom     = trim($_POST['tuteur_nom'] ?? '');
$tuteur_contact = trim($_POST['tuteur_contact'] ?? '');
$prof_referent_nom     = trim($_POST['prof_referent_nom'] ?? '');
$prof_referent_contact = trim($_POST['prof_referent_contact'] ?? '');

if ($nom === '' || $prenom === '') {
    erreur_utilisateur("Erreur : le nom et le prénom du stagiaire sont obligatoires.");
}

// La date de naissance est optionnelle : on envoie NULL si elle est vide
$date_naissance = $date_naissance !== '' ? $date_naissance : null;

// Le sexe doit être M ou F, sinon NULL
$sexe = in_array($sexe, ['M', 'F'], true) ? $sexe : null;

// Insertion en base de données (uniquement les infos de fiche, pas encore de notes)
$sql = "INSERT INTO stagiaires_notes
    (nom, prenom, date_naissance, classe, etablissement, sexe, entreprise, tuteur_nom, tuteur_contact, prof_referent_nom, prof_referent_contact)
    VALUES
    (:nom, :prenom, :date_naissance, :classe, :etablissement, :sexe, :entreprise, :tuteur_nom, :tuteur_contact, :prof_referent_nom, :prof_referent_contact)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nom'                    => $nom,
    ':prenom'                 => $prenom,
    ':date_naissance'         => $date_naissance,
    ':classe'                 => $classe,
    ':etablissement'          => $etablissement,
    ':sexe'                   => $sexe,
    ':entreprise'             => $entreprise,
    ':tuteur_nom'             => $tuteur_nom,
    ':tuteur_contact'         => $tuteur_contact,
    ':prof_referent_nom'      => $prof_referent_nom,
    ':prof_referent_contact'  => $prof_referent_contact,
]);

// Récupération de l'ID du stagiaire tout juste créé
$id = $pdo->lastInsertId();

// Redirection vers la page de notation, liée à ce stagiaire précis
header("Location: index.php?id=" . $id);
exit;
