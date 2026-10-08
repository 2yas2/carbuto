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

$titrePage = 'Carbuto - Développeur';
$pageCourante = 'tech';

require_once __DIR__ . '/include/functions.inc.php';

$env = @parse_ini_file(__DIR__ . '/.env') ?: [];
$cleIp2Location = $env['IP2LOCATION_KEY'] ?? '';

$films = [];
$url = "https://ghibliapi.vercel.app/films";
$json = @file_get_contents($url);

if ($json !== false && $json !== '') {
    $donnees = json_decode($json, true);
    if (is_array($donnees)) {
        $films = $donnees;
    }
}

if (empty($films)) {
    $fichierLocal = __DIR__ . '/ressources/films.json';
    if (file_exists($fichierLocal)) {
        $contenu = @file_get_contents($fichierLocal);
        if ($contenu !== false) {
            $donnees = json_decode($contenu, true);
            if (is_array($donnees)) {
                $films = $donnees;
            }
        }
    }
}

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $ip = $_SERVER["REMOTE_ADDR"] ?? '';
}

$infos = [];
$geolocDispo = ($ip !== '' && !empty($cleIp2Location));
if ($geolocDispo) {
    $api = "https://api.ip2location.io/?ip=" . urlencode($ip) . "&key=" . urlencode($cleIp2Location);
    $jsonIP = @file_get_contents($api);
    if ($jsonIP !== false && $jsonIP !== '') {
        $decoded = json_decode($jsonIP, true);
        if (is_array($decoded)) {
            $infos = $decoded;
        }
    }
}

$city_name = $infos['city_name'] ?? "Inconnue";
$region_name = $infos['region_name']?? "Inconnue";
$zipcode = $infos['zip_code'] ?? "Inconnu";
$country_name =$infos['country_name'] ?? "Inconnu";
$country_code = $infos['country_code'] ?? "Inconnu";

require_once __DIR__ . '/include/header.inc.php';

$filmDispo = !empty($films);
$title = '';
$original_title= '';
$release_date= '';
$image= '';
$banniere = '';
$description = '';

if ($filmDispo) {
    $filmNumber = getRandom(count($films) - 1);
    $film = $films[$filmNumber]?? null;
    if (is_array($film)) {
        $title = $film['title']?? '';
        $original_title = $film['original_title'] ?? '';
        $release_date= $film['release_date'] ?? '';
        $image = $film['image']?? '';
        $banniere = $film['movie_banner'] ?? '';
        $description = $film['description']?? '';
    } else {
        $filmDispo = false;
    }
}
?>
<main>
    <section class="page-avec-menu">
        <aside id="menu-lateral">
            <p>Sur cette page</p>
            <ul>
                <li><a href="#film">Film aléatoire</a></li>
                <li><a href="#geo">Géolocalisation</a></li>
            </ul>
        </aside>

        <section class="contenu-page">

            <article class="article-titre" id="film">
                <?php if ($filmDispo): ?>
                    <h2><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <aside lang="ja">
                        <?php echo htmlspecialchars($original_title, ENT_QUOTES, 'UTF-8'); ?>
                    </aside>
                    <p class="date-film">
                        <span class="label-date">Date de sortie :</span>
                        <?php echo htmlspecialchars($release_date, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php else: ?>
                    <h2>Film indisponible</h2>
                    <p>Impossible de récupérer un film pour le moment. Veuillez réessayer plus tard.</p>
                <?php endif; ?>
            </article>

            <?php if ($filmDispo && ($image !== '' || $banniere !== '')): ?>
            <article class="article-images">
                <?php if ($image !== ''): ?>
                <figure class="figure-poster">
                    <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="Affiche du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy"/>
                    <figcaption class="legende">Affiche du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></figcaption>
                </figure>
                <?php endif; ?>
                <?php if ($banniere !== ''): ?>
                <figure class="figure-banniere">
                    <img src="<?php echo htmlspecialchars($banniere, ENT_QUOTES, 'UTF-8'); ?>" alt="Bannière du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy"/>
                    <figcaption class="legende">Bannière du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></figcaption>
                </figure>
                <?php endif; ?>
            </article>
            <?php endif; ?>

            <?php if ($filmDispo && $description !== ''): ?>
            <article>
                <p><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
            <?php endif; ?>

            <article id="geo">
                <h2>Position Géographique</h2>
                <?php if (!$geolocDispo): ?>
                    <p>Service de géolocalisation non disponible (clé API non configurée).</p>
                <?php elseif (empty($infos)): ?>
                    <p>Votre adresse IP : <?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>La géolocalisation n'a pas pu être effectuée pour le moment.</p>
                <?php else: ?>
                <p>Votre adresse IP : <?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?></p>
                <h3>Votre position géographique</h3>
                <p>Ville : <?php echo htmlspecialchars($city_name, ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Code Postal : <?php echo htmlspecialchars($zipcode, ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Région : <?php echo htmlspecialchars($region_name, ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Pays : <?php echo htmlspecialchars($country_name, ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Code Pays : <?php echo htmlspecialchars($country_code, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </article>
        </section>
    </section>
</main>

<?php require_once __DIR__ . '/include/footer.inc.php';?>
