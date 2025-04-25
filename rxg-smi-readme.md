# RXG Site Maillage Interne (RXG SMI)

Plugin WordPress pour cartographier, analyser et optimiser le maillage interne de votre site.

## 🚀 Version actuelle: 1.0.0 (Phase 1)

## Description

RXG SMI analyse la structure des liens internes de votre site WordPress pour vous fournir une cartographie complète de votre maillage. Il vous permet d'identifier les pages fortement liées, les opportunités d'amélioration et la structure globale de votre contenu.

## Fonctionnalités actuelles (Phase 1)

- ✅ Crawl automatique des contenus WordPress
- ✅ Extraction des liens dans le contenu avec informations détaillées
- ✅ Analyse des attributs des liens (nofollow, sponsored, etc.)
- ✅ Vérification des statuts HTTP pour les liens externes
- ✅ Tableau de bord avec métriques et statistiques
- ✅ Liste des pages avec comptage des liens entrants/sortants
- ✅ Exploration détaillée des liens par page
- ✅ Système de planification des analyses (quotidien, hebdomadaire, etc.)

## Installation

1. Téléchargez et décompressez le plugin dans le dossier `/wp-content/plugins/`
2. Activez le plugin via le menu 'Extensions' dans WordPress
3. Accédez au menu "Maillage Interne" dans le tableau de bord
4. Cliquez sur "Analyser le site maintenant" pour lancer la première analyse

## Structure des données

Le plugin stocke les données dans deux tables principales:

- `wp_rxg_smi_pages`: Informations sur les pages analysées
- `wp_rxg_smi_links`: Détails de tous les liens trouvés

## Optimisations possibles

### Performance
- Augmenter la limite de temps d'exécution pour les grands sites
- Implémenter un système de file d'attente pour les analyses par lots
- Ajouter des index supplémentaires aux tables pour les grands volumes
- Optimiser les requêtes SQL pour les sites avec beaucoup de contenu

### Robustesse
- Améliorer la gestion des erreurs pendant le crawl
- Ajouter une journalisation détaillée pour le débogage
- Gérer les cas particuliers comme les liens relatifs et les URL avec fragments

### Interface
- Ajouter des graphiques pour visualiser les données
- Implémenter des filtres plus avancés
- Améliorer la pagination pour les sites avec beaucoup de pages

## Roadmap Phase 2

### Métriques fondamentales (2-3 semaines)
- ✅ Profondeur dans l'arborescence du site (pour pages hiérarchiques)
  - Calculer la distance depuis la page d'accueil
  - Visualiser la hiérarchie des pages
  - Identifier les contenus trop profonds

- ✅ Analyse de la structure des ancres (diversité, longueur)
  - Calculer des scores de diversité des ancres
  - Détecter les ancres suroptimisées
  - Suggérer des variantes pour les ancres trop répétitives

- ✅ Comptage de mots par page
  - Analyser la densité du contenu
  - Corréler le volume de contenu avec le nombre de liens
  - Suggérer des améliorations basées sur le ratio mots/liens

- ✅ Classification par type de contenu et taxonomies WordPress
  - Segmenter l'analyse par catégories/tags
  - Visualiser les silos de contenu
  - Identifier les connexions entre thématiques

- ✅ Système de filtrage et tri dans l'interface admin
  - Filtres par taxonomies, types et attributs
  - Tris multiples et combinés
  - Export des données filtrées

- ✅ Position des liens dans le contenu (header, content, footer)
  - Identifier la distribution des liens dans la page
  - Distinguer les liens de navigation des liens éditoriaux
  - Évaluer l'importance des liens selon leur position

### Techniques WordPress à utiliser
- Exploiter l'API WordPress `wp_get_post_parent_id()` pour la hiérarchie
- Utiliser `wp_get_object_terms()` pour les taxonomies
- Implémenter un système de cache avec transients pour les calculs intensifs
- Utiliser les hooks spécifiques au plugin pour l'extensibilité

## Implémentation technique (Phase 2)

### Profondeur dans l'arborescence
```php
/**
 * Calcule la profondeur d'une page dans l'arborescence
 */
private function calculate_depth($post_id) {
    $depth = 0;
    $parent_id = wp_get_post_parent_id($post_id);
    
    while ($parent_id) {
        $depth++;
        $parent_id = wp_get_post_parent_id($parent_id);
    }
    
    return $depth;
}
```

### Analyse des taxonomies
```php
/**
 * Récupère les taxonomies d'un post
 */
private function get_post_taxonomies($post_id) {
    $taxonomies = get_object_taxonomies(get_post_type($post_id));
    $terms = array();
    
    foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy);
        if (!empty($post_terms) && !is_wp_error($post_terms)) {
            $terms[$taxonomy] = $post_terms;
        }
    }
    
    return $terms;
}
```

## Configuration requise

- WordPress 5.6 ou supérieur
- PHP 7.2 ou supérieur
- MySQL 5.6 ou supérieur

## Ressources et documentation

- [Documentation WordPress sur les fonctions de taxonomie](https://developer.wordpress.org/reference/functions/wp_get_object_terms/)
- [Guide sur l'utilisation du plugin](https://votre-documentation.com)
- [Tutoriel sur l'optimisation du maillage interne](https://votre-blog.com)

## Support et développement

Pour toute question ou suggestion, contactez l'équipe de développement à support@votresite.com.

---

© 2025 Votre Entreprise. Tous droits réservés.
