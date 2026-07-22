<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Code d'accès réservé au personnel informatique
define('CODE_ACCES', '140407');

// Pages autorisées à être protégées par ce code (sécurité : on n'autorise pas une redirection libre)
$pages_autorisees = ['creer_stagiaire.php'];

// Page vers laquelle rediriger une fois le code validé
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? 'creer_stagiaire.php';
if (!in_array($redirect, $pages_autorisees, true)) {
    $redirect = 'creer_stagiaire.php';
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';

    if ($code === CODE_ACCES) {
        $_SESSION['acces_creation'] = true;
        header('Location: ' . $redirect);
        exit;
    } else {
        $erreur = "Code incorrect. Cette section est réservée au personnel informatique.";
    }
}

$page_title = "Accès restreint";
require __DIR__ . '/includes/header.php';
?>

<div class="container">

    <h1>Code d'accès requis</h1>
    <p class="info-acces">Cette section est réservée au personnel informatique.</p>

    <?php if ($erreur): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="post" class="form-code">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <label for="code">Code d'accès :</label>
        <input type="password" id="code" name="code" required placeholder="••••••" autofocus>
        <button type="submit" class="btn-primary">Valider</button>
    </form>

    <p class="retour"><a href="home.php">← Retour à l'accueil</a></p>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
