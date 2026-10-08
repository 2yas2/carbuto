<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$style = 'classique';

if (isset($_GET['style']) && $_GET['style'] === 'sombre') {
    $style = 'sombre';
}

$fichierStyle = ($style === 'sombre') ? 'style/style-sombre.css' : 'style/style.css';
$titrePage    = 'Carbuto — Développeur';
$pageCourante = 'tech';

$url = "https://ghibliapi.vercel.app/films";
$json = file_get_contents($url);

if($json === null){
    die("Erreur lors de la récupération du JSON");
}
$films = json_decode($json,true);

if($films===null){
    die("Erreur lors du décodage JSON");
}

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $ip = $_SERVER["REMOTE_ADDR"];
}

$env = @parse_ini_file(__DIR__ . '/.env') ?: [];
$cle = $env['IP2LOCATION_KEY'] ?? '';
$api = "https://api.ip2location.io/?ip=" . $ip . "&key=" . $cle;
$jsonIP = @file_get_contents($api);

if ($jsonIP === false) {
    $infos = [];
} else {
    $infos = json_decode($jsonIP, true);
    if ($infos === null) {
        $infos = [];
    }
}

$city_name = isset($infos['city_name']) ? $infos['city_name'] : "Inconnue";
$region_name = isset($infos['region_name']) ? $infos['region_name'] : "Inconnue";
$zipcode = isset($infos['zip_code']) ? $infos['zip_code'] : "Inconnu";
$country_name = isset($infos['country_name']) ? $infos['country_name'] : "Inconnu";
$country_code = isset($infos['country_code']) ? $infos['country_code'] : "Inconnu";


require_once __DIR__ . '/include/header.inc.php';
require_once __DIR__ . '/include/functions.inc.php';
$filmNumber = getRandom(21);

$title = $films[$filmNumber]['title'];
$original_title = $films[$filmNumber]['original_title'];
$release_date = $films[$filmNumber]['release_date'];





?>
<main>
    <div class="page-avec-menu">

        <aside id="menu-lateral">
            <p>Sur cette page</p>
            <ul>
                <li><a href="#film">Film aléatoire</a></li>
                <li><a href="#geo">Géolocalisation</a></li>
            </ul>
        </aside>

        <div class="contenu-page">
            <section>

                <article class="article-titre">
                    <h2><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <aside>
                        <?php echo htmlspecialchars($original_title, ENT_QUOTES, 'UTF-8'); ?>
                    </aside>
                    <p class="date-film">
                        <span class="label-date">Date de sortie :</span>
                        <?php echo htmlspecialchars($release_date, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </article>

                <article class="article-images">
                    <figure class="figure-poster">
                        <img src="<?php echo htmlspecialchars($films[$filmNumber]['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Affiche du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                        <figcaption class="legende">Affiche du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></figcaption>
                    </figure>
                    <figure class="figure-banniere">
                        <img src="<?php echo htmlspecialchars($films[$filmNumber]['movie_banner'], ENT_QUOTES, 'UTF-8'); ?>" alt="Bannière du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                        <figcaption class="legende">Bannière du film <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></figcaption>
                    </figure>
                </article>

                <article>
                    <p><?php echo htmlspecialchars($films[$filmNumber]['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                </article>

                <article>
                    <h2>Position Géographique</h2>
                    <p>Votre adresse IP : <?php echo $ip; ?></p>
                    <h5>Votre position géographique</h5>
                    <p>Ville : <?php echo $city_name; ?></p>
                    <p>Code Postal :  <?php echo $zipcode; ?></p>
                    <p>Région : <?php echo $region_name; ?></p>
                    <p>Pays : <?php echo $country_name; ?></p>
                    <p>Code Pays : <?php echo $country_code; ?></p>
                </article>

            </section>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/include/footer.inc.php';?>
