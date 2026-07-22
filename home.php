<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Gestion des stagiaires";
require __DIR__ . '/includes/header.php';
?>

<div class="container">

    <form action="liste.php" method="get" class="search-form">
        <input type="text" name="recherche" placeholder="Rechercher un stagiaire..." autofocus>
        <button type="submit" aria-label="Rechercher">🔍</button>
    </form>

    <p class="sous-titre">Bienvenue au Lycée Polyvalent Jule Garnier — Rechercher le meilleur stagiaire</p>

    <div class="menu">
        <a href="code_acces.php">Créer un stagiaire</a>
        <a href="liste.php">Voir les stagiaires notés</a>
        <a href="fiche_stagiaire.php">Fiche des stagiaires</a>
        <a href="graphique.php">📊 Voir le graphique</a>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
