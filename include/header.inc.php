<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="author" content="Yassine AIT TALB, Mariam TRAORE"/>
    <link rel="icon" href="ressources/favico.ico" type="image/x-icon"/>
    <meta name="description" content="Carbuto — Trouvez les stations-service les moins chères près de chez vous."/>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($fichierStyle, ENT_QUOTES, 'UTF-8'); ?>"/>
    <title><?php echo htmlspecialchars($titrePage ?? 'Carbuto', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>

<header class="entete-page">
    <div class="bloc-entete">
        <a href="index.php?style=<?php echo urlencode($style); ?>">
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
                    <a href="#">
                        Prix carburant
                    </a>
                </li>
                <li>
                    <a href="#">
                       Statistiques
                    </a>
                </li>
                <li>
                    <a href="#">
                       Plan du site
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
