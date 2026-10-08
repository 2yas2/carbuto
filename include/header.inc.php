<?php
    $cheminCookie = "/";
    $style = 'classique';
    if(isset($_GET['style'])) {
        $styleChoice = $_GET['style'];
        if ($styleChoice === 'sombre' || $styleChoice === 'classique') {
            $style = $styleChoice;
            setcookie("choix_style", $style, time() + (3600 * 24 * 30), $cheminCookie);
        }
    }
    elseif (isset($_COOKIE['choix_style'])) {
        $styleFromCookie = $_COOKIE['choix_style'];
        if ($styleFromCookie === 'sombre' || $styleFromCookie === 'classique') {
            $style = $styleFromCookie;
        }
    }
    $fichierStyle = ($style === 'sombre') ? 'style/style-sombre.css' : 'style/style.css';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="author" content="Yassine AIT TALB, Mariam TRAORE"/>
    <link rel="icon" href="ressources/favico.ico" type="image/x-icon"/>
    <meta name="description" content="Carbuto - Trouvez les stations-service les moins chères près de chez vous."/>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($fichierStyle, ENT_QUOTES, 'UTF-8'); ?>"/>
    <title><?php echo htmlspecialchars($titrePage ?? 'Carbuto', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>

<header class="entete-page">
    <div class="bloc-entete">
        <a href="index.php?style=<?php echo urlencode($style); ?>" aria-label="Retour à l'accueil Carbuto">
            <img class="logo-entete" src="ressources/logo.svg" alt="Logo Carbuto"/>
        </a>
        <nav class="navigation-principale">
            <ul>
                <li>
                    <a href="index.php?style=<?php echo urlencode($style); ?>"
                       <?php if (($pageCourante ?? '') === 'accueil') echo 'class="actif"'; ?>>
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="tech.php?style=<?php echo urlencode($style); ?>"
                       <?php if (($pageCourante ?? '') === 'tech') echo 'class="actif"'; ?>>
                        Développeur
                    </a>
                </li>
                <li>
                    <a href="prix.php?style=<?php echo urlencode($style); ?>"
                    <?php if (($pageCourante ?? '') === 'prix') echo 'class="actif"'; ?>>
                        Prix carburant
                    </a>
                </li>
                <li>
                    <a href="stats.php?style=<?php echo urlencode($style); ?>"
                       <?php if (($pageCourante ?? '') === 'stats') echo 'class="actif"'; ?>>
                       Statistiques
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
