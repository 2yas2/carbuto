<footer>
    <div class="bloc-footer">
        <img class="logo-footer" src="ressources/logo.svg" alt="Logo Carbuto"/>
        <div>
            <p>&#169; 2026 - Yassine AIT TALB &amp; Mariam TRAORE - L2 Informatique</p>
            <p>
                <a href="index.php?style=<?php echo urlencode($style ?? 'classique'); ?>">Accueil</a>
                &#160;|&#160;
                <a href="tech.php?style=<?php echo urlencode($style ?? 'classique'); ?>">Développeur</a>
                &#160;|&#160;
                <a href="plan.php?style=<?php echo urlencode($style ?? 'classique'); ?>">Plan du site</a>
                &#160;|&#160;
                <a href="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])); ?>?style=<?php echo ($style === 'sombre' ? 'classique' : 'sombre'); ?>">
                    <?php echo ($style === 'sombre') ? 'Mode classique' : 'Mode sombre'; ?>
                </a>
            </p>
        </div>
    </div>
</footer>

</body>
</html>
