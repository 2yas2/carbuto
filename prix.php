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

require_once __DIR__ . '/include/functions.inc.php';

$env = @parse_ini_file(__DIR__ . '/.env') ?: [];
$cleIp2Location = $env['IP2LOCATION_KEY'] ?? '';

$regionSelectionnee = isset($_GET['region']) ? $_GET['region'] : null;
$departementChoisi = isset($_GET['departement']) ? $_GET['departement'] : null;
$villeChoisie = isset($_GET['ville']) ? $_GET['ville'] : null;
$toutDepartement = isset($_GET['tout_departement']) && $_GET['tout_departement'] == '1';
$estGeolocalise = isset($_GET['geoloc']) && $_GET['geoloc'] == '1';


$codePostalChoisi = null;
$nomVilleChoisie = null;

if ($villeChoisie && strpos($villeChoisie, '|') !== false) {
    $parts = explode('|', $villeChoisie);
    $codePostalChoisi = $parts[0];
    $nomVilleChoisie = $parts[1];
} else {
    $codePostalChoisi = $villeChoisie;
    $nomVilleChoisie = $villeChoisie;
}

$infosGeo = [];
$villeGeo = null;
$codePostalGeo = null;

if ($estGeolocalise && !empty($cleIp2Location)) {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"] ?? '';
    }

    $infosGeo = geolocaliserParIp($ip, $cleIp2Location);
    if (!empty($infosGeo)) {
        $villeGeo = $infosGeo['city_name'] ?? null;
        $codePostalGeo = $infosGeo['zip_code'] ?? null;

        if ($codePostalGeo) {
            $villeChoisie = $codePostalGeo . '|' . $villeGeo;
            $codePostalChoisi = $codePostalGeo;
            $nomVilleChoisie = $villeGeo;
            $toutDepartement = false;
            $departementChoisi = null;
        }
    }
}

$villesDuDepartement = [];
$departementsDeLaRegion = [];

if ($regionSelectionnee !== null) {
    $departementsDeLaRegion = filtreDeptsParRegion("ressources/departements.csv", 1, $regionSelectionnee);
}
if ($departementChoisi !== null) {
    $villesDuDepartement = filtreVillesParDepts("ressources/villes.csv", $departementChoisi);
}

$urlApiCarburant = construireUrlApiCarburant($codePostalChoisi, $departementChoisi, $toutDepartement);
$resultatsCarburant = chercherStationsCarburant($urlApiCarburant);
$stations= $resultatsCarburant['stations'];
$nbStations = $resultatsCarburant['nombre'];
$erreurAPI= $resultatsCarburant['erreur'];


$cheminCookie = "/";
$recherchesVilles = '';
if($codePostalChoisi && $nomVilleChoisie && !$toutDepartement) {
    $recherchesVilles = $codePostalChoisi . '|' . $nomVilleChoisie;
    setcookie("recherches_villes", $recherchesVilles, time() + (3600 * 24 * 30), $cheminCookie);
}
elseif (isset($_COOKIE['recherches_villes'])) {
    $recherchesVilles = $_COOKIE['recherches_villes'];
}

$carburantsXML = chargerDonneesXML(__DIR__ . '/ressources/carburants_info.xml');

$titrePage = 'Carbuto - Prix des carburants';
$pageCourante= 'prix';
require_once __DIR__ . '/include/header.inc.php';

?>
<main>
    <section class="page-avec-menu">
        <section class="contenu-page">
            <article class="article-titre">
                <h2> Cliquez sur votre région </h2>
                <figure class="figure-carte">
                    <img src="ressources/carte_regions.jpg" alt="Carte des régions de France" usemap="#carteRegions" loading="lazy"/>
                    <map name="carteRegions" id="carteRegions">
                        <area alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="prix.php?region=<?= urlencode('Nouvelle-Aquitaine') ?>" coords="194,506,197,495,201,488,204,478,204,468,199,462,199,451,200,441,211,437,221,435,234,433,248,423,253,412,261,402,265,394,270,387,275,378,284,375,291,378,297,378,305,374,310,368,312,362,315,355,319,350,319,339,317,330,322,324,323,315,323,308,318,302,313,296,307,295,299,294,290,297,281,295,270,294,263,287,254,279,251,269,243,260,233,261,222,249,211,253,186,256,189,269,193,278,194,286,193,296,183,297,176,296,170,299,171,314,168,326,174,338,179,341,186,353,187,364,192,374,183,373,181,364,178,356,173,349,168,355,164,369,162,380,160,411,152,443,143,465,137,475,151,490,184,508" shape="poly"/>
                        <area alt="Occitanie" title="Occitanie" href="prix.php?region=<?= urlencode('Occitanie') ?>" coords="279,380,274,394,259,407,254,421,246,437,218,443,207,447,204,458,210,471,210,486,203,494,198,502,202,512,213,516,220,518,230,519,239,513,250,514,274,523,288,528,311,537,331,538,346,534,345,514,349,500,357,490,366,486,391,470,407,467,414,458,417,450,421,437,413,426,394,424,384,407,372,390,381,397,356,387,346,398,334,387,324,400,313,405,302,383" shape="poly"/>
                        <area alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="prix.php?region=<?= urlencode("Provence-Alpes-Côte d'Azur") ?>" coords="420,426,433,422,453,430,464,423,456,413,479,387,489,388,487,374,499,373,508,381,518,388,514,406,518,419,530,428,549,427,545,440,542,449,527,459,514,472,508,479,502,487,490,493,474,492,459,488,448,481,431,476,411,475,403,472,415,464,420,449,428,441" shape="poly"/>
                        <area alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhône-Alpes" href="prix.php?region=<?= urlencode('Auvergne-Rhône-Alpes') ?>" coords="318,292,328,284,335,278,347,273,361,277,372,276,375,290,384,290,383,305,400,310,404,302,414,305,421,308,429,288,436,288,447,302,459,300,474,297,470,313,481,307,487,296,496,294,502,294,504,301,506,307,511,316,507,327,513,339,523,351,520,358,513,364,501,367,487,369,484,380,474,387,455,408,457,425,436,420,423,421,414,420,402,419,396,419,382,394,361,382,345,393,335,383,317,398,306,383,319,359,326,347,322,330,330,320" shape="poly"/>
                        <area alt="Corse" title="Corse" href="prix.php?region=<?= urlencode('Corse') ?>" coords="577,458,574,476,547,489,544,500,562,549,576,555,585,523,589,501" shape="poly"/>
                        <area alt="Bretagne" title="Bretagne" href="prix.php?region=<?= urlencode('Bretagne') ?>" coords="30,149,99,133,116,153,160,152,177,163,179,190,167,202,122,221,43,187,31,177" shape="poly"/>
                        <area alt="Pays de la Loire" title="Pays de la Loire" href="prix.php?region=<?= urlencode('Pays de la Loire') ?>" coords="182,163,180,176,180,192,169,207,119,225,127,234,134,247,140,271,147,283,164,295,190,294,193,277,184,257,218,247,229,221,246,214,256,199,256,188,239,176,234,167,220,170,214,160,199,165" shape="poly"/>
                        <area alt="Normandie" title="Normandie" href="prix.php?region=<?= urlencode('Normandie') ?>" coords="150,86,174,89,178,109,215,116,235,106,233,92,282,72,294,83,292,97,295,122,283,132,277,146,261,152,262,167,254,175,252,182,236,167,222,168,214,158,197,162,183,160,172,156,164,149,163,131,162,117,154,108" shape="poly"/>
                        <area alt="Île-de-France" title="Île-de-France" href="prix.php?region=<?= urlencode('Île-de-France') ?>" coords="295,127,286,136,290,144,291,155,299,167,302,176,314,177,322,178,326,183,331,189,345,183,348,175,358,172,362,163,362,155,362,147,357,139,351,133,326,131,316,125" shape="poly"/>
                        <area alt="Centre-Val de Loire" title="Centre-Val de Loire" href="prix.php?region=<?= urlencode('Centre-Val de Loire') ?>" coords="285,140,279,150,264,153,264,167,257,177,257,186,257,201,238,220,230,226,224,245,233,254,243,258,251,262,259,278,270,291,283,294,291,293,313,293,321,285,331,279,345,271,348,254,341,239,340,227,339,215,345,210,350,199,345,191,326,191,317,177,307,182,296,169,287,155" shape="poly"/>
                        <area alt="Hauts-de-France" title="Hauts-de-France" href="prix.php?region=<?= urlencode('Hauts-de-France') ?>" coords="328,10,290,22,289,57,283,69,296,81,294,102,297,116,305,123,316,123,336,130,349,128,365,144,372,138,372,119,383,112,387,96,394,80,390,70,390,57,375,56,366,46" shape="poly"/>
                        <area alt="Grand Est" title="Grand Est" href="prix.php?region=<?= urlencode('Grand Est') ?>" coords="416,65,409,74,395,77,394,91,388,101,387,115,376,120,375,139,367,148,366,161,365,168,373,184,385,199,410,199,416,197,422,203,427,213,434,216,444,216,455,211,463,198,473,193,478,198,491,198,510,209,517,222,523,226,532,217,533,194,553,132,496,123,480,104,446,105,420,83" shape="poly"/>
                        <area alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="prix.php?region=<?= urlencode('Bourgogne-Franche-Comté') ?>" coords="511,222,505,208,490,203,473,197,459,209,445,220,433,219,422,214,417,202,385,204,364,177,349,178,348,188,351,198,348,209,346,223,344,239,350,254,350,270,367,273,380,284,389,292,388,303,398,307,404,299,416,298,423,289,438,285,449,297,468,296,492,259,512,236" shape="poly"/>
                    </map>
                    <figcaption class="legende"> Carte des régions de France </figcaption>
                </figure>
            </article>

            <article id="geoloc">
                <h2>Stations près de chez vous</h2>
                <p>Cliquez sur le bouton ci-dessous pour trouver les stations-service à proximité de votre position</p>
                
                <form method="GET" action="prix.php#liste-stations">
                    <input type="hidden" name="geoloc" value="1"/>
                    <button type="submit">Trouver les stations près de moi</button>
                </form>
            </article>
            <?php if ($regionSelectionnee): ?>
            <article id="liste-departements">
                <h2>Départements de la région : <?php echo htmlspecialchars($regionSelectionnee); ?></h2>
                
                <?php if (!empty($departementsDeLaRegion)): ?>
                <form method="GET" action="prix.php#liste-villes" id="form-departement">
                    <input type="hidden" name="region" value="<?= htmlspecialchars($regionSelectionnee) ?>"/>
                    <?php if ($villeChoisie): ?>
                        <input type="hidden" name="ville" value="<?= htmlspecialchars($villeChoisie) ?>"/>
                    <?php endif; ?>
                    
                    <label for="departement">Choisissez un département :</label>
                    <select name="departement" id="departement" required="required">
                        <option value=""> Sélectionnez </option>
                        <?php foreach ($departementsDeLaRegion as $dept): ?>
                            <?php
                                $codeDept = $dept[0];
                                $nomDept= $dept[6];
                            ?>
                            <option value="<?= htmlspecialchars($codeDept) ?>"
                                <?= ($departementChoisi == $codeDept) ? 'selected="selected"' : '' ?>>
                                <?= htmlspecialchars($codeDept) ?> - <?= htmlspecialchars($nomDept) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Valider</button>
                </form>
                <?php endif; ?>
            </article>
            <?php endif; ?>

            <?php if ($departementChoisi): ?>
            <article id="liste-villes">
                <h2>Villes du département : <?php echo htmlspecialchars($departementChoisi ?? ''); ?></h2>
                
                <section class="actions-departement">
                    <form method="GET" action="prix.php#liste-stations">
                        <input type="hidden" name="region" value="<?= htmlspecialchars($regionSelectionnee ?? '') ?>"/>
                        <input type="hidden" name="departement" value="<?= htmlspecialchars($departementChoisi) ?>"/>
                        <input type="hidden" name="tout_departement" value="1"/>
                        <button type="submit">Voir toutes les stations du département</button>
                    </form>
                </section>

                <?php if (!empty($villesDuDepartement)): ?>
                <form method="GET" action="prix.php#liste-stations" id="form-ville">
                    <input type="hidden" name="region" value="<?= htmlspecialchars($regionSelectionnee ?? '') ?>"/>
                    <input type="hidden" name="departement" value="<?= htmlspecialchars($departementChoisi) ?>"/>
                    
                    <label for="ville">Choisissez une ville :</label>
                    <select name="ville" id="ville" required="required">
                        <option value=""> Sélectionnez </option>
                        <?php
                        $vues = [];
                        foreach ($villesDuDepartement as $ville):
                            $codePostal = $ville[2];
                            $nomVille= $ville[1];

                            if (!in_array($nomVille, $vues)):
                                $vues[] = $nomVille;
                        ?>
                            <option value="<?= htmlspecialchars($codePostal . '|' . $nomVille) ?>"
                                <?= ($villeChoisie == ($codePostal . '|' . $nomVille)) ? 'selected="selected"' : '' ?>>
                                <?= htmlspecialchars($nomVille) ?>
                            </option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                    <button type="submit">Voir les prix</button>
                </form>
                <?php else: ?>
                    <p>Aucune ville trouvée pour ce département.</p>
                <?php endif; ?>
            </article>
            <?php endif; ?> 
            
            <?php if (($villeChoisie && !$toutDepartement) || ($departementChoisi && $toutDepartement)):
                if ($villeChoisie && !$toutDepartement) {
                    enregistrerVilleCSV($nomVilleChoisie, $codePostalChoisi, date('Y-m-d H:i:s'));
                }
            ?>
            <article id="liste-stations">
                <?php if ($toutDepartement): ?>
                    <h2>Stations du département <?= htmlspecialchars($departementChoisi) ?></h2>

                <?php else: ?>
                    <h2>Stations autour de <?= htmlspecialchars($nomVilleChoisie) ?></h2>
                <?php endif; ?>

                <?php if ($erreurAPI): ?>
                    <p class="erreur"><?= htmlspecialchars($erreurAPI) ?></p>
                <?php elseif ($nbStations > 0): ?>

                    <section class="stations-grid">
                        <?php foreach ($stations as $station): 
                            $nom = $station['id'];
                            $adresse = $station['adresse'] ?? '';
                            $villeStation = $station['ville'] ?? '';
                            $prixJson = $station['prix'] ?? null;
                            $carburants = parserPrix($prixJson);
                        ?>
                        <section class="station-card">
                            <h3>ID de la station : <?= htmlspecialchars($nom) ?></h3>
                            <p class="adresse">
                                <?= htmlspecialchars($adresse) ?><br/>
                                <?= htmlspecialchars($villeStation) ?>
                            </p>
                            
                            <?php if (!empty($carburants)): ?>
                                <section class="carburants">
                                    <?php foreach ($carburants as $c): ?>
                                        <span class="prix-badge">
                                            <?= htmlspecialchars($c['nom']) ?> : 
                                            <?= htmlspecialchars($c['prix']) ?> €
                                        </span>
                                    <?php endforeach; ?>
                                    </section>
                            <?php else: ?>
                                <p class="aucun-prix">Aucun prix disponible</p>
                            <?php endif; ?>
                            </section>
                        <?php endforeach; ?>
                            </section>

                <?php else: ?>
                    <p>Aucune station trouvée pour cette ville.</p>
                <?php endif; ?>

            </article>
            <?php endif; ?>

            <?php if (!empty($carburantsXML)): ?>
            <article id="reperes-carburants">
                <h2>Repères sur les carburants</h2>
                <ul class="liste-carburants">
                    <?php foreach ($carburantsXML as $c):
                        $nom = $c['nom'] ?? '';
                        $type = $c['type'] ?? '';
                        $description = $c['description'] ?? '';
                        $prix = $c['prix_moyen'] ?? '';
                        $octane= $c['indice_octane'] ?? '';
                    ?>
                    <li class="carte-carburant">
                        <h3><?php echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <?php if ($type !== ''): ?>
                            <p class="type-carburant"><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <?php if ($description !== ''): ?>
                            <p><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <ul class="infos-carburant">
                            <?php if ($prix !== ''): ?>
                                <li>Prix moyen indicatif : <strong><?php echo htmlspecialchars($prix, ENT_QUOTES, 'UTF-8'); ?> €/L</strong></li>
                            <?php endif; ?>
                            <?php if ($octane !== ''): ?>
                                <li>Indice d'octane : <strong><?php echo htmlspecialchars($octane, ENT_QUOTES, 'UTF-8'); ?></strong></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endif; ?>
        </section>
    </section>
</main>
<?php require_once __DIR__ . '/include/footer.inc.php';?>