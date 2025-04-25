<?php
/**
 * Classe pour l'analyse de la hiérarchie des pages
 */
class RXG_SMI_Hierarchy_Analyzer {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Cache des profondeurs de pages
     */
    private $depth_cache = array();
    
    /**
     * Cache des parents de pages
     */
    private $parent_cache = array();
    
    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->db = $db;
    }
    


/**
 * Affiche la visualisation de la hiérarchie des pages
 */
public function display_plugin_hierarchy() {
    global $wpdb;
    
    // Tables
    $table_pages = $wpdb->prefix . 'rxg_smi_pages';
    
    // Récupérer les statistiques
    $max_depth = $this->hierarchy_analyzer->get_max_depth();
    $orphan_pages = $this->hierarchy_analyzer->get_orphan_pages();
    
    // Arbre hiérarchique
    $tree = $this->hierarchy_analyzer->build_page_tree();
    
    // Inclure le template
    include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-hierarchy.php';
}

/**
 * Affiche la visualisation des taxonomies
 */
public function display_plugin_taxonomies() {
    global $wpdb;
    
    // Récupérer la taxonomie demandée
    $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : 'category';
    $term_id = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;
    
    // Récupérer toutes les taxonomies
    $taxonomies = $this->db->get_taxonomies();
    
    // Utiliser la première taxonomie si aucune n'est spécifiée
    if (empty($taxonomies)) {
        echo '<div class="notice notice-warning"><p>' . __('Aucune taxonomie trouvée. Veuillez analyser le site.', 'rxg-smi') . '</p></div>';
        return;
    }
    
    if (empty($taxonomy) && !empty($taxonomies)) {
        $taxonomy = $taxonomies[0];
    }
    
    // Récupérer les termes de cette taxonomie
    $terms = $this->db->get_terms_by_taxonomy($taxonomy);
    
    // Récupérer les pages pour un terme spécifique
    $term_pages = array();
    if ($term_id > 0) {
        $term_pages = $this->db->get_pages_by_taxonomy($taxonomy, $term_id);
    }
    
    // Inclure le template
    include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-taxonomies.php';
}

/**
 * Affiche l'analyse des textes d'ancre
 */
public function display_plugin_anchors() {
    global $wpdb;
    
    // Récupérer les statistiques d'ancre
    $table_anchors = $wpdb->prefix . 'rxg_smi_anchor_stats';
    
    // Récupérer les ancres les plus utilisées
    $top_anchors = $this->db->get_top_anchors(20);
    
    // Récupérer les pages avec une faible diversité d'ancres
    $table_pages = $wpdb->prefix . 'rxg_smi_pages';
    $low_diversity_pages = $wpdb->get_results("
        SELECT * FROM $table_pages 
        WHERE inbound_links_count > 1 AND anchor_diversity_score < 50 
        ORDER BY anchor_diversity_score ASC 
        LIMIT 10
    ");
    
    // Récupérer les paires d'ancres similaires
    $similar_anchors = array();
    foreach ($top_anchors as $anchor1) {
        foreach ($top_anchors as $anchor2) {
            if ($anchor1->anchor_text != $anchor2->anchor_text) {
                $similarity = $this->anchor_analyzer->calculate_text_similarity($anchor1->anchor_text, $anchor2->anchor_text);
                
                if ($similarity > 80) {
                    $similar_anchors[] = array(
                        'anchor1' => $anchor1->anchor_text,
                        'anchor2' => $anchor2->anchor_text,
                        'count1' => $anchor1->total_count,
                        'count2' => $anchor2->total_count,
                        'similarity' => $similarity
                    );
                }
            }
        }
    }
    
    // Inclure le template
    include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-anchors.php';
}

    /**
     * Affiche les opportunités de maillage interne
     */
    public function display_plugin_opportunities() {
        global $wpdb;
        
        // Vérifier si une page spécifique est demandée
        $page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
        
        if ($page_id > 0) {
            // Récupérer les détails de la page
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            $page_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_pages WHERE id = %d", $page_id));
            
            // Générer des suggestions
            $taxonomy_suggestions = $this->taxonomy_analyzer->get_taxonomy_suggestions($page_id);
            $potential_links = $this->taxonomy_analyzer->get_potential_links($page_id);
            $suggested_anchors = $this->anchor_analyzer->generate_anchor_suggestions($page_id);
            
            // Inclure le template
            include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-opportunities.php';
        } else {
            // Liste des opportunités générales
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            
            // Pages orphelines
            $orphan_pages = $this->hierarchy_analyzer->get_orphan_pages();
            
            // Pages sans liens sortants
            $no_outbound_pages = $wpdb->get_results("
                SELECT * FROM $table_pages 
                WHERE outbound_links_count = 0 
                ORDER BY juice_score DESC
            ");
            
            // Pages avec ratio mots/liens élevé
            $high_ratio_pages = $wpdb->get_results("
                SELECT * FROM $table_pages 
                WHERE word_count > 500 AND outbound_links_count > 0 AND word_link_ratio > 300 
                ORDER BY word_link_ratio DESC 
                LIMIT 10
            ");
            
            // Inclure le template
            include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-opportunities.php';
        }
    }

    
    /**
     * Analyse la hiérarchie de toutes les pages indexées
     */
    public function analyze_hierarchy() {
        global $wpdb;
        
        // Récupérer toutes les pages depuis la table
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $pages = $wpdb->get_results("SELECT id, post_id FROM $table_pages");
        
        foreach ($pages as $page) {
            // Calculer et stocker la profondeur
            $depth = $this->calculate_page_depth($page->post_id);
            $parent_id = $this->get_parent_page_id($page->post_id);
            
            // Mettre à jour la base de données
            $this->db->update_page_depth($page->id, $depth, $parent_id);
        }
    }
    
    /**
     * Calcule la profondeur d'une page dans l'arborescence
     */
    public function calculate_page_depth($post_id) {
        // Vérifier si déjà en cache
        if (isset($this->depth_cache[$post_id])) {
            return $this->depth_cache[$post_id];
        }
        
        $depth = 0;
        $parent_id = wp_get_post_parent_id($post_id);
        
        // Pages sans parent (normalement la page d'accueil et autres pages de premier niveau)
        if (!$parent_id) {
            // La page d'accueil a une profondeur de 0, les autres pages de premier niveau ont 1
            if ($post_id == get_option('page_on_front') || $this->is_home_by_url($post_id)) {
                $depth = 0;
            } else {
                $depth = 1;
            }
        } else {
            // Pour les pages avec parent, calculer la profondeur récursivement
            $depth = $this->calculate_page_depth($parent_id) + 1;
        }
        
        // Mettre en cache
        $this->depth_cache[$post_id] = $depth;
        
        return $depth;
    }
    
    /**
     * Vérifie si la page est la page d'accueil en comparant son URL
     */
    private function is_home_by_url($post_id) {
        $home_url = trailingslashit(home_url());
        $page_url = trailingslashit(get_permalink($post_id));
        
        return $home_url == $page_url;
    }
    
    /**
     * Récupère l'ID de la page parente dans notre table interne
     */
    private function get_parent_page_id($post_id) {
        // Vérifier si déjà en cache
        if (isset($this->parent_cache[$post_id])) {
            return $this->parent_cache[$post_id];
        }
        
        $parent_post_id = wp_get_post_parent_id($post_id);
        
        if (!$parent_post_id) {
            return 0;
        }
        
        global $wpdb;
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        $parent_page_id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $table_pages WHERE post_id = %d", $parent_post_id)
        );
        
        // Mettre en cache
        $this->parent_cache[$post_id] = $parent_page_id ? $parent_page_id : 0;
        
        return $this->parent_cache[$post_id];
    }
    
    /**
     * Construit un arbre hiérarchique des pages
     */
    public function build_page_tree() {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Récupérer toutes les pages triées par profondeur
        $pages = $wpdb->get_results("
            SELECT id, post_id, title, parent_id, depth 
            FROM $table_pages 
            ORDER BY depth ASC, title ASC
        ");
        
        // Organiser les pages en arbre
        $tree = array();
        $page_map = array();
        
        // Construire un tableau de référence
        foreach ($pages as $page) {
            $page_map[$page->id] = $page;
        }
        
        // Construire l'arbre
        foreach ($pages as $page) {
            $page->children = array();
            
            if ($page->parent_id == 0 || !isset($page_map[$page->parent_id])) {
                // Pages de premier niveau
                $tree[] = $page;
            } else {
                // Ajouter comme enfant de la page parente
                $page_map[$page->parent_id]->children[] = $page;
            }
        }
        
        return $tree;
    }
    
    /**
     * Génère un HTML représentant l'arbre des pages
     */
    public function render_page_tree($tree, $current_depth = 0) {
        $html = '<ul class="rxg-smi-page-tree">';
        
        foreach ($tree as $page) {
            $indent = str_repeat('&mdash;', $current_depth);
            $html .= '<li class="depth-' . $page->depth . '">';
            $html .= $indent . ' <a href="' . admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id) . '">' . $page->title . '</a>';
            
            if (!empty($page->children)) {
                $html .= $this->render_page_tree($page->children, $current_depth + 1);
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }
    
    /**
     * Récupère toutes les pages orphelines (sans liens entrants)
     */
    public function get_orphan_pages() {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        return $wpdb->get_results("
            SELECT id, post_id, title, url, post_type, depth 
            FROM $table_pages 
            WHERE inbound_links_count = 0 
            ORDER BY depth ASC, title ASC
        ");
    }
    
    /**
     * Récupère les pages à une profondeur spécifique
     */
    public function get_pages_by_depth($depth) {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        return $wpdb->get_results(
            $wpdb->prepare("
                SELECT id, post_id, title, url, post_type, depth, inbound_links_count, outbound_links_count 
                FROM $table_pages 
                WHERE depth = %d 
                ORDER BY title ASC
            ", $depth)
        );
    }
    
    /**
     * Calcule la profondeur maximale du site
     */
    public function get_max_depth() {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        return $wpdb->get_var("SELECT MAX(depth) FROM $table_pages");
    }
}
