<?php
require_once __DIR__ . '/config/database.php';

// Source unique de la liste des compétences (voir includes/competences.php)
$competences = require __DIR__ . '/includes/competences.php';

// Récupération de tous les stagiaires notés (note20 renseignée)
$stmt = $pdo->query("SELECT * FROM stagiaires_notes WHERE note20 IS NOT NULL ORDER BY nom ASC, prenom ASC");
$stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalNotes = count($stagiaires);
$noteMoyenne = null;

// --- Graphique 1 : Stagiaires (%) — note /20 de chaque stagiaire, ramenée en % ---
$labelsStagiaires = [];
$valeursStagiaires = [];

if ($totalNotes > 0) {
    $sommeNotes = 0;
    foreach ($stagiaires as $s) {
        $nomComplet = trim($s['nom'] . ' ' . ($s['prenom'] ?? ''));
        $labelsStagiaires[] = $nomComplet;
        $pourcentage = round(((float)$s['note20'] / 20) * 100, 1);
        $valeursStagiaires[] = $pourcentage;
        $sommeNotes += (float)$s['note20'];
    }
    $noteMoyenne = round($sommeNotes / $totalNotes, 2);

    // Barre "Ensemble" = moyenne générale en %
    $labelsStagiaires[] = 'Ensemble';
    $valeursStagiaires[] = round(($noteMoyenne / 20) * 100, 1);
}

// --- Graphique 2 : moyenne par compétence (nombre d'étoiles /5), toutes fiches notées confondues ---
$labelsCompetences = [];
$valeursCompetences = [];

foreach ($competences as $col => $label) {
    $labelsCompetences[] = $label;
    if ($totalNotes > 0) {
        $somme = 0;
        foreach ($stagiaires as $s) {
            $somme += (float)($s[$col] ?? 0);
        }
        $valeursCompetences[] = round($somme / $totalNotes, 2);
    } else {
        $valeursCompetences[] = 0;
    }
}

// --- Graphique 3 : répartition des notes /20 par tranche ---
$tranches = [
    '0-8'   => 0,
    '8-10'  => 0,
    '10-12' => 0,
    '12-14' => 0,
    '14-16' => 0,
    '16-20' => 0,
];

foreach ($stagiaires as $s) {
    $n = (float)$s['note20'];
    if ($n < 8)       $tranches['0-8']++;
    elseif ($n < 10)  $tranches['8-10']++;
    elseif ($n < 12)  $tranches['10-12']++;
    elseif ($n < 14)  $tranches['12-14']++;
    elseif ($n < 16)  $tranches['14-16']++;
    else              $tranches['16-20']++;
}

$page_title = "Graphique des stagiaires";
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

    <p><a href="home.php">← Retour à l'accueil</a></p>

    <h1>Statistiques globales</h1>

    <?php if ($totalNotes === 0): ?>
        <p style="color:#888;">Aucune fiche notée pour le moment, impossible d'afficher de statistiques.</p>
    <?php else: ?>

        <p class="info-acces">
            <?= $totalNotes ?> stagiaire(s) noté(s) — Note moyenne générale :
            <strong><?= htmlspecialchars((string)$noteMoyenne) ?> / 20</strong>
        </p>

        <div class="fiche-section">
            <h3 style="text-align:center;">Stagiaires (%)</h3>
            <div class="chart-box">
                <canvas id="chartStagiaires"></canvas>
            </div>
        </div>

        <div class="fiche-columns">
            <div class="fiche-section" style="flex:2; min-width:320px;">
                <h3>Moyenne par compétence (nombre d'étoiles /5)</h3>
                <div class="chart-box">
                    <canvas id="chartCompetences"></canvas>
                </div>
            </div>

            <div class="fiche-section" style="flex:1; min-width:260px;">
                <h3>Répartition des notes /20</h3>
                <div class="chart-box">
                    <canvas id="chartRepartition"></canvas>
                </div>
            </div>
        </div>

        <script>
            // Graphique 1 : note (%) de chaque stagiaire
            new Chart(document.getElementById('chartStagiaires'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labelsStagiaires) ?>,
                    datasets: [{
                        label: 'Stagiaires (%)',
                        data: <?= json_encode($valeursStagiaires) ?>,
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

            // Graphique 2 : moyenne par compétence (étoiles /5)
            new Chart(document.getElementById('chartCompetences'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labelsCompetences) ?>,
                    datasets: [{
                        label: "Moyenne d'étoiles (/5)",
                        data: <?= json_encode($valeursCompetences) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, max: 5 }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Graphique 3 : répartition des notes /20
            new Chart(document.getElementById('chartRepartition'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_keys($tranches)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($tranches)) ?>,
                        backgroundColor: [
                            '#e74c3c', '#e67e22', '#f1c40f',
                            '#2ecc71', '#3498db', '#9b59b6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        </script>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
