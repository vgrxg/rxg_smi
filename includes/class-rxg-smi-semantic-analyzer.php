<?php
/**
 * Classe pour l'analyse sémantique du contenu
 */
class RXG_SMI_Semantic_Analyzer {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Liste des stopwords (mots vides)
     */
    private $stopwords = array();
    
    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->db = $db;
        $this->load_stopwords();
    }
    
    /**
     * Charge les stopwords depuis un fichier
     */
    private function load_stopwords() {
        $stopwords_file = RXG_SMI_PLUGIN_DIR . 'includes/data/stopwords-fr.php';
        if (file_exists($stopwords_file)) {
            $this->stopwords = include $stopwords_file;
        }
    }
    
    /**
     * Analyse tout le contenu du site
     */
    public function analyze_site_content() {
        global $wpdb;
        
        // Récupérer toutes les pages depuis la table
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $pages = $wpdb->get_results("SELECT id, post_id FROM $table_pages");
        
        // Vérifier si les tables existent, sinon les créer
        $this->create_tables();
        
        foreach ($pages as $page) {
            $this->analyze_page_content($page->id, $page->post_id);
        }
        
        // Calculer les poids TF-IDF
        $this->calculate_tf_idf();
        
        // Calculer les similarités après avoir analysé toutes les pages
        $this->calculate_semantic_similarities();
        
        // Identifier les clusters thématiques
        $this->identify_thematic_clusters();
        
        return count($pages);
    }
    
    /**
     * Crée les tables nécessaires pour l'analyse sémantique
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        $table_semantic_similarities = $wpdb->prefix . 'rxg_smi_semantic_similarities';
        $table_semantic_clusters = $wpdb->prefix . 'rxg_smi_semantic_clusters';

        $sql = array();

        $sql[] = "CREATE TABLE IF NOT EXISTS $table_semantic_terms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) NOT NULL,
            term varchar(100) NOT NULL,
            count int(11) NOT NULL DEFAULT 0,
            weight float NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY page_id (page_id),
            KEY term (term(50))
        ) $charset_collate;";

        $sql[] = "CREATE TABLE IF NOT EXISTS $table_semantic_similarities (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id_1 bigint(20) NOT NULL,
            page_id_2 bigint(20) NOT NULL,
            similarity float NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY page_pair (page_id_1,page_id_2),
            KEY page_id_1 (page_id_1),
            KEY page_id_2 (page_id_2)
        ) $charset_collate;";
        
        $sql[] = "CREATE TABLE IF NOT EXISTS $table_semantic_clusters (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cluster_id int(11) NOT NULL,
            page_id bigint(20) NOT NULL,
            cluster_score float NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY page_cluster (page_id,cluster_id),
            KEY cluster_id (cluster_id),
            KEY page_id (page_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($sql as $query) {
            dbDelta($query);
        }
    }
    /**
     * Analyse le contenu d'une page
     */
    public function analyze_page_content($page_id, $post_id) {
        $post = get_post($post_id);
        if (!$post) return;
        
        // Collecter tout le contenu pertinent
        $content = array(
            'title' => $post->post_title,
            'content' => wp_strip_all_tags($post->post_content),
            'excerpt' => $post->post_excerpt,
            'terms' => $this->get_post_terms($post_id)
        );
        
        // Extraire les termes significatifs
        $terms = $this->extract_significant_terms($content);
        
        // Stocker les termes en base
        $this->store_page_terms($page_id, $terms);
        
        return count($terms);
    }
    
    /**
     * Extrait les termes significatifs d'un contenu
     */
    private function extract_significant_terms($content) {
        $all_text = $content['title'] . ' ' . $content['content'] . ' ' . $content['excerpt'] . ' ' . implode(' ', $content['terms']);
        
        // Tokenisation (découpage en mots)
        $words = preg_split('/\s+/', strtolower($all_text));
        
        // Filtrage des stopwords et petits mots
        $filtered_words = array();
        foreach ($words as $word) {
            $word = trim($word, '.,;:?!()[]{}"\'-');
            if (strlen($word) > 3 && !in_array($word, $this->stopwords)) {
                $filtered_words[] = $word;
            }
        }
        
        // Compter les occurrences
        $term_counts = array_count_values($filtered_words);
        
        // Supprimer les nombres
        foreach ($term_counts as $term => $count) {
            if (is_numeric($term)) {
                unset($term_counts[$term]);
            }
        }
        
        // Trier par fréquence
        arsort($term_counts);
        
        // Limiter aux 50 termes les plus fréquents
        return array_slice($term_counts, 0, 50, true);
    }
    
    /**
     * Récupère les termes de taxonomie d'un post
     */
    private function get_post_terms($post_id) {
        $taxonomies = get_object_taxonomies(get_post_type($post_id));
        $terms = array();
        
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'names'));
            if (!is_wp_error($post_terms)) {
                $terms = array_merge($terms, $post_terms);
            }
        }
        
        return $terms;
    }
    
    /**
     * Stocke les termes significatifs d'une page
     */
    private function store_page_terms($page_id, $terms) {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        
        // Supprimer les anciens termes
        $wpdb->delete($table_semantic_terms, array('page_id' => $page_id));
        
        // Insérer les nouveaux termes
        foreach ($terms as $term => $count) {
            $wpdb->insert(
                $table_semantic_terms,
                array(
                    'page_id' => $page_id,
                    'term' => $term,
                    'count' => $count,
                    'weight' => 0 // Sera mis à jour avec TF-IDF
                )
            );
        }
    }
    
    /**
     * Calcule le TF-IDF pour tous les termes
     */
    private function calculate_tf_idf() {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Nombre total de pages
        $total_pages = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages");
        
        // Pour chaque terme, calculer dans combien de pages il apparaît
        $terms = $wpdb->get_results("SELECT DISTINCT term FROM $table_semantic_terms");
        
        foreach ($terms as $term) {
            // Nombre de pages contenant ce terme
            $doc_frequency = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT page_id) FROM $table_semantic_terms WHERE term = %s",
                $term->term
            ));
            
            // Calculer l'IDF
            $idf = log($total_pages / ($doc_frequency + 1));
            
            // Pour chaque occurrence de ce terme, mettre à jour le poids TF-IDF
            $occurrences = $wpdb->get_results($wpdb->prepare(
                "SELECT id, page_id, count FROM $table_semantic_terms WHERE term = %s",
                $term->term
            ));
            
            foreach ($occurrences as $occurrence) {
                // Calculer TF (normalisation logarithmique)
                $tf = 1 + log($occurrence->count);
                
                // TF-IDF
                $tf_idf = $tf * $idf;
                
                // Mettre à jour le poids
                $wpdb->update(
                    $table_semantic_terms,
                    array('weight' => $tf_idf),
                    array('id' => $occurrence->id)
                );
            }
        }
    }
    
    /**
     * Calcule les similarités sémantiques entre pages
     */
    public function calculate_semantic_similarities() {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        $table_semantic_similarities = $wpdb->prefix . 'rxg_smi_semantic_similarities';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Récupérer toutes les pages
        $pages = $wpdb->get_results("SELECT id FROM $table_pages");
        
        // Vider la table des similarités
        $wpdb->query("TRUNCATE TABLE $table_semantic_similarities");
        
        // Traitement par lots pour éviter les timeouts
        $batch_size = 100;
        $total_pages = count($pages);
        
        for ($i = 0; $i < $total_pages; $i += $batch_size) {
            $batch = array_slice($pages, $i, $batch_size);
            
            foreach ($batch as $page1) {
                $vector1 = $this->get_page_term_vector($page1->id);
                
                for ($j = $i; $j < $total_pages; $j++) {
                    $page2 = $pages[$j];
                    
                    // Ne pas comparer une page avec elle-même
                    if ($page1->id == $page2->id) continue;
                    
                    $vector2 = $this->get_page_term_vector($page2->id);
                    $similarity = $this->calculate_cosine_similarity($vector1, $vector2);
                    
                    // Ne stocker que les similarités significatives
                    if ($similarity > 0.2) {
                        $wpdb->insert(
                            $table_semantic_similarities,
                            array(
                                'page_id_1' => $page1->id,
                                'page_id_2' => $page2->id,
                                'similarity' => $similarity
                            ),
                            array('%d', '%d', '%f')
                        );
                    }
                }
            }
        }
    }
    
    /**
     * Récupère le vecteur de termes pondérés pour une page
     */
    private function get_page_term_vector($page_id) {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        
        $terms = $wpdb->get_results($wpdb->prepare(
            "SELECT term, weight FROM $table_semantic_terms WHERE page_id = %d",
            $page_id
        ));
        
        $vector = array();
        foreach ($terms as $term) {
            $vector[$term->term] = $term->weight;
        }
        
        return $vector;
    }
    
    /**
     * Calcule la similarité cosinus entre deux vecteurs
     */
    private function calculate_cosine_similarity($vector1, $vector2) {
        // Produit scalaire
        $dot_product = 0;
        foreach ($vector1 as $term => $weight) {
            if (isset($vector2[$term])) {
                $dot_product += $weight * $vector2[$term];
            }
        }
        
        // Normes des vecteurs
        $norm1 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector1)));
        $norm2 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector2)));
        
        // Éviter les divisions par zéro
        if ($norm1 == 0 || $norm2 == 0) return 0;
        
        return $dot_product / ($norm1 * $norm2);
    }
    
    /**
     * Identifie les clusters thématiques dans le site
     * Utilise un algorithme de clustering simple basé sur les similarités
     */
    public function identify_thematic_clusters() {
        global $wpdb;
        
        $table_semantic_similarities = $wpdb->prefix . 'rxg_smi_semantic_similarities';
        $table_semantic_clusters = $wpdb->prefix . 'rxg_smi_semantic_clusters';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Vider la table des clusters
        $wpdb->query("TRUNCATE TABLE $table_semantic_clusters");
        
        // Récupérer toutes les pages
        $pages = $wpdb->get_results("SELECT id FROM $table_pages");
        
        // Initialiser les clusters
        $clusters = array();
        $page_clusters = array();
        
        // Seuil de similarité pour considérer deux pages comme appartenant au même cluster
        $similarity_threshold = 0.4;
        
        // Récupérer toutes les similarités significatives
        $similarities = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT page_id_1, page_id_2, similarity 
                FROM $table_semantic_similarities 
                WHERE similarity >= %f 
                ORDER BY similarity DESC",
                $similarity_threshold
            )
        );
        
        // Initialiser chaque page dans son propre cluster
        $cluster_id = 0;
        foreach ($pages as $page) {
            $clusters[$cluster_id] = array($page->id);
            $page_clusters[$page->id] = $cluster_id;
            $cluster_id++;
        }
        
        // Fusionner les clusters en fonction des similarités
        foreach ($similarities as $similarity) {
            $page1 = $similarity->page_id_1;
            $page2 = $similarity->page_id_2;
            
            // Si les pages sont déjà dans le même cluster, continuer
            if ($page_clusters[$page1] == $page_clusters[$page2]) {
                continue;
            }
            
            // Fusionner les clusters
            $cluster1 = $page_clusters[$page1];
            $cluster2 = $page_clusters[$page2];
            
            // Tous les pages du cluster2 vont dans le cluster1
            foreach ($clusters[$cluster2] as $page_id) {
                $clusters[$cluster1][] = $page_id;
                $page_clusters[$page_id] = $cluster1;
            }
            
            // Supprimer le cluster2
            unset($clusters[$cluster2]);
        }
        
        // Ne conserver que les clusters avec au moins 2 pages
        $valid_clusters = array();
        foreach ($clusters as $id => $cluster) {
            if (count($cluster) >= 2) {
                $valid_clusters[$id] = $cluster;
            }
        }
        
        // Stocker les résultats en base
        $new_cluster_id = 1;
        foreach ($valid_clusters as $cluster) {
            foreach ($cluster as $page_id) {
                $wpdb->insert(
                    $table_semantic_clusters,
                    array(
                        'cluster_id' => $new_cluster_id,
                        'page_id' => $page_id,
                        'cluster_score' => 1.0
                    )
                );
            }
            $new_cluster_id++;
        }
        
        return count($valid_clusters);
    }
    
    /**
     * Récupère les top termes sémantiques d'une page
     */
    public function get_page_top_terms($page_id, $limit = 20) {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT term, count, weight 
            FROM $table_semantic_terms 
            WHERE page_id = %d 
            ORDER BY weight DESC 
            LIMIT %d",
            $page_id, $limit
        ));
    }
    
    /**
     * Récupère les suggestions de liens basées sur la similarité sémantique
     */
    public function get_semantic_link_suggestions($page_id, $limit = 10) {
        global $wpdb;
        
        $table_semantic_similarities = $wpdb->prefix . 'rxg_smi_semantic_similarities';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Récupérer les pages similaires qui ne sont pas déjà liées
        $query1 = $wpdb->prepare(
            "SELECT p.id, p.title, p.url, s.similarity
            FROM $table_semantic_similarities s
            INNER JOIN $table_pages p ON p.id = s.page_id_2
            WHERE s.page_id_1 = %d
            AND NOT EXISTS (
                SELECT 1 FROM $table_links l 
                WHERE l.source_id = %d AND l.target_id = p.id
            )
            ORDER BY s.similarity DESC
            LIMIT %d",
            $page_id, $page_id, $limit
        );
        
        $query2 = $wpdb->prepare(
            "SELECT p.id, p.title, p.url, s.similarity
            FROM $table_semantic_similarities s
            INNER JOIN $table_pages p ON p.id = s.page_id_1
            WHERE s.page_id_2 = %d
            AND NOT EXISTS (
                SELECT 1 FROM $table_links l 
                WHERE l.source_id = %d AND l.target_id = p.id
            )
            ORDER BY s.similarity DESC
            LIMIT %d",
            $page_id, $page_id, $limit
        );
        
        $results1 = $wpdb->get_results($query1);
        $results2 = $wpdb->get_results($query2);
        
        // Combiner et trier les résultats
        $combined = array_merge($results1, $results2);
        usort($combined, function($a, $b) {
            return $b->similarity <=> $a->similarity;
        });
        
        // Éliminer les doublons et limiter
        $unique = array();
        $final = array();
        
        foreach ($combined as $item) {
            if (!isset($unique[$item->id])) {
                $unique[$item->id] = true;
                $final[] = $item;
                
                if (count($final) >= $limit) {
                    break;
                }
            }
        }
        
        return $final;
    }
    
    /**
     * Génère la carte thématique d'une page
     */
    public function get_page_thematic_map($page_id) {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        $table_semantic_clusters = $wpdb->prefix . 'rxg_smi_semantic_clusters';
        
        // Récupérer le cluster de la page
        $cluster_id = $wpdb->get_var($wpdb->prepare(
            "SELECT cluster_id FROM $table_semantic_clusters WHERE page_id = %d",
            $page_id
        ));
        
        if (!$cluster_id) {
            return array();
        }
        
        // Récupérer les autres pages du même cluster
        $cluster_pages = $wpdb->get_results($wpdb->prepare(
            "SELECT p.id, p.title, p.url, c.cluster_score
            FROM $table_semantic_clusters c
            INNER JOIN {$wpdb->prefix}rxg_smi_pages p ON p.id = c.page_id
            WHERE c.cluster_id = %d AND c.page_id != %d
            ORDER BY c.cluster_score DESC",
            $cluster_id, $page_id
        ));
        
        // Récupérer les termes les plus représentatifs du cluster
        $cluster_terms = $wpdb->get_results($wpdb->prepare(
            "SELECT t.term, SUM(t.weight) as total_weight
            FROM $table_semantic_terms t
            INNER JOIN $table_semantic_clusters c ON t.page_id = c.page_id
            WHERE c.cluster_id = %d
            GROUP BY t.term
            ORDER BY total_weight DESC
            LIMIT 10",
            $cluster_id
        ));
        
        return array(
            'cluster_id' => $cluster_id,
            'pages' => $cluster_pages,
            'terms' => $cluster_terms
        );
    }
    
    /**
     * Récupère tous les clusters thématiques
     */
    public function get_thematic_clusters($limit = 10) {
        global $wpdb;
        
        $table_semantic_clusters = $wpdb->prefix . 'rxg_smi_semantic_clusters';
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        
        // Récupérer les clusters avec le nombre de pages
        $clusters = $wpdb->get_results($wpdb->prepare(
            "SELECT cluster_id, COUNT(page_id) as page_count
            FROM $table_semantic_clusters
            GROUP BY cluster_id
            ORDER BY page_count DESC
            LIMIT %d",
            $limit
        ));
        
        $result = array();
        
        foreach ($clusters as $cluster) {
            // Récupérer les termes représentatifs du cluster
            $terms = $wpdb->get_results($wpdb->prepare(
                "SELECT t.term, SUM(t.weight) as total_weight
                FROM $table_semantic_terms t
                INNER JOIN $table_semantic_clusters c ON t.page_id = c.page_id
                WHERE c.cluster_id = %d
                GROUP BY t.term
                ORDER BY total_weight DESC
                LIMIT 5",
                $cluster->cluster_id
            ));
            
            $term_list = array();
            foreach ($terms as $term) {
                $term_list[] = $term->term;
            }
            
            // Récupérer quelques pages du cluster
            $pages = $wpdb->get_results($wpdb->prepare(
                "SELECT p.id, p.title, p.url
                FROM $table_semantic_clusters c
                INNER JOIN {$wpdb->prefix}rxg_smi_pages p ON p.id = c.page_id
                WHERE c.cluster_id = %d
                ORDER BY c.cluster_score DESC
                LIMIT 5",
                $cluster->cluster_id
            ));
            
            $result[] = array(
                'id' => $cluster->cluster_id,
                'page_count' => $cluster->page_count,
                'terms' => $term_list,
                'pages' => $pages
            );
        }
        
        return $result;
    }
    
    /**
     * Suggère des textes d'ancre optimaux basés sur l'analyse sémantique
     */
    public function suggest_anchor_texts($source_id, $target_id, $limit = 3) {
        global $wpdb;
        
        $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Récupérer le titre de la page cible
        $target_title = $wpdb->get_var($wpdb->prepare(
            "SELECT title FROM $table_pages WHERE id = %d",
            $target_id
        ));
        
        // Récupérer les termes communs les plus significatifs
        $query = "
            SELECT t1.term, t1.weight + t2.weight as combined_weight
            FROM $table_semantic_terms t1
            INNER JOIN $table_semantic_terms t2 ON t1.term = t2.term
            WHERE t1.page_id = %d AND t2.page_id = %d
            ORDER BY combined_weight DESC
            LIMIT 10
        ";
        
        $common_terms = $wpdb->get_results($wpdb->prepare($query, $source_id, $target_id));
        
        $suggestions = array();
        
        // Suggestion 1: Titre de la page
        $suggestions[] = array(
            'text' => $target_title,
            'source' => 'Titre de la page',
            'score' => 100
        );
        
        // Suggestions basées sur les termes communs
        if (!empty($common_terms)) {
            // Le terme le plus significatif
            $best_term = ucfirst($common_terms[0]->term);
            $suggestions[] = array(
                'text' => $best_term,
                'source' => 'Terme le plus pertinent',
                'score' => 90
            );
            
            // Combinaison de termes
            if (count($common_terms) >= 3) {
                $combined_text = ucfirst($common_terms[0]->term) . ' ' . $common_terms[1]->term . ' ' . $common_terms[2]->term;
                $suggestions[] = array(
                    'text' => $combined_text,
                    'source' => 'Combinaison de termes',
                    'score' => 85
                );
            } elseif (count($common_terms) >= 2) {
                $combined_text = ucfirst($common_terms[0]->term) . ' ' . $common_terms[1]->term;
                $suggestions[] = array(
                    'text' => $combined_text,
                    'source' => 'Combinaison de termes',
                    'score' => 85
                );
            }
            
            // Un terme descriptif avec "sur" ou similaire
            if (count($common_terms) >= 1) {
                $suggestions[] = array(
                    'text' => 'En savoir plus sur ' . $common_terms[0]->term,
                    'source' => 'Variante descriptive',
                    'score' => 80
                );
            }
        }
        
        return array_slice($suggestions, 0, $limit);
    }
}