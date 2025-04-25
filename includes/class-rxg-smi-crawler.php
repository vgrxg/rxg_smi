<?php
/**
 * Classe de crawl et d'analyse du site - Mise à jour Phase 2
 */
class RXG_SMI_Crawler {

    /**
     * Instance de la base de données
     */
    protected $db;

    /**
     * Instance de l'analyseur de hiérarchie
     */
    protected $hierarchy_analyzer;

    /**
     * Instance de l'analyseur de taxonomies
     */
    protected $taxonomy_analyzer;

    /**
     * Instance de l'analyseur d'ancres
     */
    protected $anchor_analyzer;

    /**
     * Instance de l'analyseur de contenu
     */
    protected $content_analyzer;

    /**
     * URL du site
     */
    protected $site_url;

    /**
     * Types de contenu à analyser
     */
    protected $post_types;

    /**
     * Constructeur
     */
public function __construct($crawler, $db, $hierarchy_analyzer = null, $taxonomy_analyzer = null, $anchor_analyzer = null, $content_analyzer = null) {
    $this->crawler = $crawler;
    $this->db = $db;
    $this->hierarchy_analyzer = $hierarchy_analyzer;
    $this->taxonomy_analyzer = $taxonomy_analyzer;
    $this->anchor_analyzer = $anchor_analyzer;
    $this->content_analyzer = $content_analyzer;
}

    /**
     * Récupère les types de contenu à analyser
     */
    private function get_post_types_to_analyze() {
        // Récupérer les types de contenu définis dans les options
        $saved_types = get_option('rxg_smi_post_types', array());
        
        if (!empty($saved_types)) {
            return $saved_types;
        }
        
        // Par défaut, utiliser les types standards
        $default_types = array('post', 'page');
        
        // On peut ajouter d'autres types personnalisés
        $custom_types = get_post_types(array(
            'public' => true,
            '_builtin' => false
        ));
        
        return array_merge($default_types, $custom_types);
    }

    /**
     * Lance l'analyse complète du site avec les nouvelles fonctionnalités
     */
    public function analyze_site() {
        // Limiter le temps d'exécution si possible
        if (!ini_get('safe_mode')) {
            set_time_limit(600); // 10 minutes
        }
        
        // Mettre à jour le schéma de la base de données
        $this->db->update_schema();
        
        // Récupérer toutes les pages/posts
        $count = 0;
        foreach ($this->post_types as $post_type) {
            $count += $this->crawl_post_type($post_type);
        }
        
        // Analyse de la structure des liens
        $this->analyze_links();
        
        // Analyses supplémentaires de la Phase 2
        $this->perform_advanced_analysis();
        
        return $count;
    }

    /**
     * Effectue les analyses avancées de la Phase 2
     */
    protected function perform_advanced_analysis() {
        // Analyse de la hiérarchie des pages
        $this->hierarchy_analyzer->analyze_hierarchy();
        
        // Analyse des taxonomies
        $this->taxonomy_analyzer->analyze_taxonomies();
        
        // Analyse des textes d'ancre
        $this->anchor_analyzer->analyze_anchors();
        
        // Analyse du contenu
        $this->content_analyzer->analyze_content();
    }

    /**
     * Crawl un type de contenu spécifique
     */
    private function crawl_post_type($post_type) {
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1, // Tous les posts
        );
        
        $query = new WP_Query($args);
        $count = 0;
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // Analyser cette page/post
                $this->analyze_post(get_post());
                $count++;
                
                // Limiter la mémoire utilisée en libérant le cache régulièrement
                if ($count % 100 === 0) {
                    wp_cache_flush();
                }
            }
            wp_reset_postdata();
        }
        
        return $count;
    }

    /**
     * Analyse un article/page avec les nouvelles métriques
     */
    private function analyze_post($post) {
        // Récupérer les données de base
        $page_data = array(
            'post_id' => $post->ID,
            'url' => get_permalink($post->ID),
            'title' => get_the_title($post->ID),
            'meta_description' => $this->get_meta_description($post->ID),
            'h1' => $this->get_h1($post),
            'word_count' => $this->content_analyzer->count_words($post->post_content),
            'post_type' => $post->post_type,
            'depth' => 0, // Sera mis à jour par l'analyseur de hiérarchie
            'parent_id' => 0, // Sera mis à jour par l'analyseur de hiérarchie
            'last_crawled' => current_time('mysql'),
        );
        
        // Sauvegarder les données de la page
        $page_id = $this->db->save_page($page_data);
        
        // Extraire les liens du contenu
        $this->extract_links($post->post_content, $page_id, $post->ID);
        
        // Analyser la position des liens
        $this->content_analyzer->update_link_positions($page_id, $post->ID);
        
        return $page_id;
    }

    /**
     * Récupère la meta description (compatible avec Yoast et autres plugins SEO)
     */
    private function get_meta_description($post_id) {
        // Priorité à Yoast SEO
        $yoast_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (!empty($yoast_desc)) {
            return $yoast_desc;
        }
        
        // Rank Math
        $rank_math_desc = get_post_meta($post_id, 'rank_math_description', true);
        if (!empty($rank_math_desc)) {
            return $rank_math_desc;
        }
        
        // SEOPress
        $seopress_desc = get_post_meta($post_id, '_seopress_titles_desc', true);
        if (!empty($seopress_desc)) {
            return $seopress_desc;
        }
        
        // Extrait de l'article par défaut
        return get_the_excerpt($post_id);
    }

    /**
     * Récupère le H1 de la page (si disponible)
     */
    private function get_h1($post) {
        // Si le titre est dans un H1, on le prend par défaut
        return get_the_title($post->ID);
    }

    /**
     * Extrait les liens d'un contenu avec les nouvelles métriques
     */
    private function extract_links($content, $page_id, $post_id) {
        // Utiliser DOMDocument pour extraire les liens
        if (empty($content)) {
            return;
        }
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Éviter les erreurs HTML
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        
        $links = $dom->getElementsByTagName('a');
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            
            // Ignorer les liens vides ou les ancres
            if (empty($href) || substr($href, 0, 1) === '#') {
                continue;
            }
            
            // Déterminer la section et la position du lien
            $position = $this->determine_link_position($link, $dom);
            $section = $this->determine_link_section($link, $dom);
            $weight = $this->calculate_link_weight($position, $section);
            
            // Préparer les données du lien
            $link_data = array(
                'source_id' => $page_id,
                'target_url' => $href,
                'anchor_text' => $link->textContent,
                'link_text' => $dom->saveHTML($link),
                'context' => $this->get_link_context($link),
                'nofollow' => strpos($link->getAttribute('rel'), 'nofollow') !== false ? 1 : 0,
                'sponsored' => strpos($link->getAttribute('rel'), 'sponsored') !== false ? 1 : 0,
                'ugc' => strpos($link->getAttribute('rel'), 'ugc') !== false ? 1 : 0,
                'position' => $position,
                'section' => $section,
                'weight' => $weight,
                'external' => $this->is_external_link($href) ? 1 : 0,
            );
            
            // Vérifier l'état HTTP pour les liens externes
            if ($link_data['external']) {
                $link_data['http_status'] = $this->check_http_status($href);
            } else {
                // Pour les liens internes, trouver l'ID de la page cible
                $target_post_id = url_to_postid($href);
                if ($target_post_id) {
                    $link_data['target_id'] = $this->get_page_id_by_post_id($target_post_id);
                }
            }
            
            // Sauvegarder le lien
            $this->db->save_link($link_data);
        }
        
        // Mettre à jour les compteurs
        $this->db->update_link_counts($page_id);
    }

    /**
     * Récupère le contexte d'un lien (texte avant et après)
     */
    private function get_link_context($link_node) {
        // Récupérer le parent du lien (souvent un paragraphe)
        $parent = $link_node->parentNode;
        
        if ($parent) {
            $context = $parent->textContent;
            // Limiter la taille du contexte
            if (strlen($context) > 255) {
                $context = substr($context, 0, 252) . '...';
            }
            return $context;
        }
        
        return '';
    }

    /**
     * Détermine la position d'un lien (titre, paragraphe, liste, etc.)
     */
    private function determine_link_position($link, $dom) {
        $parent = $link->parentNode;
        
        // Vérifier si le lien est dans un titre
        $headings = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($headings as $heading) {
            if ($this->is_node_or_ancestor_tag($parent, $heading)) {
                return 'heading';
            }
        }
        
        // Vérifier si le lien est dans une liste
        if ($this->is_node_or_ancestor_tag($parent, 'li')) {
            return 'list';
        }
        
        // Vérifier si le lien est dans un tableau
        if ($this->is_node_or_ancestor_tag($parent, 'table')) {
            return 'table';
        }
        
        // Vérifier si le lien est dans un paragraphe
        if ($this->is_node_or_ancestor_tag($parent, 'p')) {
            return 'paragraph';
        }
        
        // Vérifier si le lien est une image
        if ($link->getElementsByTagName('img')->length > 0) {
            return 'image';
        }
        
        // Par défaut, considérer comme texte
        return 'text';
    }
    
    /**
     * Vérifie si un nœud ou l'un de ses ancêtres est d'un tag spécifique
     */
    private function is_node_or_ancestor_tag($node, $tag) {
        while ($node && $node->nodeType === XML_ELEMENT_NODE) {
            if (strtolower($node->nodeName) === $tag) {
                return true;
            }
            $node = $node->parentNode;
        }
        return false;
    }

    /**
     * Détermine la section de la page où se trouve le lien
     */
    private function determine_link_section($link, $dom) {
        $node = $link;
        
        // Remonter l'arbre DOM pour trouver les sections
        while ($node) {
            // Vérifier les attributs de classe et ID pour identifier les sections
            if ($node->hasAttributes()) {
                $class = $node->getAttribute('class');
                $id = $node->getAttribute('id');
                
                // Détecter le header
                if (
                    $node->nodeName === 'header' || 
                    stripos($class, 'header') !== false || 
                    stripos($id, 'header') !== false ||
                    stripos($class, 'menu') !== false || 
                    stripos($id, 'menu') !== false ||
                    stripos($class, 'nav') !== false || 
                    stripos($id, 'nav') !== false
                ) {
                    return 'header';
                }
                
                // Détecter le footer
                if (
                    $node->nodeName === 'footer' || 
                    stripos($class, 'footer') !== false || 
                    stripos($id, 'footer') !== false
                ) {
                    return 'footer';
                }
                
                // Détecter la sidebar
                if (
                    $node->nodeName === 'aside' || 
                    stripos($class, 'sidebar') !== false || 
                    stripos($id, 'sidebar') !== false ||
                    stripos($class, 'widget') !== false || 
                    stripos($id, 'widget') !== false
                ) {
                    return 'sidebar';
                }
                
                // Détecter le contenu principal
                if (
                    stripos($class, 'content') !== false || 
                    stripos($id, 'content') !== false ||
                    stripos($class, 'main') !== false || 
                    stripos($id, 'main') !== false ||
                    $node->nodeName === 'article' ||
                    $node->nodeName === 'main'
                ) {
                    return 'content';
                }
            }
            
            $node = $node->parentNode;
        }
        
        // Par défaut, considérer comme contenu
        return 'content';
    }

    /**
     * Calcule le poids d'un lien en fonction de sa position et section
     */
    private function calculate_link_weight($position, $section) {
        // Poids de base par section
        $section_weights = array(
            'content' => 1.0,
            'header' => 0.8,
            'sidebar' => 0.6,
            'footer' => 0.4
        );
        
        // Poids de base par position
        $position_weights = array(
            'heading' => 1.5,
            'paragraph' => 1.0,
            'list' => 0.9,
            'image' => 1.2,
            'table' => 0.8,
            'text' => 1.0
        );
        
        // Récupérer les poids
        $section_weight = isset($section_weights[$section]) ? $section_weights[$section] : 1.0;
        $position_weight = isset($position_weights[$position]) ? $position_weights[$position] : 1.0;
        
        // Calculer le poids final
        $weight = $section_weight * $position_weight;
        
        return round($weight, 2);
    }

    /**
     * Vérifie si un lien est externe
     */
    private function is_external_link($url) {
        return strpos($url, $this->site_url) === false && preg_match('/^https?:\/\//', $url);
    }

    /**
     * Vérifie l'état HTTP d'un lien avec limitation du nombre de requêtes
     */
    private function check_http_status($url) {
        static $checked_count = 0;
        static $max_checks = 50; // Limiter le nombre de vérifications externes
        
        // Initialiser avec un code "inconnu"
        $http_status = 0;
        
        // Vérifier si on a atteint la limite
        if ($checked_count >= $max_checks) {
            return $http_status;
        }
        
        $response = wp_remote_head($url, array(
            'timeout' => 5,
            'sslverify' => false,
            'redirection' => 5, // Suivre les redirections
            'user-agent' => 'Mozilla/5.0 (compatible; RXG-SMI/1.0; +' . $this->site_url . ')'
        ));
        
        $checked_count++;
        
        if (!is_wp_error($response)) {
            $http_status = wp_remote_retrieve_response_code($response);
        }
        
        return $http_status;
    }

    /**
     * Récupère l'ID interne d'une page à partir de son ID post
     */
    private function get_page_id_by_post_id($post_id) {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $page_id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $table_pages WHERE post_id = %d", $post_id)
        );
        
        return $page_id;
    }

    /**
     * Analyse les liens pour calculer des scores améliorés
     */
    private function analyze_links() {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Réinitialiser les scores de jus
        $wpdb->query("UPDATE $table_pages SET juice_score = 0");
        
        // Calcul simple pour la Phase 2 (à améliorer dans la Phase 3)
        // Score = base + (inbound_links * weight)
        
        // Score de base pour toutes les pages
        $wpdb->query("UPDATE $table_pages SET juice_score = 10");
        
        // Récupérer toutes les pages
        $pages = $wpdb->get_results("SELECT id FROM $table_pages");
        
        foreach ($pages as $page) {
            // Récupérer tous les liens entrants
            $inbound_links = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT source_id, weight 
                    FROM $table_links 
                    WHERE target_id = %d
                ", $page->id)
            );
            
            $juice_score = 10; // Score de base
            
            // Calculer le score en fonction des liens entrants et de leur poids
            foreach ($inbound_links as $link) {
                // Récupérer le score de la page source
                $source_score = $wpdb->get_var(
                    $wpdb->prepare("SELECT juice_score FROM $table_pages WHERE id = %d", $link->source_id)
                );
                
                // Ajouter au score proportionnellement au poids du lien et au score de la source
                $juice_score += ($source_score * 0.1) * $link->weight;
            }
            
            // Mettre à jour le score
            $wpdb->update(
                $table_pages,
                array('juice_score' => $juice_score),
                array('id' => $page->id)
            );
        }
    }
}
