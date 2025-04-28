<?php
/**
 * Template pour la documentation du plugin
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-documentation">
        <div class="rxg-smi-doc-navigation">
            <ul>
                <li><a href="#introduction">Introduction</a></li>
                <li><a href="#importance">Pourquoi le maillage interne est important</a></li>
                <li><a href="#fondements">Fondements techniques et scientifiques</a></li>
                <li><a href="#pages">Pages orphelines</a></li>
                <li><a href="#profondeur">Profondeur de page</a></li>
                <li><a href="#juice">Distribution du "jus de lien"</a></li>
                <li><a href="#ancre">Diversité des textes d'ancre</a></li>
                <li><a href="#clusters">Clusters thématiques</a></li>
                <li><a href="#semantique">Analyse sémantique</a></li>
            </ul>
        </div>
        
        <div class="rxg-smi-doc-content">
            <section id="introduction">
                <h2>Introduction</h2>
                <p>RXG Site Maillage Interne est un plugin WordPress dédié à l'analyse et l'optimisation de votre maillage interne - l'ensemble des liens qui connectent les pages de votre site entre elles. Un maillage interne bien structuré est fondamental pour le référencement et l'expérience utilisateur.</p>
                <p>Ce plugin vous offre une suite complète d'outils pour analyser, visualiser et améliorer la structure de liens internes de votre site, vous permettant ainsi d'optimiser à la fois l'expérience utilisateur et le référencement naturel.</p>
            </section>
            
            <section id="importance">
                <h2>Pourquoi le maillage interne est important</h2>
                <p>Le maillage interne fait référence à la façon dont les pages de votre site sont liées entre elles. Son importance repose sur plusieurs aspects fondamentaux du référencement et de l'expérience utilisateur:</p>
                
                <h3>Pour le référencement (SEO)</h3>
                <ol>
                    <li>
                        <strong>Distribution du "jus de lien" (PageRank)</strong>: Les moteurs de recherche attribuent une valeur à chaque page. Lorsqu'une page fait un lien vers une autre, elle lui transmet une partie de cette valeur. Un bon maillage interne permet de distribuer cette valeur de manière stratégique vers vos pages les plus importantes.
                    </li>
                    <li>
                        <strong>Découverte et indexation</strong>: Les moteurs de recherche comme Google découvrent vos pages en suivant les liens. Sans liens internes, certaines pages peuvent rester invisibles pour les robots d'indexation (d'où l'importance de ne pas avoir de "pages orphelines").
                    </li>
                    <li>
                        <strong>Compréhension thématique</strong>: Les liens internes, en particulier leurs textes d'ancrage, aident les moteurs de recherche à comprendre la relation thématique entre vos pages et à identifier le sujet principal de chaque page.
                    </li>
                    <li>
                        <strong>Profondeur de site</strong>: Les pages trop "profondes" (nécessitant de nombreux clics depuis la page d'accueil) sont généralement considérées comme moins importantes par les moteurs de recherche. Un bon maillage permet de réduire cette profondeur.
                    </li>
                </ol>
                
                <h3>Pour l'expérience utilisateur</h3>
                <ol>
                    <li>
                        <strong>Navigation</strong>: Un maillage bien pensé guide naturellement vos visiteurs vers d'autres contenus pertinents, augmentant ainsi leur engagement.
                    </li>
                    <li>
                        <strong>Réduction du taux de rebond</strong>: En proposant des liens contextuels pertinents, vous encouragez les visiteurs à explorer davantage votre site.
                    </li>
                    <li>
                        <strong>Architecture de l'information</strong>: Le maillage interne contribue à créer une structure logique et intuitive pour votre site.
                    </li>
                </ol>
            </section>
            
            <section id="fondements">
                <h2>Fondements techniques et scientifiques des métriques du plugin</h2>
                <p>Cette section explique les bases scientifiques et techniques sur lesquelles reposent les analyses et recommandations du plugin.</p>
                
                <h3>Algorithmes de crawl et d'indexation</h3>
                <p>Le plugin RXG SMI utilise des algorithmes similaires à ceux employés par les moteurs de recherche pour analyser la structure de votre site. Il parcourt systématiquement toutes les pages et enregistre les liens entre elles, créant ainsi une carte complète de votre maillage interne.</p>
                
                <h3>Calcul du score de "jus de lien"</h3>
                <p>Inspiré par l'algorithme PageRank de Google, le score de "jus de lien" attribué à chaque page est calculé en fonction de plusieurs facteurs :</p>
                <ul>
                    <li>Le nombre de liens entrants vers cette page</li>
                    <li>La position des liens (dans un titre, paragraphe, liste, etc.)</li>
                    <li>La section où se trouve le lien (contenu principal, sidebar, footer, etc.)</li>
                    <li>L'importance des pages qui font ces liens</li>
                </ul>
                
                <h3>Analyse des clusters thématiques</h3>
                <p>L'identification des clusters thématiques est basée sur des algorithmes de regroupement (clustering) qui analysent les relations entre les pages en fonction de leurs taxonomies communes (catégories, tags) et de leur contenu sémantique.</p>
                
                <h3>Analyse sémantique</h3>
                <p>L'analyse sémantique utilise des techniques de traitement du langage naturel pour identifier les termes significatifs et leurs relations dans votre contenu. Ces techniques comprennent :</p>
                <ul>
                    <li>Tokenisation (découpage du texte en mots)</li>
                    <li>Filtrage des mots vides (stopwords)</li>
                    <li>Calcul de la fréquence des termes (TF-IDF)</li>
                    <li>Analyse de similarité par mesure cosinus entre vecteurs de termes</li>
                </ul>
            </section>
            
            <section id="pages">
                <h2>Pages orphelines</h2>
                <h3>Affirmation du plugin</h3>
                <p><em>"Ces pages ne reçoivent aucun lien interne, ce qui les rend difficiles à découvrir pour les utilisateurs et les moteurs de recherche."</em></p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Crawl et découverte par les moteurs de recherche</strong> : Les moteurs de recherche comme Google découvrent les pages d'un site principalement en suivant les liens. Cette approche est documentée dans les brevets de Google (comme le brevet "Reasonable Surfer" US Patent 8,626,752) et dans les déclarations officielles de Google. Sans liens internes, une page peut uniquement être découverte si :
                        <ul>
                            <li>Elle est incluse dans le sitemap XML (qui n'est qu'une indication, pas une garantie d'indexation)</li>
                            <li>Elle est liée depuis un site externe</li>
                            <li>Son URL est soumise directement via Google Search Console</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Impact sur l'autorité de la page</strong> : Selon le modèle PageRank et les modèles similaires utilisés par les moteurs de recherche, les pages qui ne reçoivent pas de liens n'accumulent pas d'autorité à partir des autres pages du site. Cela limite leur potentiel de classement dans les résultats de recherche.
                    </li>
                    <li>
                        <strong>Conséquences sur l'expérience utilisateur</strong> : Les utilisateurs naviguent principalement en suivant les liens. Une page orpheline ne sera trouvée que par des recherches directes sur le site, ou si elle est présente dans le menu de navigation, mais jamais à travers le contenu lui-même, ce qui réduit considérablement sa visibilité.
                    </li>
                </ol>
                
                <h3>Recommandations</h3>
                <p>Pour résoudre ce problème, le plugin recommande de créer des liens contextuels vers les pages orphelines à partir d'autres pages thématiquement liées. Pour chaque page orpheline, le plugin propose :</p>
                <ul>
                    <li>Des pages sources potentielles ayant des thématiques communes</li>
                    <li>Des suggestions de textes d'ancre optimaux basés sur le contenu de la page cible</li>
                    <li>Des positions idéales pour placer ces liens dans le contenu</li>
                </ul>
            </section>
            
            <section id="profondeur">
                <h2>Profondeur de page</h2>
                <h3>Affirmation du plugin</h3>
                <p><em>"Les pages trop profondes sont plus difficiles à découvrir et reçoivent moins de 'jus de lien', ce qui peut limiter leur potentiel en termes de référencement."</em></p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Atténuation du PageRank</strong> : Selon la formule originale du PageRank, le "jus de lien" se dilue à mesure qu'il traverse des liens successifs. Bien que les algorithmes modernes soient plus complexes, les pages situées à plusieurs clics de la page d'accueil tendent à recevoir moins d'autorité.
                    </li>
                    <li>
                        <strong>Budget de crawl</strong> : Les moteurs de recherche disposent d'un "budget de crawl" limité pour chaque site. Les pages plus profondes sont généralement crawlées moins fréquemment, ce qui peut retarder l'indexation des mises à jour et du nouveau contenu.
                    </li>
                    <li>
                        <strong>Comportement des utilisateurs</strong> : Des études d'UX montrent que les utilisateurs sont moins susceptibles d'atteindre des pages nécessitant plus de 3-4 clics depuis l'entrée sur le site. Chaque niveau de profondeur supplémentaire réduit significativement le taux de visite.
                    </li>
                </ol>
                
                <h3>Méthode de calcul</h3>
                <p>Le plugin calcule la profondeur de chaque page en déterminant le nombre minimum de clics nécessaires pour l'atteindre depuis la page d'accueil. Cette analyse prend en compte :</p>
                <ul>
                    <li>La structure hiérarchique des pages (pages parentes/enfants)</li>
                    <li>Les liens transversaux qui peuvent créer des raccourcis dans la structure</li>
                    <li>Les menus de navigation (qui peuvent permettre d'accéder directement à des pages qui seraient autrement profondes)</li>
                </ul>
                
                <h3>Recommandations</h3>
                <p>Pour optimiser la profondeur des pages, le plugin recommande :</p>
                <ul>
                    <li>Limiter la profondeur maximale du site à 3-4 niveaux quand c'est possible</li>
                    <li>Créer des liens transversaux directs vers les pages importantes situées en profondeur</li>
                    <li>Restructurer les sections du site dont la hiérarchie est trop profonde</li>
                    <li>Inclure les pages importantes dans les menus de navigation principaux</li>
                </ul>
            </section>
            
            <section id="juice">
                <h2>Distribution du "jus de lien"</h2>
                <h3>Concept et importance</h3>
                <p>Le "jus de lien" (Link Juice) est une métaphore utilisée en SEO pour décrire la transmission d'autorité et de pertinence d'une page à une autre via des liens. Ce concept est directement inspiré de l'algorithme PageRank développé par Google.</p>
                
                <h3>La colonne "Score" dans l'interface</h3>
                <p>Dans l'interface d'administration (page=rxg-smi-pages), vous remarquerez une colonne intitulée "Score". <strong>Cette valeur représente précisément le score de "jus de lien"</strong> calculé par le plugin pour chaque page. Plus ce score est élevé, plus la page est considérée comme importante dans la structure de votre site.</p>
                
                <p>Par exemple :</p>
                <ul>
                    <li>Une page d'accueil pourrait avoir un score de 50-100 (ou plus), indiquant qu'elle est au centre de votre maillage interne</li>
                    <li>Une page de catégorie importante pourrait avoir un score de 30-50</li>
                    <li>Un article régulier pourrait avoir un score de 10-30</li>
                    <li>Une page peu liée ou orpheline pourrait avoir un score inférieur à 10</li>
                </ul>
                
                <p>Ces scores ne sont pas absolus mais relatifs à votre site spécifique. Ils vous permettent d'identifier rapidement vos pages les plus influentes et celles qui pourraient bénéficier d'un meilleur maillage.</p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Principe de base du PageRank</strong> : Chaque page reçoit une valeur d'autorité basée sur la quantité et la qualité des liens qu'elle reçoit. Une partie de cette autorité est transmise à travers les liens sortants.
                    </li>
                    <li>
                        <strong>Facteurs de pondération</strong> : Tous les liens ne transmettent pas la même quantité de "jus". Les facteurs suivants influencent cette transmission :
                        <ul>
                            <li>Position du lien dans la page (les liens dans le contenu principal ont plus de valeur que ceux dans le footer)</li>
                            <li>Contexte thématique (les liens entre pages thématiquement liées transmettent plus de valeur)</li>
                            <li>Attributs de lien (nofollow, sponsored, ugc peuvent limiter ou bloquer la transmission)</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Dilution du "jus"</strong> : Plus une page contient de liens sortants, moins chaque lien individuel transmet de valeur, car le "jus" est divisé entre tous les liens.
                    </li>
                </ol>
                
                <h3>Méthode de calcul dans le plugin</h3>
                <p>Le plugin RXG SMI calcule le score de "jus de lien" pour chaque page en utilisant un algorithme itératif qui prend en compte :</p>
                <ul>
                    <li>Le nombre de liens entrants vers chaque page</li>
                    <li>Le score des pages qui font ces liens</li>
                    <li>La position et le poids relatif de chaque lien</li>
                    <li>La pertinence thématique entre les pages liées</li>
                </ul>
                
                <h3>Exemple concret de calcul</h3>
                <p>Prenons un exemple simple pour illustrer comment le score est calculé :</p>
                <ol>
                    <li>Chaque page commence avec un score de base de 10</li>
                    <li>Si la page A (avec un score de 50) contient un lien vers la page B dans le contenu principal, elle lui transmet environ 5 points (10% de son score) × 1.0 (poids du lien dans le contenu)</li>
                    <li>Si la page C (avec un score de 30) contient un lien vers la page B dans le footer, elle lui transmet environ 1.2 points (10% de son score × 0.4 pour le poids réduit du footer)</li>
                    <li>Le score final de la page B sera donc : 10 (base) + 5 + 1.2 = 16.2</li>
                </ol>
                <p>Ce calcul est en réalité plus complexe et itératif, mais cet exemple simplifié donne une idée du fonctionnement.</p>
                
                <h3>Optimisation de la distribution</h3>
                <p>Pour optimiser la distribution du "jus de lien" sur votre site, le plugin recommande :</p>
                <ul>
                    <li>Identifier les pages stratégiques qui méritent de recevoir plus de liens internes</li>
                    <li>Limiter le nombre de liens sortants des pages à forte autorité pour éviter la dilution</li>
                    <li>Utiliser une structure de liens en silo pour concentrer le "jus" dans des thématiques spécifiques</li>
                    <li>Veiller à ce que les pages importantes ne soient pas à plus de 3 clics de la page d'accueil</li>
                </ul>
                
                <h3>Application pratique</h3>
                <p>Voici comment utiliser concrètement cette métrique dans votre stratégie de maillage :</p>
                <ol>
                    <li>Triez vos pages par score (dans l'interface admin.php?page=rxg-smi-pages) pour identifier vos pages les plus influentes</li>
                    <li>Vérifiez si vos pages stratégiques pour la conversion (pages produit, pages de service, etc.) reçoivent un score suffisant</li>
                    <li>Pour les pages importantes ayant un faible score, créez des liens supplémentaires depuis des pages à score élevé</li>
                    <li>Ajustez votre menu principal pour inclure les pages qui méritent plus de "jus"</li>
                    <li>Surveillez l'évolution des scores après chaque analyse pour mesurer l'impact de vos modifications</li>
                </ol>
            </section>
            
            <section id="ancre">
                <h2>Diversité des textes d'ancre</h2>
                <h3>Affirmation du plugin</h3>
                <p><em>"Une diversité suffisante des textes d'ancre aide les moteurs de recherche à comprendre le contexte thématique d'une page et évite les signaux de sur-optimisation."</em></p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Compréhension thématique</strong> : Les moteurs de recherche utilisent les textes d'ancre pour comprendre le sujet d'une page. Des textes d'ancre variés mais thématiquement cohérents fournissent plus d'indices sur la thématique globale.
                    </li>
                    <li>
                        <strong>Sur-optimisation</strong> : L'utilisation répétitive du même texte d'ancre, particulièrement s'il s'agit d'un mot-clé commercial, peut être interprétée comme une tentative de manipulation du référencement. Depuis les mises à jour Penguin de Google, cela peut entraîner des pénalités.
                    </li>
                    <li>
                        <strong>Pertinence contextuelle</strong> : Des textes d'ancre variés permettent d'établir la pertinence d'une page pour une gamme plus large de requêtes et de intentions de recherche, augmentant ainsi son potentiel de trafic.
                    </li>
                </ol>
                
                <h3>Méthode d'analyse</h3>
                <p>Le plugin analyse la diversité des textes d'ancre en calculant :</p>
                <ul>
                    <li>Le nombre de textes d'ancre uniques pointant vers chaque page</li>
                    <li>La distribution statistique des différents textes (pour détecter les déséquilibres)</li>
                    <li>La similarité lexicale entre les différents textes d'ancre</li>
                    <li>Le rapport entre les ancres optimisées pour les mots-clés et les ancres génériques</li>
                </ul>
                
                <h3>Score de diversité</h3>
                <p>Le score de diversité d'ancre est calculé selon une formule qui prend en compte :</p>
                <ul>
                    <li>Le nombre d'ancres uniques par rapport au nombre total de liens entrants</li>
                    <li>L'entropie de la distribution (mesure statistique de la diversité)</li>
                    <li>La richesse lexicale des ancres (variété des termes utilisés)</li>
                </ul>
                
                <h3>Recommandations</h3>
                <p>Pour améliorer la diversité des textes d'ancre, le plugin suggère :</p>
                <ul>
                    <li>Varier les formulations tout en restant thématiquement cohérent</li>
                    <li>Utiliser un mélange d'ancres exactes, partielles, génériques et contextuelles</li>
                    <li>Éviter d'utiliser le même texte d'ancre dans plus de 30% des liens vers une page</li>
                    <li>S'assurer que les ancres reflètent les différents aspects et sous-thèmes de la page cible</li>
                </ul>
            </section>
            
            <section id="clusters">
                <h2>Clusters thématiques</h2>
                <h3>Concept et importance</h3>
                <p>Les clusters thématiques sont des ensembles de pages qui traitent de sujets connexes et qui devraient idéalement être interconnectées. Cette approche permet de créer des "îlots d'expertise" sur des thèmes spécifiques, ce qui renforce l'autorité thématique du site aux yeux des moteurs de recherche.</p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Modèle de topic cluster</strong> : Développé par HubSpot et largement adopté en SEO, ce modèle organise le contenu autour d'une page pilier principale liée à plusieurs pages satellites qui explorent des aspects spécifiques du sujet.
                    </li>
                    <li>
                        <strong>Algorithmes basés sur l'entité</strong> : Les moteurs de recherche modernes utilisent des modèles de compréhension du langage qui identifient les entités (personnes, lieux, concepts) et leurs relations. Un maillage interne structuré par clusters aide ces algorithmes à comprendre les relations entre vos contenus.
                    </li>
                    <li>
                        <strong>Principe de co-occurrence</strong> : Les termes et sujets qui apparaissent fréquemment ensemble sur des pages connectées renforcent mutuellement leur pertinence thématique.
                    </li>
                </ol>
                
                <h3>Identification des clusters dans le plugin</h3>
                <p>Le plugin RXG SMI identifie les clusters thématiques en utilisant plusieurs méthodes :</p>
                <ul>
                    <li><strong>Analyse taxonomique</strong> : Regroupement basé sur les catégories, tags et taxonomies personnalisées partagées</li>
                    <li><strong>Analyse sémantique</strong> : Identification des similarités de contenu via des techniques de NLP (Natural Language Processing)</li>
                    <li><strong>Analyse des liens existants</strong> : Détection des groupes de pages déjà fortement interconnectées</li>
                    <li><strong>Analyse hiérarchique</strong> : Prise en compte des relations parent-enfant dans la structure du site</li>
                </ul>
                
                <h3>Optimisation des clusters</h3>
                <p>Pour renforcer vos clusters thématiques, le plugin recommande :</p>
                <ul>
                    <li>Créer un maillage complet entre les pages d'un même cluster (idéalement, chaque page devrait être liée à plusieurs autres pages du cluster)</li>
                    <li>Utiliser des textes d'ancre riches en mots-clés thématiques pour les liens internes au cluster</li>
                    <li>Identifier les opportunités de créer de nouvelles pages pour compléter des clusters existants</li>
                    <li>Établir des liens entre clusters connexes via des pages "passerelles"</li>
                </ul>
            </section>
            
            <section id="semantique">
                <h2>Analyse sémantique</h2>
                <h3>Concept et innovation</h3>
                <p>L'analyse sémantique va au-delà des simples correspondances de mots-clés pour comprendre le sens et les relations conceptuelles entre les contenus. Cette approche reflète l'évolution des moteurs de recherche vers une compréhension plus contextuelle et naturelle du langage.</p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Traitement du Langage Naturel (NLP)</strong> : Le plugin utilise des techniques de NLP pour extraire le sens des contenus, identifier les entités et comprendre les relations sémantiques.
                    </li>
                    <li>
                        <strong>Modèles vectoriels</strong> : Les textes sont convertis en vecteurs numériques dans un espace sémantique, permettant de calculer la proximité conceptuelle entre différents contenus.
                    </li>
                    <li>
                        <strong>Méthode TF-IDF</strong> : Cette approche statistique (Term Frequency-Inverse Document Frequency) permet d'identifier les termes les plus significatifs pour chaque page par rapport à l'ensemble du site.
                    </li>
                    <li>
                        <strong>Similarité cosinus</strong> : Cette mesure mathématique détermine à quel point deux contenus sont conceptuellement proches en comparant leurs vecteurs de termes.
                    </li>
                </ol>
                
                <h3>Comprendre le TF-IDF en détail</h3>
                <p>TF-IDF sert à repérer les mots importants dans un texte, sans se fier juste à leur nombre d'apparitions.</p>
                <ul>
                    <li><strong>TF (Term Frequency)</strong> : compte combien de fois un mot apparaît dans une page.</li>
                    <li><strong>IDF (Inverse Document Frequency)</strong> : regarde à quel point ce mot est rare dans l'ensemble du site ou d'un groupe de textes.</li>
                </ul>
                
                <p><strong>L'idée :</strong></p>
                <ul>
                    <li>Si un mot est très fréquent sur une seule page mais rare ailleurs, il est important pour cette page.</li>
                    <li>Si un mot est fréquent partout, il a peu de valeur (exemple : "service", "entreprise").</li>
                </ul>
                
                <p><strong>Pourquoi c'est utile en SEO :</strong></p>
                <ul>
                    <li>Ça aide à savoir quels mots sont forts et distinctifs sur une page.</li>
                    <li>Ça permet de repérer les mots qu'il faudrait ajouter pour mieux répondre aux attentes de Google.</li>
                </ul>
                
                <p><strong>Exemple rapide :</strong></p>
                <p>Sur un site d'expert-comptable, si le mot "bilan annuel" apparaît souvent sur une page mais pas ailleurs, TF-IDF dira :</p>
                <p>➔ "bilan annuel" est un mot important pour cette page.</p>
                
                <h3>Comprendre le nuage de termes</h3>
                <p>Dans l'onglet "Termes fréquents" de la section "Analyse Sémantique" du plugin, vous verrez un nuage de termes avec des chiffres. Voici comment l'interpréter :</p>
                
                <ul>
                    <li><strong>Les termes</strong> : Ce sont les mots ou expressions qui ont été identifiés comme pertinents pour votre site, après filtrage des mots vides (comme "le", "la", "et", etc.).</li>
                    
                    <li><strong>Les chiffres entre parenthèses</strong> : Ces chiffres indiquent généralement le nombre de pages où le terme apparaît sur votre site. Par exemple, "service (75)" signifie que le terme "service" est présent dans 75 pages différentes.</li>
                    
                    <li><strong>La taille des termes</strong> : Attention, la taille du terme dans le nuage n'est PAS directement liée au chiffre entre parenthèses. Elle est déterminée par le poids sémantique calculé par l'algorithme TF-IDF, qui prend en compte d'autres facteurs comme la rareté du terme par rapport aux autres sites et son importance dans le contexte de votre site spécifique. Un terme peut donc apparaître dans beaucoup de pages (chiffre élevé) mais être affiché en petit s'il est considéré comme moins distinctif pour votre contenu.</li>
                </ul>
                
                <h4>Exemple réel d'interprétation</h4>
                <p>Prenons cet exemple de nuage de termes pour un site d'expertise comptable :</p>
                <ul>
                    <li>Termes en grande taille : <strong>service (75)</strong>, <strong>équipe (65)</strong>, <strong>l'externalisation (61)</strong> - Ces termes sont considérés comme très distinctifs et importants pour ce site, même si certains apparaissent dans moins de pages que d'autres termes affichés plus petits.</li>
                    
                    <li>Termes en taille moyenne : <strong>externalisation (53)</strong>, <strong>entreprise (92)</strong>, <strong>besoins (98)</strong> - Notez que "besoins" apparaît dans 98 pages mais est affiché en taille moyenne, car l'algorithme le considère comme moins distinctif que "service".</li>
                    
                    <li>Termes en petite taille : <strong>gestion (130)</strong>, <strong>comptabilité (23)</strong> - "Gestion" apparaît dans 130 pages mais est en petit car ce terme est probablement très courant sur de nombreux sites et donc moins distinctif pour ce site spécifique.</li>
                </ul>
                
                <h4>Comment utiliser ces informations</h4>
                <p>Le nuage de termes vous aide à :</p>
                <ol>
                    <li><strong>Identifier vos termes distinctifs</strong> : Les termes les plus grands révèlent les sujets pour lesquels votre site est potentiellement le plus distinctif, indépendamment de leur fréquence absolue</li>
                    <li><strong>Comprendre votre positionnement sémantique</strong> : Cette visualisation vous montre comment les algorithmes pourraient percevoir la spécificité de votre contenu</li>
                    <li><strong>Trouver des opportunités</strong> : Des termes importants (grands) qui apparaissent dans peu de pages pourraient indiquer des thématiques à développer davantage</li>
                    <li><strong>Identifier des liens thématiques potentiels</strong> : Les termes distinctifs peuvent être utilisés pour créer des liens contextuels entre différentes pages</li>
                </ol>
                
                <h3>Comprendre les clusters thématiques</h3>
                <p>Dans l'onglet "Clusters thématiques", vous verrez des groupes de pages avec des termes associés. Ces clusters sont formés automatiquement en analysant les similarités sémantiques entre vos pages. Pour chaque cluster, vous pouvez voir :</p>
                
                <ul>
                    <li><strong>Termes principaux</strong> : Les mots-clés qui caractérisent ce groupe thématique</li>
                    <li><strong>Nombre de pages</strong> : Combien de pages appartiennent à ce cluster (par exemple "12 pages")</li>
                    <li><strong>Pages représentatives</strong> : Des exemples de pages appartenant à ce cluster</li>
                </ul>
                
                <p>Ces clusters vous aident à visualiser comment vos contenus s'organisent naturellement en thématiques, indépendamment de votre structure de navigation ou de vos catégories.</p>
                
                <h3>Fonctionnalités d'analyse sémantique</h3>
                <p>Le module d'analyse sémantique du plugin offre plusieurs fonctionnalités avancées :</p>
                <ul>
                    <li><strong>Identification des termes clés</strong> : Pour chaque page, le plugin extrait les termes les plus significatifs en tenant compte de leur importance relative sur cette page et sur l'ensemble du site.</li>
                    <li><strong>Cartographie des relations sémantiques</strong> : Visualisation des relations conceptuelles entre les pages basée sur leur contenu plutôt que sur les liens existants.</li>
                    <li><strong>Détection des opportunités de maillage</strong> : Identification des paires de pages sémantiquement liées mais qui ne sont pas encore connectées par des liens.</li>
                    <li><strong>Suggestion d'ancres optimales</strong> : Proposition de textes d'ancre contextuellement pertinents basés sur l'analyse sémantique des pages source et cible.</li>
                </ul>
                
                <h3>Applications pratiques</h3>
                <p>L'analyse sémantique vous permet d'optimiser votre maillage interne de manière plus intelligente :</p>
                <ul>
                    <li>Découvrir des connections thématiques non évidentes entre vos contenus</li>
                    <li>Créer des liens basés sur la pertinence réelle plutôt que sur des correspondances de mots-clés superficielles</li>
                    <li>Renforcer la cohérence thématique globale de votre site</li>
                    <li>Améliorer votre positionnement sur des requêtes sémantiquement liées à vos sujets principaux</li>
                    <li>Offrir une meilleure expérience utilisateur en connectant des contenus véritablement complémentaires</li>
                </ul>
                
                <h3>Exemple d'utilisation pas à pas</h3>
                <ol>
                    <li><strong>Étape 1</strong> : Lancez une analyse sémantique complète via le bouton "Lancer l'analyse sémantique" dans l'interface</li>
                    <li><strong>Étape 2</strong> : Examinez le nuage de termes pour identifier vos thématiques principales</li>
                    <li><strong>Étape 3</strong> : Consultez les clusters thématiques pour voir comment vos pages sont naturellement regroupées</li>
                    <li><strong>Étape 4</strong> : Dans l'onglet "Opportunités sémantiques", repérez les paires de pages qui partagent des thématiques communes mais ne sont pas liées</li>
                    <li><strong>Étape 5</strong> : Cliquez sur "Voir suggestions" pour une page spécifique pour obtenir des recommandations de liens et de textes d'ancre optimaux</li>
                    <li><strong>Étape 6</strong> : Implémentez ces liens dans vos pages en utilisant les textes d'ancre suggérés</li>
                </ol>
            </section>
        </div>
    </div>
</div>

<style>
.rxg-smi-documentation {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.rxg-smi-doc-navigation {
    flex: 0 0 250px;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 32px; /* WP Admin bar height */
    max-height: calc(100vh - 52px);
    overflow-y: auto;
}

.rxg-smi-doc-navigation ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.rxg-smi-doc-navigation li {
    margin-bottom: 10px;
}

.rxg-smi-doc-navigation a {
    text-decoration: none;
    display: block;
    padding: 5px 10px;
    border-left: 2px solid transparent;
}

.rxg-smi-doc-navigation a:hover {
    background: #f8f9fa;
    border-left: 2px solid #2271b1;
}

.rxg-smi-doc-navigation a.active {
    background: #f0f7fc;
    border-left: 2px solid #2271b1;
    font-weight: 500;
    color: #2271b1;
}

.rxg-smi-doc-content {
    flex: 1;
    background: #fff;
    padding: 30px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-doc-content section {
    margin-bottom: 40px;
    border-bottom: 1px solid #eee;
    padding-bottom: 30px;
}

.rxg-smi-doc-content section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.rxg-smi-doc-content h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f1;
}

.rxg-smi-doc-content h3 {
    margin: 25px 0 15px;
    color: #2271b1;
}

.rxg-smi-doc-content p {
    line-height: 1.7;
}

.rxg-smi-doc-content ul, 
.rxg-smi-doc-content ol {
    margin-left: 20px;
    margin-bottom: 20px;
}

.rxg-smi-doc-content li {
    margin-bottom: 10px;
    line-height: 1.6;
}

.rxg-smi-doc-content em {
    background: #f8f9fa;
    padding: 2px 5px;
    font-style: italic;
    border-radius: 3px;
}

.rxg-smi-doc-content strong {
    color: #2271b1;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Smooth scrolling pour les liens d'ancrage
    $('.rxg-smi-doc-navigation a').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 50
        }, 500);
    });
    
    // Surligner l'élément actif dans la navigation
    $(window).on('scroll', function() {
        var scrollPosition = $(window).scrollTop();
        
        $('.rxg-smi-doc-content section').each(function() {
            var currentSection = $(this);
            var sectionTop = currentSection.offset().top - 100;
            var sectionBottom = sectionTop + currentSection.outerHeight();
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                var id = currentSection.attr('id');
                $('.rxg-smi-doc-navigation a').removeClass('active');
                $('.rxg-smi-doc-navigation a[href="#' + id + '"]').addClass('active');
            }
        });
    });
});
</script>