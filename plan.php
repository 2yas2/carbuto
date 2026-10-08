<?php
$modeDebug = false;
if ($modeDebug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

$titrePage    = 'Carbuto - Plan du site';
$pageCourante = 'plan';

require_once __DIR__ . '/include/header.inc.php';
?>
<main>
    <section class="page-simple">
        <article>
            <h2>Plan du site</h2>
            <p>Liste des pages disponibles sur Carbuto.</p>
            <ul>
                <li><a href="index.php?style=<?php echo urlencode($style); ?>">Accueil</a> - Présentation du site</li>
                <li><a href="tech.php?style=<?php echo urlencode($style); ?>">Développeur</a> - Page film aléatoire et géolocalisation</li>
                <li><a href="prix.php?style=<?php echo urlencode($style); ?>">Prix carburant</a> - Recherche de stations par région, département ou ville</li>
                <li><a href="stats.php?style=<?php echo urlencode($style); ?>">Statistiques</a> - Classement des villes les plus consultées</li>
                <li><a href="plan.php?style=<?php echo urlencode($style); ?>">Plan du site</a> - Page actuelle</li>
            </ul>
        </article>
    </section>
</main>

<?php require_once __DIR__ . '/include/footer.inc.php';?>
