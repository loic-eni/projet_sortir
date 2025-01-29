import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

document.addEventListener('DOMContentLoaded', function () {
    // Charger le thème enregistré depuis localStorage (ou 'light' par défaut)
    const savedTheme = localStorage.getItem('theme') || 'light';

    // Appliquer le thème sélectionné à la racine du document
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Sélectionner l'élément <select> du thème
    const themeSelect = document.querySelector('select[name="theme"]');

    // Si l'élément existe, mettre à jour sa valeur en fonction du thème enregistré
    if (themeSelect) {
        themeSelect.value = savedTheme;  // Appliquer le thème dans le menu déroulant

        // Écouter le changement de sélection dans le <select>
        themeSelect.addEventListener('change', function () {
            const selectedTheme = themeSelect.value;
            document.documentElement.setAttribute('data-theme', selectedTheme); // Appliquer le thème
            localStorage.setItem('theme', selectedTheme); // Sauvegarder le thème dans localStorage
        });
    }
});



