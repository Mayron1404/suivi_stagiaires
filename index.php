<?php
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/config/database.php';

// NOTE : cette page (notation d'une fiche déjà créée) n'est pas encore
// protégée par exiger_acces_creation(). Une protection globale des pages
// métier est prévue en Phase 3 de la revue structurelle.

// Cette page fonctionne uniquement avec un id de fiche déjà créée
// (via "Créer un stagiaire" → creer_stagiaire.php → save_creation.php)

$id  = $_GET['id'] ?? null;
$prenom = '';

if (!$id) {
    header("Location: creer_stagiaire.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM stagiaires_notes WHERE id = :id");
$stmt->execute([':id' => $id]);
$stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stagiaire) {
    header("Location: creer_stagiaire.php");
    exit;
}

$nom    = $stagiaire['nom'];
$prenom = $stagiaire['prenom'] ?? '';
$appreciation = $stagiaire['appreciation'] ?? '';

$jeton_csrf = generer_jeton_csrf();

// Source unique de la liste des compétences (voir includes/competences.php)
$competences = require __DIR__ . '/includes/competences.php';

$page_title = "Notation du stagiaire";
require __DIR__ . '/includes/header.php';
?>

<h1>Notation du stagiaire</h1>

<form action="save.php" method="post" id="form-notation">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($jeton_csrf) ?>">

    <!-- Identifiant caché (présent seulement si la fiche existe déjà en base) -->
    <?php if ($id): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <?php endif; ?>

    <!-- Nom du stagiaire -->
    <label>Nom du stagiaire :</label>
    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom . ($prenom ? ' ' . $prenom : '')); ?>" readonly>

    <?php $numero = 1; ?>
    <?php foreach ($competences as $cle => $libelle): ?>
        <!-- <?= $numero ?>. <?= htmlspecialchars($libelle) ?> -->
        <div class="bloc-competence">
            <h3><?= $numero ?>. <?= htmlspecialchars($libelle) ?></h3>
            <div class="rating" data-name="<?= htmlspecialchars($cle) ?>">
                <?php for ($etoile = 1; $etoile <= 5; $etoile++): ?>
                    <span data-value="<?= $etoile ?>">★</span>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="<?= htmlspecialchars($cle) ?>" value="<?= htmlspecialchars($stagiaire[$cle] ?? 0) ?>">
        </div>
        <?php $numero++; ?>
    <?php endforeach; ?>

    <!-- Note finale -->
    <div class="bloc-note-final">
        <label>Note finale sur 20 :</label>
        <input type="text" name="note20" id="note20" value="<?= htmlspecialchars($stagiaire['note20'] ?? '') ?>" readonly>
    </div>

    <!-- Appréciation globale : suggérée automatiquement selon la note, puis modifiable -->
    <div class="bloc-appreciation">
        <label for="appreciation">Appréciation globale (suggestion automatique, à adapter librement) :</label>
        <textarea name="appreciation" id="appreciation" rows="4" placeholder="Cliquez sur « Calculer la note » pour générer une suggestion."><?= htmlspecialchars($appreciation) ?></textarea>
    </div>

    <button type="button" id="btn-calcul">Calculer la note</button>
    <button type="submit">Enregistrer</button>

</form>

<p><a href="liste.php">Voir les stagiaires notés</a></p>

<script src="assets/script.js"></script>
<script>
// Génère une suggestion d'appréciation globale dès que la note est calculée
// (s'ajoute au script existant sans le modifier)
document.getElementById('btn-calcul').addEventListener('click', function () {
    // Petit délai pour être sûr que #note20 a bien été mis à jour par script.js
    setTimeout(function () {
        var champNote = document.getElementById('note20');
        var champAppreciation = document.getElementById('appreciation');
        if (!champNote || !champAppreciation) return;

        var note = parseFloat(champNote.value.toString().replace(',', '.'));
        if (isNaN(note)) return;

        var suggestion = '';
        if (note >= 16) {
            suggestion = "Stage très réussi. Le stagiaire a fait preuve d'un excellent investissement, d'une grande autonomie et d'un professionnalisme remarquable tout au long de la période.";
        } else if (note >= 14) {
            suggestion = "Très bon stage. Le stagiaire a montré de réelles compétences et un bon comportement professionnel, avec quelques axes de progression mineurs à consolider.";
        } else if (note >= 12) {
            suggestion = "Bon stage. Le stagiaire a globalement répondu aux attentes, avec un comportement satisfaisant et des compétences encore en cours de consolidation.";
        } else if (note >= 10) {
            suggestion = "Stage satisfaisant. Le niveau attendu est atteint sur l'essentiel des compétences, mais certains points méritent d'être retravaillés.";
        } else if (note >= 8) {
            suggestion = "Stage en difficulté. Plusieurs compétences restent insuffisamment acquises ; un accompagnement renforcé serait bénéfique pour la suite du parcours.";
        } else {
            suggestion = "Stage très insuffisant. De nombreuses compétences ne sont pas acquises ; une remise en question du positionnement ou du projet professionnel est recommandée.";
        }

        // Ne pas écraser silencieusement une appréciation déjà rédigée ou
        // adaptée par le maître de stage.
        var dejaRedigee = champAppreciation.value.trim() !== '';
        if (!dejaRedigee || confirm("Une appréciation est déjà renseignée dans ce champ.\nVoulez-vous la remplacer par la suggestion automatique ?")) {
            champAppreciation.value = suggestion;
        }
    }, 50);
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
