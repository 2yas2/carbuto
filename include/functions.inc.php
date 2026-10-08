<?php

/**
 * Donne le chemin du fichier d'historique et le crée s'il n'existe pas.
 *
 * @return string Chemin complet du fichier historique.csv.
 */
function cheminHistorique():string
{
    $fichier = __DIR__ . '/../ressources/historique.csv';
    if (!file_exists($fichier)) {
        $f = fopen($fichier, 'w');
        if ($f) {
            fclose($f);
        }
    }
    return $fichier;
}

/**
 * Tire un nombre au hasard en 0 et un maximum.
 *
 * @param int $number Borne maximale (incluse).
 * @return int Nombre tiré au hasard.
 */
function getRandom(int $number):int
{
    return random_int(0,$number);
}

/**
 * Enlève les espaces autour d'une valeur et la transforme en chaîne.
 *
 * @param int $valeur Valeur reçue.
 * @return string Texte propre.
 */
function nettoyerTexte($valeur):string
{
    if ($valeur === null) {
        return '';
    }
    return trim((string) $valeur);
}

/**
 * Construit l'URL pour interroger l'API des prix des carburants.
 *
 * @param string $codePostal Code postal de la ville.
 * @param string $codeDepartement Code du département.
 * @param bool $toutDepartement Vrai si on veut tout le département.
 * @return string URL à utiliser, ou null si les critères ne sont pas bons.
 */
function construireUrlApiCarburant(?string $codePostal, ?string $codeDepartement, bool $toutDepartement):?string
{
    $base = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records";
    if ($codePostal && !$toutDepartement) {
        return $base . "?where=cp%3D%22" . urlencode($codePostal) . "%22&limit=50";
    }
    if ($codeDepartement && $toutDepartement) {
        return $base . "?where=code_departement%3D%22" . urlencode($codeDepartement) . "%22&limit=100";
    }
    return null;
}

/**
 * Va chercher la liste des stations sur l'API à partir d'une URL.
 *
 * @param string $url Adresse à appeler.
 * @return array Tableau avec les stations, le nombre trouvé et un message d'erreur si besoin.
 */
function chercherStationsCarburant(?string $url):array
{
    $resultat = ['stations' => [], 'nombre' => 0, 'erreur' => null];
    if ($url === null) {
        $resultat['erreur'] = "Aucun critère de recherche valide.";
        return $resultat;
    }
    $reponse = @file_get_contents($url);
    if ($reponse === false || $reponse === '') {
        $resultat['erreur'] = "Impossible de contacter l'API.";
        return $resultat;
    }
    $data = json_decode($reponse, true);
    if (!is_array($data) || !isset($data['total_count'])) {
        $resultat['erreur'] = "Erreur dans les données reçues.";
        return $resultat;
    }
    $resultat['nombre'] = (int) $data['total_count'];
    $resultat['stations'] = isset($data['results']) && is_array($data['results']) ? $data['results'] : [];
    return $resultat;
}

/**
 * Récupère la position approximative d'un visiteur à partir de son IP.
 *
 * @param string $ip Adresse IP du visiteur.
 * @param string $cleApi Clé pour l'API IP2Location.
 * @return array Infos de localisation, ou tableau vide si l'appel échoue.
 */
function geolocaliserParIp(string $ip, string $cleApi):array
{
    if ($ip === '' || $cleApi === '') {
        return [];
    }
    $url = "https://api.ip2location.io/?ip=" . urlencode($ip) . "&key=" . urlencode($cleApi);
    $reponse = @file_get_contents($url);
    if ($reponse === false || $reponse === '') {
        return [];
    }
    $decoded = json_decode($reponse, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Lit un fichier XML local et renvoie la liste des carburants.
 *
 * @param string $cheminLocal Chemin du fichier XML.
 * @return array Liste des carburants trouvés.
 */
function chargerDonneesXML(string $cheminLocal):array
{
    if (!file_exists($cheminLocal)) {
        return [];
    }
    if (!function_exists('simplexml_load_string')) {
        return [];
    }
    $contenu = @file_get_contents($cheminLocal);
    if ($contenu === false || $contenu === '') {
        return [];
    }
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($contenu);
    if ($xml === false) {
        return [];
    }
    $resultats = [];
    foreach ($xml->carburant as $c) {
        $resultats[] = [
            'nom' => (string) $c->nom,
            'type' => (string) $c->type,
            'description' => (string) $c->description,
            'prix_moyen' => (string) $c->prix_moyen,
            'indice_octane' => (string) $c->indice_octane,
        ];
    }
    return $resultats;
}

/**
 * Garde les départements qui appartiennent à une région donnée.
 *
 * @param string $csv Chemin du fichier CSV des départements.
 * @param int $colonne Numéro de la colonne du code région.
 * @param string $region Nom de la région choisie.
 * @return array Lignes correspondant à la région.
 */
function filtreDeptsParRegion(string $csv, int $colonne, string $region){
    $correspondance = chargerCorrespondanceRegion();

    if (!isset($correspondance[$region])) {
        return [];
    }

    $codeRegion = $correspondance[$region];
    $liste = [];
    $monFichier = fopen($csv, "r");

    if ($monFichier !== FALSE) {
        fgetcsv($monFichier, 1000, ",", "\"", "\\");

        while(($ligne = fgetcsv($monFichier, 1000, ",", "\"", "\\")) !== FALSE) {
            if($ligne[$colonne] === $codeRegion) {
                $liste[] = $ligne;
            }
        }
        fclose($monFichier);
    }
    return $liste;
}

/**
 * Charge la liste des régions et associe chaque nom à son code.
 *
 * @return array Tableau nomRegion => codeRegion.
 */
function chargerCorrespondanceRegion() {
    $chemin = __DIR__ . '/../ressources/regions.csv';
    $fichier = fopen($chemin, "r");
    $correspondance = [];

    if ($fichier !== FALSE) {
        fgetcsv($fichier, 1000, ",", "\"", "\\");

        while(($ligne = fgetcsv($fichier, 1000, ",", "\"", "\\")) !== FALSE) {
            $codeRegion = $ligne[0];
            $nomRegion = $ligne[5];
            $correspondance[$nomRegion] = $codeRegion;
        }
        fclose($fichier);
    }
    return $correspondance;
}

/**
 * Garde les villes qui appartiennent à un département donné.
 *
 * @param string $csv Chemin du fichier CSV des villes.
 * @param string $codeDept Code du département (sur 2 caractères).
 * @return array Lignes correspondant au département.
 */
function filtreVillesParDepts(string $csv, string $codeDept){
    $liste = [];
    $monFichier = fopen($csv, "r");
    if ($monFichier !== FALSE) {
        fgetcsv($monFichier, 1000, ";", '"', "\\");

        while(($ligne = fgetcsv($monFichier, 1000, ";", '"', "\\")) !== FALSE){
            if(isset($ligne[0]) && substr($ligne[0], 0, 2) === $codeDept){
                $liste[] = $ligne;
            }
        }
        fclose($monFichier);
    }
    return $liste;
}

/**
 * Transforme le JSON des prix d'une station en tableau plus simple à afficher.
 *
 * @param mixed $prixJson Données JSON renvoyées par l'API pour une station.
 * @return array Liste des carburants avec leur nom et leur prix.
 */
function parserPrix($prixJson) {
    $resultats = [];
    if ($prixJson === null) {
        return $resultats;
    }

    $prix = json_decode($prixJson, true);
    if (isset($prix['@attributes'])) {
        $prix = [$prix];
    }

    if (isset($prix['@nom'])) {
        $prix = [$prix];
    }

    foreach ($prix as $carburant) {
        $nom = $carburant['@nom'] ?? 'Inconnu';
        $valeur = $carburant['@valeur'] ?? null;

        if ($valeur !== null) {
            $resultats[] = [
                'nom' => $nom,
                'prix' => $valeur,
            ];
        }
    }

    return $resultats;
}

/**
 * Ajoute une ligne dans le fichier d'historique.
 *
 * @param string $ville Nom de la ville consultée.
 * @param string $code Code postal de la ville.
 * @param string $date Date et heure de la consultation.
 * @return void
 */
function enregistrerVilleCSV(string $ville, string $code, string $date):void{
    $fichier = cheminHistorique();
    $f = @fopen($fichier, 'a');
    if(!$f){
        error_log("Carbuto : impossible d'ouvrir " . $fichier);
        return;
    }
    flock($f, LOCK_EX);
    fputcsv($f, [$date, $code, $ville]);
    flock($f, LOCK_UN);
    fclose($f);
}

/**
 * Cherche une ville à partir de son code postal dans le CSV des villes.
 *
 * @param string $cp Code postal recherché.
 * @return array Tableau avec le nom et le code postal, ou vide si non trouvé.
 */
function infosParCCI(string $cp):array
{
    $fichier = __DIR__ . '/../ressources/villes.csv';
    $f = fopen($fichier,'r');
    if (!$f){
        return [];
    }
    flock($f,LOCK_SH);
    fgetcsv($f,0,';','"','');
    while(($ligne = fgetcsv($f,0,';','"',''))!==FALSE){
        if(isset($ligne[2]) && $ligne[2]===$cp){
            $resultats = [
                'nomVille' => $ligne[1],
                'codePostal' => $ligne[2],
            ];
            break;
        }
    }
    flock($f,LOCK_UN);
    fclose($f);
    return $resultats ?? [];
}

/**
 * Trouve le département correspondant à un code postal.
 *
 * @param string $cp Code postal de la ville.
 * @return array Tableau avec le nom du département et son code région.
 */
function nomDepParCP(string $cp):array
{
    $fichier = __DIR__ . '/../ressources/departements.csv';
    $f = fopen($fichier,'r');
    if (!$f){
        return [];
    }
    flock($f,LOCK_SH);
    while(($ligne = fgetcsv($f,0,',','"',''))!==FALSE){
        if($ligne[0][0]===$cp[0] && $ligne[0][1]=== $cp[1]){
            $resultats = [
                'nomDepartement' => $ligne[5],
                'codeReg' => $ligne[1],
            ] ;
        }
    }
    flock($f,LOCK_UN);
    fclose($f);
    return $resultats ?? [];
}

/**
 * Donne le nom d'une région à partir de son code.
 *
 * @param string $reg Code région.
 * @return string Nom de la région, ou chaîne vide si non trouvé.
 */
function nomDepParREG(string $reg):string
{
    $fichier = __DIR__ . '/../ressources/regions.csv';
    $f = fopen($fichier,'r');
    if (!$f){
        return '';
    }
    flock($f,LOCK_SH);
    while(($ligne = fgetcsv($f,0,',','"',''))!==FALSE){
        if($ligne[0]===$reg){
            $resultats = $ligne[4];
        }
    }
    flock($f,LOCK_UN);
    fclose($f);
    return $resultats ?? '';
}

/**
 * Compte combien de fois chaque ville a été consultée.
 *
 * @return array Tableau code postal => nombre de consultations, trié du plus consulté au moins consulté.
 */
function tableauClassementVille():array{
    $fichier = cheminHistorique();
    $f = fopen($fichier, 'r');
    if(!$f){
        return [];
    }

    $compteurs = [];
    flock($f,LOCK_SH);
    while(($ligne = fgetcsv($f,0,',','"',''))!==FALSE){
        $cp = $ligne[1];
        $compteurs[$cp] = ($compteurs[$cp] ?? 0) + 1;
    }
    flock($f,LOCK_UN);
    fclose($f);
    arsort($compteurs);

    return $compteurs;
}

/**
 * Construit le tableau HTML du classement des villes.
 *
 * @return string Code HTML du tableau prêt à être affiché.
 */
function classementParVille():string{
    $classement = tableauClassementVille();

    $tableau = "";
    $tableau = $tableau . "<table>\n";
    $tableau = $tableau . "\t<tr>\n";
    $tableau = $tableau . "\t\t<th>Place</th>\n";
    $tableau = $tableau . "\t\t<th>Code Postal</th>\n";
    $tableau = $tableau . "\t\t<th>Ville</th>\n";
    $tableau = $tableau . "\t\t<th>Departement</th>\n";
    $tableau = $tableau . "\t\t<th>Region</th>\n";
    $tableau = $tableau . "\t\t<th>Occurrences</th>\n";
    $tableau = $tableau . "\t</tr>\n";

    $place = 1;
    foreach($classement as $cp => $nombre){
        $infosDept = nomDepParCP($cp);
        $nomDept = $infosDept['nomDepartement'] ?? 'Inconnu';
        $codeReg = $infosDept['codeReg'] ?? '';
        $nomReg = nomDepParREG($codeReg);

        $infosVille = infosParCCI($cp);
        $nomVille = $infosVille['nomVille'] ?? 'Inconnue';

        $tableau = $tableau . "\t<tr>\n";
        $tableau = $tableau . "\t\t<td>" . $place . "</td>\n";
        $tableau = $tableau . "\t\t<td>" . htmlspecialchars($cp, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</td>\n";
        $tableau = $tableau . "\t\t<td>" . htmlspecialchars($nomVille, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</td>\n";
        $tableau = $tableau . "\t\t<td>" . htmlspecialchars($nomDept, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</td>\n";
        $tableau = $tableau . "\t\t<td>" . htmlspecialchars($nomReg, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</td>\n";
        $tableau = $tableau . "\t\t<td>" . $nombre . "</td>\n";
        $tableau = $tableau . "\t</tr>\n";
        $place++;
    }

    $tableau = $tableau . "</table>\n";
    return $tableau;
}
?>