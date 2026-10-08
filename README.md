# Carbuto

Site web en PHP qui devait permettre de consulter les prix des carburants dans les stations-service en France. Dans cette version du dépôt, seules la page d'accueil et la page « Développeur » sont présentes : un film aléatoire du studio Ghibli et la géolocalisation du visiteur à partir de son adresse IP. Les autres liens du menu (prix carburant, statistiques, plan du site) pointent vers `#` dans ce dossier.

Projet réalisé en L2 Informatique (S4, 2025-2026), dans l'UE Développement Web, à deux avec Mariam Traore.

## Technos

- PHP, HTML, CSS (deux thèmes : classique et sombre)
- API Ghibli (https://ghibliapi.vercel.app/films)
- API REST IP2Location (https://api.ip2location.io)

## Lancer le projet

1. Copier `.env.example` en `.env` et mettre sa clé IP2Location dans `IP2LOCATION_KEY`.
2. Lancer un serveur PHP depuis le dossier du projet :

```
php -S localhost:8000
```

3. Ouvrir http://localhost:8000/index.php

Il faut que PHP puisse faire des requêtes HTTP (`allow_url_fopen`), car les deux API sont appelées avec `file_get_contents`.

## Captures d'écran

Page d'accueil, thème classique :

![accueil classique](captures/accueil-classique.png)

Page d'accueil, thème sombre :

![accueil sombre](captures/accueil-sombre.png)

Page « Développeur » (film aléatoire et géolocalisation) : [À COMPLÉTER : capture]

## Ce que j'ai fait

- la page d'accueil
- la page du film aléatoire
- le plan du site
- le diagramme des statistiques de visites
- la géolocalisation des visiteurs par adresse IP avec l'API REST IP2Location

Mariam Traore a fait le reste.

[À COMPLÉTER] le plan du site et le diagramme des statistiques ne sont pas dans ce dossier : ajouter les fichiers ou retirer ces deux lignes.
