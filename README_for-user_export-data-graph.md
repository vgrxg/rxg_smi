# Guide d'utilisation de l'export JSON pour visualisation

## Structure du fichier JSON

Le fichier JSON exporté contient deux sections principales:

```json
{
  "pages": [...],  // Liste des pages du site
  "links": [...]   // Liste des liens entre les pages
}
```

### Pages

Chaque page contient les attributs suivants:

- `id`: Identifiant unique de la page
- `title`: Titre de la page
- `url`: URL relative de la page
- `type`: Type de contenu WordPress (post, page, etc.)
- `inbound_links_count`: Nombre de liens entrants
- `outbound_links_count`: Nombre de liens sortants
- `juice_score`: Score d'importance calculé (équivalent PageRank simplifié)
- `depth`: Profondeur dans l'arborescence du site
- `word_count`: Nombre de mots
- `taxonomies`: Liste des termes de taxonomie associés
- `cluster`: Cluster thématique identifié par l'analyse sémantique

### Liens

Chaque lien contient:

- `source`: ID de la page source
- `target`: ID de la page cible
- `anchor`: Texte d'ancre utilisé
- `weight`: Poids du lien (importance)
- `position`: Position du lien (heading, paragraph, etc.)
- `section`: Section de la page (content, footer, etc.)

## Outils de visualisation recommandés

### Gephi (Analyse de graphe professionnelle)

1. Importez le JSON avec le plugin "JSON Graph"
2. Utilisez l'algorithme de disposition ForceAtlas2
3. Redimensionnez les nœuds selon `inbound_links_count`
4. Colorez les nœuds selon `cluster` ou `type`

### Freemind/XMind (Mindmapping)

1. Convertissez d'abord le JSON en format .mm avec un outil comme json2mm
2. Importez dans Freemind
3. Organisez par clusters ou par profondeur

### D3.js (Développeurs web)

Si vous avez des compétences en développement, D3.js permet de créer des visualisations interactives personnalisées.

## Conseils d'interprétation

- **Pages isolées**: Identifiez les pages sans liens entrants (orphelines)
- **Clusters déconnectés**: Cherchez les groupes de pages liées entre elles mais isolées du reste du site
- **Hubs**: Pages avec beaucoup de liens entrants et sortants (pages clés pour la navigation)
- **Pages à fort juice_score**: Pages avec un fort potentiel SEO, vérifiez qu'elles sont bien optimisées
