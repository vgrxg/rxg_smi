# RXG Site Maillage Interne (RXG SMI) - Phase 2

Version 2.0.0 - Plugin WordPress pour cartographier, analyser et optimiser le maillage interne de votre site.

## Nouveautés de la Phase 2

Cette mise à jour majeure (v2.0.0) ajoute de nombreuses fonctionnalités avancées d'analyse et de visualisation pour vous aider à optimiser votre maillage interne :

### Nouvelles fonctionnalités

- **Analyse de hiérarchie** : Visualisation de l'arborescence complète du site et détection des pages trop profondes
- **Analyse des taxonomies** : Exploration des catégories, tags et taxonomies personnalisées pour identifier les clusters thématiques
- **Analyse des textes d'ancre** : Évaluation de la diversité des ancres et détection des ancres trop similaires
- **Calcul de profondeur** : Détermination automatique de la profondeur de chaque page dans la structure du site
- **Position des liens** : Analyse de la position des liens (contenu, header, footer, etc.) et évaluation de leur importance
- **Ratio mots/liens** : Calcul du ratio entre le contenu et les liens pour identifier les déséquilibres
- **Suggestions intelligentes** : Recommandations de maillage basées sur les taxonomies et les thématiques communes
- **Interface améliorée** : Tableau de bord redessiné avec visualisations, filtres avancés et suggestions concrètes

### Améliorations techniques

- Architecture modulaire avec analyseurs spécialisés
- Stockage optimisé pour de meilleures performances
- Support AJAX pour les interactions dynamiques
- Système de filtrage avancé par profondeur, taxonomie, etc.
- Détection automatique des opportunités de maillage

## Utilisation

### Nouvelles sections d'administration

Le plugin ajoute désormais les sections suivantes à votre menu d'administration :

- **Tableau de bord** : Vue d'ensemble des statistiques du maillage interne
- **Pages** : Liste complète des pages avec filtres avancés
- **Liens** : Analyse détaillée des liens internes et externes
- **Hiérarchie** : Visualisation de l'arborescence et des niveaux de profondeur
- **Taxonomies** : Exploration des clusters thématiques
- **Analyse d'ancres** : Évaluation de la diversité et pertinence des textes d'ancre
- **Opportunités** : Suggestions concrètes pour améliorer votre maillage interne
- **Paramètres** : Configuration du plugin

### Workflow recommandé

1. Accédez à "Maillage Interne" dans le menu WordPress
2. Lancez une analyse complète du site depuis le tableau de bord
3. Explorez la hiérarchie pour comprendre la structure de votre site
4. Consultez les taxonomies pour identifier les clusters thématiques
5. Vérifiez les textes d'ancre pour améliorer leur diversité
6. Utilisez la section "Opportunités" pour découvrir les améliorations possibles
7. Implémentez les suggestions proposées en éditant vos pages

## Structure des fichiers

```
rxg-smi/
├── admin/
│   ├── css/
│   │   └── rxg-smi-admin.css
│   ├── js/
│   │   └── rxg-smi-admin.js
│   └── partials/
│       ├── rxg-smi-admin-dashboard.php
│       ├── rxg-smi-admin-pages.php
│       ├── rxg-smi-admin-links.php
│       ├── rxg-smi-admin-hierarchy.php
│       ├── rxg-smi-admin-taxonomies.php
│       ├── rxg-smi-admin-anchors.php
│       ├── rxg-smi-admin-opportunities.php
│       └── rxg-smi-admin-settings.php
│   └── class-rxg-smi-admin.php
├── includes/
│   ├── class-rxg-smi-db.php
│   ├── class-rxg-smi-hierarchy-analyzer.php
│   ├── class-rxg-smi-taxonomy-analyzer.php
│   ├── class-rxg-smi-anchor-analyzer.php
│   ├── class-rxg-smi-content-analyzer.php
│   ├── class-rxg-smi-crawler.php
│   ├── class-rxg-smi-activator.php
│   ├── class-rxg-smi-deactivator.php
│   └── class-rxg-smi-ajax.php
└── rxg-smi.php
```

## Notes techniques

### Analyseurs spécialisés

La phase 2 introduit une architecture modulaire avec des analyseurs spécialisés :

- **Hierarchy Analyzer** : Analyse la structure hiérarchique du site
- **Taxonomy Analyzer** : Analyse les taxonomies et clusters thématiques
- **Anchor Analyzer** : Analyse la diversité et pertinence des textes d'ancre
- **Content Analyzer** : Analyse le contenu et la position des liens

### Structure de la base de données

Le plugin utilise désormais les tables suivantes :

- `wp_rxg_smi_pages` : Informations sur les pages (avec nouveaux champs pour Phase 2)
- `wp_rxg_smi_links` : Informations sur les liens (avec position et poids)
- `wp_rxg_smi_page_terms` : Termes de taxonomie associés à chaque page
- `wp_rxg_smi_anchor_stats` : Statistiques d'utilisation des textes d'ancre

### Optimisations

- **Cache transient** : Les calculs intensifs sont mis en cache
- **Traitement par lots** : Le crawl est optimisé pour traiter les grandes quantités de pages
- **Requêtes SQL optimisées** : Utilisation d'index et de requêtes efficaces
- **AJAX asynchrone** : Les interactions utilisateur ne bloquent pas l'interface

## Roadmap pour la Phase 3

Les fonctionnalités prévues pour la prochaine phase incluent :

- Algorithme PageRank avancé pour une mesure plus précise de l'importance des pages
- Visualisation graphique interactive du maillage interne
- Analyse sémantique du contenu pour des suggestions encore plus pertinentes
- Intégration avec Google Analytics pour corréler maillage et performance
- Export de rapports PDF et comparaison de l'évolution du maillage dans le temps

## Notes de mise à jour

Lors de la mise à jour depuis la version 1.x, les tables de la base de données seront automatiquement mises à jour avec les nouveaux champs requis. Une nouvelle analyse complète est recommandée après la mise à jour pour bénéficier de toutes les nouvelles fonctionnalités.

## Crédits et Licence

Développé par Votre Nom

Licence : GPLv2 ou ultérieure
