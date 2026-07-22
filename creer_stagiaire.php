<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

// Accès bloqué tant que le code n'a pas été validé sur code_acces.php
exiger_acces_creation();

$jeton_csrf = generer_jeton_csrf();

$page_title = "Créer un stagiaire";
require __DIR__ . '/includes/header.php';
?>

<div class="container container-large">

<h1>Fiche de renseignements du stagiaire</h1>

<form action="save_creation.php" method="post" class="fiche-stagiaire">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($jeton_csrf) ?>">

    <div class="fiche-columns">

        <div class="fiche-section">
            <h3>Informations personnelles</h3>

            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required placeholder="Ex : Azael">

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required placeholder="Ex : Jean">

            <label for="date_naissance">Date de naissance :</label>
            <input type="date" id="date_naissance" name="date_naissance">

            <label for="classe">Classe :</label>
            <input type="text" id="classe" name="classe" placeholder="Ex : 1ère Bac Pro SN">

            <label for="etablissement">Établissement :</label>
            <input type="text" id="etablissement" name="etablissement" placeholder="Ex : Lycée Jule Garnier">

            <label>Sexe :</label>
            <div class="fiche-radio-group">
                <label class="fiche-radio"><input type="radio" name="sexe" value="F"> Féminin</label>
                <label class="fiche-radio"><input type="radio" name="sexe" value="M"> Masculin</label>
            </div>
        </div>

        <div class="fiche-section">
            <h3>Informations de stage</h3>

            <label for="entreprise">Entreprise / lieu de stage :</label>
            <input type="text" id="entreprise" name="entreprise" placeholder="Ex : Mairie de Charron">

            <label for="tuteur_nom">Nom du maître de stage :</label>
            <input type="text" id="tuteur_nom" name="tuteur_nom" placeholder="Ex : Mme Dupont">

            <label for="tuteur_contact">Contact du maître de stage :</label>
            <input type="text" id="tuteur_contact" name="tuteur_contact" placeholder="Téléphone ou email">
        </div>

        <div class="fiche-section">
            <h3>Prof référent (établissement)</h3>

            <label for="prof_referent_nom">Nom du prof référent :</label>
            <input type="text" id="prof_referent_nom" name="prof_referent_nom" placeholder="Ex : M. Martin">

            <label for="prof_referent_contact">Contact du prof référent :</label>
            <input type="text" id="prof_referent_contact" name="prof_referent_contact" placeholder="Téléphone ou email">
        </div>

    </div>

    <button type="submit">Enregistrer la fiche</button>

</form>

<p class="retour"><a href="home.php">← Retour à l'accueil</a></p>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
