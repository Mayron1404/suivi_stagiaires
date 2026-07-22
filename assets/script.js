document.querySelectorAll('.rating').forEach(rating => {
    const stars = rating.querySelectorAll('span');
    const input = rating.nextElementSibling;

    // CORRECTION P1 : reprendre visuellement la note déjà enregistrée pour ce
    // stagiaire (au lieu de toujours afficher les étoiles à zéro), pour éviter
    // qu'une réouverture de fiche n'écrase silencieusement l'évaluation.
    const valeurInitiale = parseInt(input.value, 10) || 0;
    stars.forEach((s, i) => {
        if (i < valeurInitiale) {
            s.classList.add('active');
        }
    });

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.dataset.value;
            input.value = value;

            stars.forEach(s => s.classList.remove('active'));
            for (let i = 0; i < value; i++) {
                stars[i].classList.add('active');
            }
        });
    });
});

// Calcul de la note finale sur 20
const btnCalcul = document.getElementById('btn-calcul');

if (btnCalcul) {
    btnCalcul.addEventListener('click', () => {
        const ratings = document.querySelectorAll('.rating');
        let total = 0;
        let max = 0;

        ratings.forEach(rating => {
            const input = rating.nextElementSibling;
            total += parseInt(input.value, 10) || 0;
            max += 5; // chaque compétence est notée sur 5 étoiles
        });

        const note20 = (total / max) * 20;
        document.getElementById('note20').value = note20.toFixed(1);
    });
}
