<?php
require_once __DIR__ . '/config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: fiche_stagiaire.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM stagiaires_notes WHERE id = :id");
$stmt->execute([':id' => $id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$s) {
    header("Location: fiche_stagiaire.php");
    exit;
}

// Badges obtenus par ce stagiaire, avec leur signification
$stmt = $pdo->prepare("
    SELECT b.nom, b.description, b.icone, sb.date_obtention
    FROM stagiaire_badges sb
    JOIN badges b ON b.id = sb.badge_id
    WHERE sb.stagiaire_id = :id
    ORDER BY sb.date_obtention DESC
");
$stmt->execute([':id' => $id]);
$badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Source unique de la liste des compétences (voir includes/competences.php)
$competences = require __DIR__ . '/includes/competences.php';

// Résultats par compétence, convertis en % (note sur 5 étoiles -> %)
$labelsCompetences = [];
$valeursCompetences = [];
foreach ($competences as $col => $label) {
    $labelsCompetences[] = $label;
    $valeursCompetences[] = $s[$col] !== null ? round(((float)$s[$col] / 5) * 100, 1) : 0;
}
// Barre "Ensemble" = note globale sur 20 ramenée en %
$labelsCompetences[] = 'Ensemble';
$valeursCompetences[] = $s['note20'] !== null ? round(((float)$s['note20'] / 20) * 100, 1) : 0;

$page_title = "Fiche de " . trim($s['nom'] . ' ' . ($s['prenom'] ?? ''));
$extra_head = <<<HTML
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<style>
    .chart-box {
        position: relative;
        width: 100%;
        height: 320px;
    }
    .chart-box canvas {
        max-width: 100% !important;
        max-height: 100% !important;
    }
</style>
HTML;
require __DIR__ . '/includes/header.php';
?>

<div class="container container-large">

    <p><a href="fiche_stagiaire.php">← Retour à la liste</a></p>

    <h1><?= htmlspecialchars($s['nom'] . ' ' . ($s['prenom'] ?? '')) ?></h1>

    <div class="fiche-columns">

        <div class="fiche-section">
            <h3>Informations personnelles</h3>
            <p><strong>Classe :</strong> <?= htmlspecialchars($s['classe'] ?? '—') ?></p>
            <p><strong>Établissement :</strong> <?= htmlspecialchars($s['etablissement'] ?? '—') ?></p>
            <p><strong>Date de naissance :</strong> <?= htmlspecialchars($s['date_naissance'] ?? '—') ?></p>
            <p><strong>Sexe :</strong> <?= htmlspecialchars($s['sexe'] ?? '—') ?></p>
            <p><strong>Note /20 :</strong> <?= htmlspecialchars($s['note20'] ?? '—') ?></p>
        </div>

        <div class="fiche-section">
            <h3>Stage & contacts</h3>
            <p><strong>Entreprise :</strong> <?= htmlspecialchars($s['entreprise'] ?? '—') ?></p>
            <p><strong>Maître de stage :</strong> <?= htmlspecialchars($s['tuteur_nom'] ?? '—') ?></p>
            <p><strong>Contact maître de stage :</strong> <?= htmlspecialchars($s['tuteur_contact'] ?? '—') ?></p>
            <p><strong>Prof référent :</strong> <?= htmlspecialchars($s['prof_referent_nom'] ?? '—') ?></p>
            <p><strong>Contact prof référent :</strong> <?= htmlspecialchars($s['prof_referent_contact'] ?? '—') ?></p>
        </div>

    </div>

    <div class="fiche-section">
        <h3>Appréciation globale</h3>
        <p><?= $s['appreciation'] !== null && $s['appreciation'] !== ''
                ? nl2br(htmlspecialchars($s['appreciation']))
                : '<span style="color:#888;">Aucune appréciation enregistrée pour le moment.</span>' ?></p>
    </div>

    <div class="fiche-section">
        <h3 style="text-align:center;">Résultats (%)</h3>
        <?php if ($s['note20'] === null): ?>
            <p style="color:#888;">Ce stagiaire n'a pas encore été noté, aucun graphique disponible.</p>
        <?php else: ?>
            <div class="chart-box">
                <canvas id="chartStagiaire"></canvas>
            </div>
            <script>
                new Chart(document.getElementById('chartStagiaire'), {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labelsCompetences) ?>,
                        datasets: [{
                            label: 'Résultat (%)',
                            data: <?= json_encode($valeursCompetences) ?>,
                            backgroundColor: 'rgba(68, 114, 196, 0.85)',
                            borderColor: 'rgba(68, 114, 196, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { stepSize: 10 }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { boxWidth: 14 }
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>

    <div class="fiche-section">
        <h3>Badges reçus</h3>

        <?php if (empty($badges)): ?>
            <p style="color:#888;">Aucun badge reçu pour le moment.</p>
        <?php else: ?>
            <ul class="badges-list">
                <?php foreach ($badges as $b): ?>
                    <li title="<?= htmlspecialchars($b['description']) ?>">
                        <span style="font-size:1.3em;"><?= htmlspecialchars($b['icone']) ?></span>
                        <strong><?= htmlspecialchars($b['nom']) ?></strong>
                        — <?= htmlspecialchars($b['description']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
