<?php
/**
 * Classe pour l'analyse des taxonomies
 */
class RXG_SMI_Taxonomy_Analyzer {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Analyse les taxonomies pour toutes les pages indexées
     */
    public function analyze_taxonomies() {
        global $wpdb;
        
        // Récupérer toutes les pages depuis la table
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $pages = $wpdb->get_results("SELECT id, post_id FROM $table_pages");
        
        foreach ($pages as $page) {
            // Extraire et stocker les termes de taxonomie
            $terms_data = $this->extract_post_terms($page->id, $page->post_id);
            
            if (!empty($terms_data)) {
                $this->db->save_page_terms($terms_data);
            }
        }
    }
    
    /**
     * Extrait tous les termes de taxonomie d'un article
     */
    public function extract_post_terms($page_id, $post_id) {
        $terms_data = array();
        $post_type = get_post_type($post_id);
        
        if (!$post_type) {
            return $terms_data;
        }
        
        // Récupérer toutes les taxonomies pour ce type de contenu
        $taxonomies = get_object_taxonomies($post_type);
        
        if (empty($taxonomies)) {
            return $terms_data;
        }
        
        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_object_terms($post_id, $taxonomy);
            
            if (is_wp_error($terms) || empty($terms)) {
                continue;
            }
            
            foreach ($terms as $term) {
                $terms_data[] = array(
                    'page_id' => $page_id,
                    'post_id' => $post_id,
                    'taxonomy' => $taxonomy,
                    'term_id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug
                );
            }
        }
        
        return $terms_data;
    }
    
    /**
     * Récupère les termes de taxonomie regroupés pour une page
     */
    public function get_page_taxonomies($page_id) {
        $terms = $this->db->get_page_terms($page_id);
        $taxonomies = array();
        
        foreach ($terms as $term) {
            if (!isset($taxonomies[$term->taxonomy])) {
                $taxonomies[$term->taxonomy] = array();
            }
            
            $taxonomies[$term->taxonomy][] = array(
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug
            );
        }
        
        return $taxonomies;
    }
    
    /**
     * Récupère toutes les pages liées à une page par taxonomie commune
     */
    public function get_related_pages_by_taxonomy($page_id, $limit = 10) {
        global $wpdb;
        
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        
        // Récupérer les termes de cette page
        $terms = $this->db->get_page_terms($page_id);
        
        if (empty($terms)) {
            return array();
        }
        
        // Construire la liste des termes pour la requête
        $term_conditions = array();
        foreach ($terms as $term) {
            $term_conditions[] = $wpdb->prepare(
                "(taxonomy = %s AND term_id = %d)",
                $term->taxonomy,
                $term->term_id
            );
        }
        
        $term_sql = implode(' OR ', $term_conditions);
        
        // Récupérer les pages liées avec un comptage des termes communs
        $query = "
            SELECT p.id, p.post_id, p.title, p.url, p.post_type, COUNT(t.id) as shared_terms
            FROM $table_pages p
            INNER JOIN $table_terms t ON p.id = t.page_id
            WHERE t.page_id != %d AND ($term_sql)
            GROUP BY p.id
            ORDER BY shared_terms DESC, p.title ASC
            LIMIT %d
        ";
        
        return $wpdb->get_results($wpdb->prepare($query, $page_id, $limit));
    }
    
    /**
     * Vérifie si deux pages partagent au moins une taxonomie
     */
    public function share_taxonomy($page_id_1, $page_id_2) {
        global $wpdb;
        
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        
        $query = "
            SELECT COUNT(*) 
            FROM $table_terms t1
            INNER JOIN $table_terms t2 ON t1.taxonomy = t2.taxonomy AND t1.term_id = t2.term_id
            WHERE t1.page_id = %d AND t2.page_id = %d
        ";
        
        $shared = $wpdb->get_var($wpdb->prepare($query, $page_id_1, $page_id_2));
        
        return $shared > 0;
    }
    
    /**
     * Récupère les pages qui devraient être liées par taxonomie mais ne le sont pas
     */
    public function get_potential_links($page_id, $limit = 10) {
        global $wpdb;
        
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Récupérer les termes de cette page
        $terms = $this->db->get_page_terms($page_id);
        
        if (empty($terms)) {
            return array();
        }
        
        // Construire la liste des termes pour la requête
        $term_conditions = array();
        foreach ($terms as $term) {
            $term_conditions[] = $wpdb->prepare(
                "(t1.taxonomy = %s AND t1.term_id = %d)",
                $term->taxonomy,
                $term->term_id
            );
        }
        
        $term_sql = implode(' OR ', $term_conditions);
        
        // Récupérer les pages qui partagent des termes mais ne sont pas liées
        $query = "
            SELECT p.id, p.post_id, p.title, p.url, p.post_type, COUNT(t1.id) as shared_terms
            FROM $table_pages p
            INNER JOIN $table_terms t1 ON p.id = t1.page_id
            WHERE p.id != %d AND ($term_sql)
            AND NOT EXISTS (
                SELECT 1 FROM $table_links l 
                WHERE l.source_id = %d AND l.target_id = p.id
            )
            GROUP BY p.id
            ORDER BY shared_terms DESC, p.title ASC
            LIMIT %d
        ";
        
        return $wpdb->get_results($wpdb->prepare($query, $page_id, $page_id, $limit));
    }
    
    /**
     * Récupère les clusters thématiques basés sur les taxonomies
     */
    public function get_taxonomy_clusters() {
        global $wpdb;
        
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        
        // Récupérer les taxonomies les plus utilisées
        $taxonomies = $this->db->get_taxonomies();
        $clusters = array();
        
        foreach ($taxonomies as $taxonomy) {
            // Récupérer les termes avec le plus de pages
            $terms = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT term_id, name, COUNT(DISTINCT page_id) as page_count
                    FROM $table_terms
                    WHERE taxonomy = %s
                    GROUP BY term_id, name
                    HAVING page_count > 1
                    ORDER BY page_count DESC
                    LIMIT 10
                ", $taxonomy)
            );
            
            if (!empty($terms)) {
                $clusters[$taxonomy] = $terms;
            }
        }
        
        return $clusters;
    }
    
    /**
     * Récupère les suggestions de taxonomies manquantes pour une page
     */
    public function get_taxonomy_suggestions($page_id) {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Récupérer la page
        $page = $wpdb->get_row($wpdb->prepare("SELECT post_id, post_type FROM $table_pages WHERE id = %d", $page_id));
        
        if (!$page) {
            return array();
        }
        
        // Récupérer toutes les taxonomies pour ce type de contenu
        $taxonomies = get_object_taxonomies($page->post_type);
        
        if (empty($taxonomies)) {
            return array();
        }
        
        // Récupérer les taxonomies déjà utilisées
        $used_taxonomies = $wpdb->get_col(
            $wpdb->prepare("SELECT DISTINCT taxonomy FROM $table_terms WHERE page_id = %d", $page_id)
        );
        
        // Trouver les taxonomies manquantes
        $missing_taxonomies = array_diff($taxonomies, $used_taxonomies);
        
        if (empty($missing_taxonomies)) {
            return array();
        }
        
        $suggestions = array();
        
        // Pour chaque taxonomie manquante, suggérer des termes basés sur les pages liées
        foreach ($missing_taxonomies as $taxonomy) {
            // Récupérer les pages liées à celle-ci
            $linked_pages = $wpdb->get_col(
                $wpdb->prepare("
                    SELECT target_id FROM $table_links 
                    WHERE source_id = %d AND target_id IS NOT NULL
                    UNION
                    SELECT source_id FROM $table_links 
                    WHERE target_id = %d
                ", $page_id, $page_id)
            );
            
            if (empty($linked_pages)) {
                continue;
            }
            
            // Trouver les termes les plus utilisés dans ces pages pour cette taxonomie
            $linked_pages_ids = implode(',', array_map('intval', $linked_pages));
            
            $terms = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT term_id, name, COUNT(DISTINCT page_id) as usage_count
                    FROM $table_terms
                    WHERE taxonomy = %s AND page_id IN ($linked_pages_ids)
                    GROUP BY term_id, name
                    ORDER BY usage_count DESC
                    LIMIT 5
                ", $taxonomy)
            );
            
            if (!empty($terms)) {
                $suggestions[$taxonomy] = $terms;
            }
        }
        
        return $suggestions;
    }
}
