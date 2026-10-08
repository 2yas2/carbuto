<?php

declare(strict_types=1);

$titrePage    = 'Carbuto - Accueil';
$pageCourante = 'accueil';

require_once __DIR__ . '/include/header.inc.php';
?>

<main>
    <section class="page-simple">
        <article>
            <h2>Bienvenue sur Carbuto</h2>
            <p>
                <strong>Carbuto</strong> est un site web permettant de consulter en temps réel
                les prix des carburants dans les stations-service de France métropolitaine.
            </p>
            <p>
                Sélectionnez votre région, votre département puis votre ville pour afficher
                la liste des stations à proximité et leurs tarifs actuels.
            </p>
        </article>

        <article>
            <h3>Fonctionnalités disponibles</h3>
            <ul>
                <li>Recherche de stations par région, département et commune</li>
                <li>Affichage des prix en temps réel (SP95, SP98, Gazole, E10, GPL…)</li>
                <li>Statistiques des villes les plus consultées</li>
                <li>Mémorisation de votre dernière recherche</li>
                <li>Mode jour / mode nuit</li>
            </ul>
        </article>

        <article>
            <h3>À propos du projet</h3>
            <p>
                Ce projet a été réalisé dans le cadre de l'UE Développement Web (L2 Informatique - S4, 2025-2026).
                Il utilise les APIs REST publiques du gouvernement français ainsi que des services de géolocalisation IP.
            </p>
            <p>
                <strong>Auteurs :</strong> Yassine AIT TALB &amp; Mariam TRAORE
            </p>
        </article>
    </section>
</main>

<?php require_once __DIR__ . '/include/footer.inc.php'; ?>
