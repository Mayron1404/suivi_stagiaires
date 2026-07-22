<?php
require_once __DIR__ . '/config/database.php';

// Récupération du terme de recherche éventuel (envoyé depuis la barre de recherche de l'accueil)
$recherche = trim($_GET['recherche'] ?? '');

// Récupération des stagiaires (filtrés si une recherche est en cours)
// Le titre de cette page annonce "stagiaires notés" : on filtre donc
// explicitement sur note20, pour ne pas afficher les fiches pas encore
// évaluées (note20 NULL).
$sql = "SELECT * FROM stagiaires_notes WHERE note20 IS NOT NULL";
if ($recherche !== '') {
    $sql .= " AND (nom LIKE :recherche OR prenom LIKE :recherche)";
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
if ($recherche !== '') {
    $stmt->bindValue(':recherche', '%' . $recherche . '%');
}
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Liste des stagiaires notés";
require __DIR__ . '/includes/header.php';
?>

<div class="container container-large">

<p><a href="home.php">← Retour à l'accueil</a></p>

<p class="info-acces">
    Cette liste n'affiche que les stagiaires déjà notés.
    Pour les fiches en attente d'évaluation, voir la
    <a href="fiche_stagiaire.php">fiche des stagiaires</a>.
</p>

<form action="liste.php" method="get" class="search-form">
    <input type="text" name="recherche" value="<?= htmlspecialchars($recherche) ?>" placeholder="Rechercher un stagiaire...">
    <button type="submit" aria-label="Rechercher">🔍</button>
</form>

<?php if ($recherche !== ''): ?>
    <p class="info-acces">
        <?= count($rows) ?> résultat(s) pour « <?= htmlspecialchars($recherche) ?> »
        — <a href="liste.php">réinitialiser</a>
    </p>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Classe</th>
        <th>Note /20</th>
        <th>Appréciation</th>
        <th>Travail équipe</th>
        <th>Communication</th>
        <th>Écoute</th>
        <th>Téléphone</th>
        <th>Gestion temps</th>
        <th>Autonomie</th>
        <th>Ponctualité</th>
        <th>Politesse</th>
        <th>Apprentissage</th>
        <th>Curiosité</th>
        <th>Montée comp.</th>
    </tr>

    <?php if (empty($rows)): ?>
        <tr>
            <td colspan="17" style="text-align:center; color:#888;">Aucun stagiaire trouvé.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['nom']) ?></td>
            <td><?= htmlspecialchars($r['prenom'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['classe'] ?? '') ?></td>
            <td><strong><?= htmlspecialchars($r['note20'] ?? '—') ?></strong></td>
            <td style="max-width:220px;">
                <?php
                    $appr = $r['appreciation'] ?? '';
                    echo $appr !== ''
                        ? htmlspecialchars(mb_strlen($appr) > 80 ? mb_substr($appr, 0, 80) . '…' : $appr)
                        : '—';
                ?>
            </td>

            <td><?= $r['travail_equipe'] ?> ★</td>
            <td><?= $r['communication'] ?> ★</td>
            <td><?= $r['ecoute'] ?> ★</td>
            <td><?= $r['telephone'] ?> ★</td>
            <td><?= $r['gestion_temps'] ?> ★</td>
            <td><?= $r['autonomie'] ?> ★</td>
            <td><?= $r['ponctualite'] ?> ★</td>
            <td><?= $r['politesse'] ?> ★</td>
            <td><?= $r['apprentissage'] ?> ★</td>
            <td><?= $r['curiosite'] ?> ★</td>
            <td><?= $r['montee_comp'] ?> ★</td>
        </tr>
    <?php endforeach; ?>

</table>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
