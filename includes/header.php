<?php
/**
 * includes/header.php
 * En-tête HTML commun (structure <head> + bandeau <header>).
 *
 * CORRECTION P2 : mutualise le squelette HTML répété dans chaque page.
 *
 * Variables attendues, à définir AVANT l'inclusion :
 *   $page_title  (string)          Titre de l'onglet et du bandeau. Obligatoire.
 *   $extra_head  (string, optionnel) Balises supplémentaires pour <head>
 *                                     (ex. dépendances Chart.js, CSS spécifique
 *                                     à une page).
 *
 * Usage :
 *   $page_title = "Fiche des stagiaires";
 *   require __DIR__ . '/includes/header.php';
 */

if (!isset($page_title)) {
    $page_title = 'Application stagiaires';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <?= $extra_head ?? '' ?>
</head>
<body>

<header><?= htmlspecialchars($page_title) ?></header>
