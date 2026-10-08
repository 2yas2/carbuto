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

$titrePage    = 'Carbuto - Statistiques';
$pageCourante = 'stats';

require_once __DIR__ . '/include/header.inc.php';
require_once __DIR__ . '/include/functions.inc.php';

$classement= tableauClassementVille();
$maxOccurrences = empty($classement) ? 0 : max($classement);
$totalConsult = empty($classement) ? 0 : array_sum($classement);
$nbVillesDistinctes = count($classement);

$cpVilleTop = '';
$nomVilleTop = '';
if (!empty($classement)) {
    $cpVilleTop = (string) array_key_first($classement);
    $infosTop = infosParCCI($cpVilleTop);
    $nomVilleTop = $infosTop['nomVille'] ?? 'Inconnue';
}
?>
<main>
    <section class="page-simple">
        <article>
            <h2>Statistiques d'utilisation</h2>
            <?php if (empty($classement)): ?>
                <p>Aucune consultation enregistrée pour le moment.</p>
            <?php else: ?>
                <ul class="resume-stats">
                    <li class="bloc-stat">
                        <span class="stat-valeur"><?php echo (int) $totalConsult; ?></span>
                        <span class="stat-libelle">consultations totales</span>
                    </li>
                    <li class="bloc-stat">
                        <span class="stat-valeur"><?php echo (int) $nbVillesDistinctes; ?></span>
                        <span class="stat-libelle">villes différentes</span>
                    </li>
                    <li class="bloc-stat">
                        <span class="stat-valeur"><?php echo htmlspecialchars($nomVilleTop, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="stat-libelle">ville la plus consultée</span>
                    </li>
                </ul>
            <?php endif; ?>
        </article>

        <article>
            <h2>Histogramme des consultations</h2>
            <?php if (empty($classement)): ?>
                <p>Aucune donnée à afficher.</p>
            <?php else: ?>
                <ul class="histogramme">
                    <?php foreach ($classement as $cp => $nombre):
                        $infosVille = infosParCCI((string) $cp);
                        $nomVille = $infosVille['nomVille'] ?? 'Inconnue';
                        $largeur= ($maxOccurrences > 0) ? (int) round(($nombre / $maxOccurrences) * 100) : 0;
                        if ($largeur < 1)   { $largeur = 1; }
                        if ($largeur > 100) { $largeur = 100; }
                    ?>
                    <li class="ligne-histo">
                        <span class="histo-label"><?php echo htmlspecialchars($nomVille . ' (' . $cp . ')', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="histo-zone">
                            <span class="histo-barre" style="width: <?php echo (int) $largeur; ?>%;"></span>
                        </span>
                        <span class="histo-valeur"><?php echo (int) $nombre; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>

        <article>
            <h2>Classement détaillé</h2>
            <?php if (empty($classement)): ?>
                <p>Aucune ville trouvée pour le moment.</p>
            <?php else: ?>
                <?php echo classementParVille(); ?>
            <?php endif; ?>
        </article>
    </section>
</main>

<?php require_once __DIR__ . '/include/footer.inc.php';?>
