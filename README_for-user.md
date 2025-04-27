# Documentation Utilisateur - RXG Site Maillage Interne (RXG SMI)

## Introduction

RXG Site Maillage Interne est un plugin WordPress dédié à l'analyse et l'optimisation de votre maillage interne - l'ensemble des liens qui connectent les pages de votre site entre elles. Un maillage interne bien structuré est fondamental pour le référencement et l'expérience utilisateur.

Ce document explique en détail le fonctionnement du plugin, ses différentes fonctionnalités et comment les utiliser efficacement pour améliorer votre stratégie de maillage interne.

## Table des matières

1. [Pourquoi le maillage interne est important](#pourquoi-le-maillage-interne-est-important)
2. [Vue d'ensemble du plugin](#vue-densemble-du-plugin)
3. [Prise en main rapide](#prise-en-main-rapide)
4. [Tableau de bord principal](#tableau-de-bord-principal)
5. [Analyse des pages](#analyse-des-pages)
6. [Analyse des liens](#analyse-des-liens)
7. [Analyse de la hiérarchie](#analyse-de-la-hiérarchie)
8. [Analyse des taxonomies](#analyse-des-taxonomies)
9. [Analyse des textes d'ancre](#analyse-des-textes-dancre)
10. [Opportunités de maillage](#opportunités-de-maillage)
11. [Analyse sémantique](#analyse-sémantique)
12. [Paramètres](#paramètres)
13. [FAQ et dépannage](#faq-et-dépannage)

## Pourquoi le maillage interne est important

Le maillage interne fait référence à la façon dont les pages de votre site sont liées entre elles. Son importance repose sur plusieurs aspects fondamentaux du référencement et de l'expérience utilisateur:

### Pour le référencement (SEO)

1. **Distribution du "jus de lien" (PageRank)**: Les moteurs de recherche attribuent une valeur à chaque page. Lorsqu'une page fait un lien vers une autre, elle lui transmet une partie de cette valeur. Un bon maillage interne permet de distribuer cette valeur de manière stratégique vers vos pages les plus importantes.

2. **Découverte et indexation**: Les moteurs de recherche comme Google découvrent vos pages en suivant les liens. Sans liens internes, certaines pages peuvent rester invisibles pour les robots d'indexation (d'où l'importance de ne pas avoir de "pages orphelines").

3. **Compréhension thématique**: Les liens internes, en particulier leurs textes d'ancrage, aident les moteurs de recherche à comprendre la relation thématique entre vos pages et à identifier le sujet principal de chaque page.

4. **Profondeur de site**: Les pages trop "profondes" (nécessitant de nombreux clics depuis la page d'accueil) sont généralement considérées comme moins importantes par les moteurs de recherche. Un bon maillage permet de réduire cette profondeur.

### Pour l'expérience utilisateur

1. **Navigation**: Un maillage bien pensé guide naturellement vos visiteurs vers d'autres contenus pertinents, augmentant ainsi leur engagement.

2. **Réduction du taux de rebond**: En proposant des liens contextuels pertinents, vous encouragez les visiteurs à explorer davantage votre site.

3. **Architecture de l'information**: Le maillage interne contribue à créer une structure logique et intuitive pour votre site.

## Vue d'ensemble du plugin

RXG SMI offre une suite complète d'outils pour analyser et optimiser votre maillage interne:

- **Crawl automatique** de votre site pour cartographier tous les liens
- **Analyse détaillée** de la structure de vos liens et pages
- **Visualisation de la hiérarchie** de votre site
- **Identification des clusters thématiques** basés sur vos taxonomies
- **Analyse de la diversité des textes d'ancre**
- **Détection automatique des opportunités d'amélioration**
- **Suggestions intelligentes** de nouveaux liens à créer

## Prise en main rapide

1. **Installation**:
   - Téléchargez et activez le plugin via le menu Extensions de WordPress
   - Accédez au menu "Maillage Interne" dans la barre latérale d'administration

2. **Première analyse**:
   - Sur le tableau de bord du plugin, cliquez sur "Analyser le site maintenant"
   - Attendez que l'analyse soit terminée (cela peut prendre quelques minutes selon la taille de votre site)

3. **Exploration des résultats**:
   - Parcourez les différentes sections du plugin pour découvrir les insights sur votre maillage interne
   - Commencez par consulter la section "Opportunités" pour voir les améliorations potentielles les plus importantes

## Tableau de bord principal

Le tableau de bord vous donne une vue d'ensemble de l'état actuel de votre maillage interne.

### Statistiques clés

- **Pages analysées**: Nombre total de pages indexées par le plugin
- **Liens totaux**: Nombre total de liens trouvés sur votre site
- **Liens internes**: Nombre de liens pointant vers d'autres pages de votre site
- **Liens externes**: Nombre de liens pointant vers d'autres sites

### Statut de l'analyse

Cette section vous indique quand la dernière analyse a été effectuée et quand la prochaine est prévue.

### Statistiques avancées

- **Pages orphelines**: Pages qui ne reçoivent aucun lien interne. Ces pages sont généralement sous-optimisées car elles ne bénéficient pas du transfert de "jus de lien" et sont plus difficiles à découvrir pour les utilisateurs comme pour les moteurs de recherche.
  
- **Profondeur maximale**: Nombre maximal de clics nécessaires pour atteindre une page depuis la page d'accueil. Une profondeur excessive (généralement >4) peut nuire à l'expérience utilisateur et à l'efficacité de l'indexation.

- **Taxonomies et termes**: Aperçu des structures thématiques de votre site.

### Onglets d'information

- **Pages populaires**: Les pages qui reçoivent le plus de liens internes
- **Textes d'ancre**: Les textes d'ancrage les plus utilisés sur votre site
- **Clusters thématiques**: Groupes de pages liées par des sujets communs
- **Opportunités**: Résumé des améliorations potentielles

## Analyse des pages

Cette section liste toutes les pages de votre site avec des métriques détaillées pour chacune.

### Filtres disponibles

- **Type de contenu**: Filtrez par articles, pages, produits, etc.
- **Profondeur**: Filtrez par niveau de profondeur dans la structure du site
- **Nombre de mots**: Filtrez par volume de contenu

### Colonnes du tableau

- **Titre**: Titre de la page
- **URL**: Adresse de la page
- **Type**: Type de contenu WordPress
- **Liens entrants**: Nombre de liens pointant vers cette page
- **Liens sortants**: Nombre de liens partant de cette page
- **Score**: Score de "jus" calculé par le plugin (indicateur de l'importance de la page dans la structure)

### Actions disponibles

- **Voir les liens**: Accéder à l'analyse détaillée des liens de cette page
- **Éditer**: Ouvrir la page dans l'éditeur WordPress

## Analyse des liens

Cette section permet d'explorer en détail les liens d'une page spécifique.

### Filtres

- **Sélection de page**: Choisissez la page à analyser
- **Direction**: Liens sortants (de cette page vers d'autres) ou entrants (d'autres pages vers celle-ci)

### Pour les liens sortants

- **Page de destination**: Page cible du lien
- **URL**: Adresse complète du lien
- **Texte d'ancre**: Texte cliquable du lien
- **Attributs**: Attributs spéciaux comme nofollow, sponsored, etc.
- **Position**: Emplacement du lien dans la page (titre, paragraphe, liste, etc.)
- **Statut**: Pour les liens externes, affiche le statut HTTP (200 OK, 404 Not Found, etc.)

### Pour les liens entrants

- **Page source**: Page d'où provient le lien
- **Texte d'ancre**: Texte utilisé pour le lien
- **Attributs**: Attributs spéciaux du lien
- **Position**: Emplacement du lien dans la page source
- **Contexte**: Extrait du texte entourant le lien

## Analyse de la hiérarchie

Cette section visualise la structure hiérarchique de votre site et identifie les problèmes potentiels de profondeur.

### Statistiques de hiérarchie

- **Profondeur maximale**: Nombre maximal de niveaux dans votre structure
- **Pages orphelines**: Nombre de pages sans liens entrants
- **Pages de niveau 0**: Nombre de pages au premier niveau (généralement la page d'accueil)

### Distribution par profondeur

Tableau montrant combien de pages se trouvent à chaque niveau de profondeur, avec la possibilité de voir les pages à chaque niveau.

### Arborescence

Visualisation de la structure hiérarchique complète du site sous forme d'arbre, montrant les relations parent-enfant entre les pages.

### Pages orphelines

Liste des pages qui ne reçoivent aucun lien interne, avec des options pour voir des suggestions d'amélioration.

### Recommandations

Conseils personnalisés basés sur l'analyse de votre structure, comme:
- Alertes pour les pages trop profondes
- Suggestions pour améliorer l'organisation hiérarchique
- Recommandations pour les pages orphelines

## Analyse des taxonomies

Cette section explore les relations thématiques entre vos pages basées sur les taxonomies WordPress (catégories, tags, etc.).

### Sélection de taxonomie

Un menu déroulant vous permet de choisir quelle taxonomie explorer (catégories, tags ou taxonomies personnalisées).

### Informations sur la taxonomie

- **Nom**: Nom de la taxonomie sélectionnée
- **Nombre de termes**: Combien de termes différents existent dans cette taxonomie
- **Pages associées**: Combien de pages utilisent cette taxonomie

### Liste des termes

Affiche tous les termes de la taxonomie sélectionnée, avec:
- Nom et slug du terme
- Nombre de pages associées
- Options pour voir les pages utilisant ce terme ou l'archive correspondante

### Analyse des pages d'un terme

Lorsque vous sélectionnez un terme spécifique:
- Liste des pages associées à ce terme
- Autres termes fréquemment associés au terme sélectionné
- Analyse du maillage interne entre les pages de ce terme

### Maillage interne du cluster

Pour un terme sélectionné, le plugin analyse à quel point les pages partageant ce terme sont bien liées entre elles:
- Nombre de liens existants entre ces pages
- Nombre de liens possibles (liens potentiels)
- Pourcentage de liaison (complétude du maillage)
- Recommandations pour améliorer le maillage au sein de ce cluster thématique

## Analyse des textes d'ancre

Cette section évalue la qualité et la diversité des textes d'ancre utilisés dans votre site.

### Statistiques d'ancre

- **Textes d'ancre uniques**: Nombre de textes d'ancre différents utilisés
- **Utilisations totales**: Nombre total d'utilisations d'ancres
- **Score de diversité moyen**: Évaluation de la variété des ancres pointant vers vos pages

### Textes d'ancre populaires

Tableau des textes d'ancre les plus utilisés sur votre site, avec:
- Texte d'ancre
- Nombre d'occurrences
- Nombre de pages cibles
- Longueur du texte
- Analyse qualitative (richesse en mots-clés, présence de mots vides, etc.)

### Ancres similaires

Identification des paires d'ancres très similaires qui pourraient être harmonisées ou diversifiées.

### Pages à faible diversité

Liste des pages qui reçoivent plusieurs liens mais avec peu de variation dans les textes d'ancre, ce qui peut être perçu comme une sur-optimisation par les moteurs de recherche.

### Analyseur d'ancre

Outil permettant d'analyser un texte d'ancre spécifique et d'obtenir des suggestions d'amélioration.

## Opportunités de maillage

Cette section centrale identifie automatiquement les principales opportunités d'amélioration de votre maillage interne.

### Pages orphelines

Pages qui ne reçoivent aucun lien interne. Ces pages sont importantes à adresser car:
- Elles sont plus difficiles à découvrir pour les utilisateurs
- Elles reçoivent moins de "jus de lien" pour le référencement
- Elles peuvent être moins bien indexées par les moteurs de recherche

### Pages sans liens sortants

Pages qui ne contiennent aucun lien vers d'autres pages du site. Ces pages créent des "impasses" dans le parcours utilisateur et ne distribuent pas leur "jus de lien" à d'autres pages.

### Pages avec trop peu de liens

Pages avec un contenu substantiel mais relativement peu de liens sortants. Ces pages présentent des opportunités manquées de guider les utilisateurs vers d'autres contenus pertinents.

### Suggestions spécifiques à une page

Lorsque vous sélectionnez une page spécifique, le plugin vous propose:
- **Taxonomies suggérées**: Termes de taxonomie utilisés sur des pages liées mais pas sur celle-ci
- **Pages à lier**: Pages thématiquement liées mais non encore référencées
- **Textes d'ancre suggérés**: Propositions intelligentes pour les textes d'ancrage à utiliser

## Analyse sémantique

Cette fonctionnalité avancée analyse le contenu textuel de vos pages pour identifier les relations thématiques au-delà des simples liens et taxonomies.

### Opportunités sémantiques

Paires de pages qui partagent des thématiques communes mais ne sont pas encore liées entre elles, identifiées par une analyse algorithmique du contenu.

### Clusters thématiques

Groupes de pages formant naturellement des ensembles cohérents sur le plan du contenu, avec:
- Termes principaux caractérisant chaque cluster
- Pages représentatives de chaque thématique
- Options pour explorer en détail un cluster

### Termes fréquents

Visualisation des termes les plus significatifs sur l'ensemble de votre site, révélant les thématiques principales de votre contenu.

### Analyse sémantique d'une page spécifique

Pour une page individuelle:
- **Termes clés**: Mots et expressions les plus significatifs de cette page
- **Suggestions de liens**: Pages thématiquement proches basées sur l'analyse du contenu
- **Carte thématique**: Visualisation des relations avec d'autres pages du même cluster

## Paramètres

Cette section permet de configurer le fonctionnement du plugin.

### Paramètres généraux

- **Types de contenu à analyser**: Sélectionnez quels types de contenu WordPress (pages, articles, produits, etc.) doivent être inclus dans l'analyse.
- **Fréquence d'analyse automatique**: Définissez à quelle fréquence le plugin doit automatiquement analyser votre site (horaire, quotidien, hebdomadaire, etc.).

### Maintenance des données

Options pour effacer les données d'analyse et recommencer de zéro.

### Exportation des données

Fonctionnalités pour exporter les données d'analyse au format JSON ou CSV.

## FAQ et dépannage

### Questions fréquemment posées

**Q: Combien de temps prend l'analyse complète du site?**
R: Cela dépend principalement du nombre de pages de votre site. Pour un site de taille moyenne (100-500 pages), l'analyse prend généralement entre 2 et 5 minutes.

**Q: L'analyse ralentit-elle mon site?**
R: Non, l'analyse s'exécute en arrière-plan et n'affecte pas les performances de votre site pour les visiteurs.

**Q: Que signifie exactement le "score" attribué aux pages?**
R: Le score est un indicateur calculé de l'importance d'une page dans la structure de votre site, basé sur le nombre et la qualité des liens qu'elle reçoit. C'est un concept similaire au PageRank de Google, mais simplifié et adapté à votre site spécifique.

**Q: Pourquoi certaines pages apparaissent comme "orphelines" alors qu'elles sont accessibles via le menu de navigation?**
R: Le plugin analyse principalement les liens dans le contenu des pages. Si une page n'est accessible que via le menu de navigation ou le pied de page, elle peut être considérée comme "orpheline" du point de vue du contenu. Bien que ces liens dans les menus soient utiles pour la navigation, les liens contextuels au sein du contenu ont généralement plus de valeur pour le référencement.

**Q: Les liens externes affectent-ils le score des pages internes?**
R: Non, dans cette version du plugin, seuls les liens internes sont pris en compte pour le calcul des scores des pages.

### Dépannage+

**Problème: L'analyse semble bloquée ou incomplète**
Solution: Vérifiez les limites de temps d'exécution sur votre serveur. Vous pouvez essayer d'augmenter la valeur de `max_execution_time` dans votre fichier php.ini ou contacter votre hébergeur.

**Problème: Certaines pages ne sont pas analysées**
Solution: Vérifiez que le type de contenu de ces pages est bien sélectionné dans les paramètres du plugin. Assurez-vous également que ces pages sont publiées et non en brouillon.

**Problème: Les données semblent incorrectes après une mise à jour du site**
Solution: Lancez une nouvelle analyse complète pour mettre à jour les données du plugin.

---

Cette documentation vous donne toutes les informations nécessaires pour utiliser efficacement le plugin RXG Site Maillage Interne et améliorer significativement la structure de liens de votre site. N'hésitez pas à explorer systématiquement chaque section pour tirer le meilleur parti de cet outil.