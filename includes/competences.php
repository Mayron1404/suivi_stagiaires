<?php
/**
 * includes/competences.php
 * Source UNIQUE de la liste des onze compétences notées.
 *
 * CORRECTION P2 : avant cette correction, la même liste de onze compétences
 * était recopiée manuellement dans index.php (formulaire), save.php
 * (traitement), fiche_stagiaire_detail.php et graphique.php (affichage).
 * Ajouter, renommer ou réordonner une compétence n'importe désormais qu'ici.
 *
 * Usage :
 *   $competences = require __DIR__ . '/includes/competences.php';
 *   // $competences['travail_equipe'] === 'Travail en équipe'
 */

return [
    'travail_equipe' => 'Travail en équipe',
    'communication'  => 'Communication professionnelle',
    'ecoute'         => 'Écoute active',
    'telephone'      => 'Téléphone : prise de notes & transmission',
    'gestion_temps'  => 'Gestion du temps',
    'autonomie'      => 'Autonomie progressive',
    'ponctualite'    => 'Ponctualité',
    'politesse'      => 'Politesse',
    'apprentissage'  => "Capacité d'apprentissage rapide",
    'curiosite'      => 'Curiosité professionnelle',
    'montee_comp'    => 'Montée en compétences',
];
