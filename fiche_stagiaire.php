<?php
require_once __DIR__ . '/config/database.php';

// Terme de recherche (nom, prénom, classe ou établissement)
$recherche = trim($_GET['recherche'] ?? '');

$sql = "SELECT
            s.id, s.nom, s.prenom, s.classe, s.etablissement,
            s.prof_referent_nom, s.prof_referent_contact,
            s.note20, s.appreciation,
            GROUP_CONCAT(CONCAT(b.icone, ' ', b.nom) SEPARATOR '  ') AS badges_list
        FROM stagiaires_notes s
        LEFT JOIN stagiaire_badges sb ON sb.stagiaire_id = s.id
        LEFT JOIN badges b ON b.id = sb.badge_id";

if ($recherche !== '') {
    $sql .= " WHERE s.nom LIKE :recherche OR s.prenom LIKE :recherche
              OR s.classe LIKE :recherche OR s.etablissement LIKE :recherche";
}

$sql .= " GROUP BY s.id ORDER BY s.nom ASC, s.prenom ASC";

$stmt = $pdo->prepare($sql);
if ($recherche !== '') {
    $stmt->bindValue(':recherche', '%' . $recherche . '%');
}
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Fiche des stagiaires";
require __DIR__ . '/includes/header.php';
?>

<div class="container container-large">

    <p><a href="home.php">← Retour à l'accueil</a></p>

    <form action="fiche_stagiaire.php" method="get" class="search-form">
        <input type="text" name="recherche" value="<?= htmlspecialchars($recherche) ?>" placeholder="Rechercher un stagiaire (nom, classe, établissement...)">
        <button type="submit" aria-label="Rechercher">🔍</button>
    </form>

    <?php if ($recherche !== ''): ?>
        <p class="info-acces">
            <?= count($rows) ?> résultat(s) pour « <?= htmlspecialchars($recherche) ?> »
            — <a href="fiche_stagiaire.php">réinitialiser</a>
        </p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Classe</th>
            <th>Établissement</th>
            <th>Prof référent</th>
            <th>Contact prof référent</th>
            <th>Note /20</th>
            <th>Appréciation</th>
            <th>Badges</th>
            <th></th>
        </tr>

        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="10" style="text-align:center; color:#888;">Aucun stagiaire trouvé.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['nom']) ?></td>
                <td><?= htmlspecialchars($r['prenom'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['classe'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['etablissement'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['prof_referent_nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['prof_referent_contact'] ?? '') ?></td>
                <td><strong><?= htmlspecialchars($r['note20'] ?? '—') ?></strong></td>
                <td style="max-width:220px;">
                    <?php
                        $appr = $r['appreciation'] ?? '';
                        echo $appr !== ''
                            ? htmlspecialchars(mb_strlen($appr) > 80 ? mb_substr($appr, 0, 80) . '…' : $appr)
                            : '—';
                    ?>
                </td>
                <td><?= $r['badges_list'] ? htmlspecialchars($r['badges_list']) : '—' ?></td>
                <td><a href="fiche_stagiaire_detail.php?id=<?= urlencode($r['id']) ?>">Voir la fiche</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
