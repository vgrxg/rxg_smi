<?php
/**
 * Classe de gestion de la base de données - Mise à jour Phase 2
 */
class RXG_SMI_DB {

    /**
     * Nom de la table des pages
     */
    private $table_pages;

    /**
     * Nom de la table des liens
     */
    private $table_links;

    /**
     * Nom de la table des termes
     */
    private $table_terms;

    /**
     * Nom de la table des statistiques d'ancres
     */
    private $table_anchors;

    /**
     * Constructeur
     */
    public function __construct() {
        global $wpdb;
        $this->table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $this->table_links = $wpdb->prefix . 'rxg_smi_links';
        $this->table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        $this->table_anchors = $wpdb->prefix . 'rxg_smi_anchor_stats';
    }

    /**
     * Création des tables en base de données
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table des pages avec nouveaux champs pour Phase 2
        $sql_pages = "CREATE TABLE $this->table_pages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            url varchar(255) NOT NULL,
            title varchar(255) NOT NULL,
            meta_description text,
            h1 varchar(255),
            word_count int(11) DEFAULT 0,
            post_type varchar(50) NOT NULL,
            juice_score float DEFAULT 0,
            inbound_links_count int(11) DEFAULT 0,
            outbound_links_count int(11) DEFAULT 0,
            depth int(11) DEFAULT 0,
            parent_id bigint(20) DEFAULT 0,
            anchor_diversity_score float DEFAULT 0,
            word_link_ratio float DEFAULT 0,
            last_crawled datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY post_id (post_id),
            KEY post_type (post_type),
            KEY juice_score (juice_score),
            KEY depth (depth),
            KEY parent_id (parent_id)
        ) $charset_collate;";

        // Table des liens avec position améliorée
        $sql_links = "CREATE TABLE $this->table_links (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            source_id bigint(20) NOT NULL,
            target_id bigint(20),
            target_url varchar(255) NOT NULL,
            anchor_text text,
            link_text text,
            context text,
            nofollow tinyint(1) DEFAULT 0,
            sponsored tinyint(1) DEFAULT 0,
            ugc tinyint(1) DEFAULT 0,
            http_status int(11) DEFAULT 0,
            position varchar(50),
            section varchar(50),
            weight float DEFAULT 1.0,
            external tinyint(1) DEFAULT 0,
            PRIMARY KEY  (id),
            KEY source_id (source_id),
            KEY target_id (target_id),
            KEY external (external),
            KEY position (position),
            KEY section (section)
        ) $charset_collate;";

        // Nouvelle table des termes de taxonomie
        $sql_terms = "CREATE TABLE $this->table_terms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) NOT NULL,
            post_id bigint(20) NOT NULL,
            taxonomy varchar(50) NOT NULL,
            term_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            KEY page_id (page_id),
            KEY post_id (post_id),
            KEY taxonomy (taxonomy),
            KEY term_id (term_id)
        ) $charset_collate;";

        // Nouvelle table pour les statistiques d'ancres
        $sql_anchors = "CREATE TABLE $this->table_anchors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) NOT NULL,
            anchor_text varchar(255) NOT NULL,
            count int(11) DEFAULT 0,
            variations int(11) DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY page_anchor (page_id, anchor_text(191)),
            KEY page_id (page_id)
        ) $charset_collate;";

        // Utilisation de dbDelta pour créer ou mettre à jour les tables
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_pages);
        dbDelta($sql_links);
        dbDelta($sql_terms);
        dbDelta($sql_anchors);
    }

    /**
     * Mise à jour du schéma (pour les mises à jour du plugin)
     */
    public function update_schema() {
        // Vérifier si les nouvelles colonnes existent déjà
        global $wpdb;
        
        // Vérifier si la colonne 'depth' existe dans la table des pages
        $depth_column_exists = $wpdb->get_results(
            "SHOW COLUMNS FROM {$this->table_pages} LIKE 'depth'"
        );
        
        if (empty($depth_column_exists)) {
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD COLUMN depth int(11) DEFAULT 0");
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD COLUMN parent_id bigint(20) DEFAULT 0");
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD COLUMN anchor_diversity_score float DEFAULT 0");
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD COLUMN word_link_ratio float DEFAULT 0");
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD INDEX (depth)");
            $wpdb->query("ALTER TABLE {$this->table_pages} ADD INDEX (parent_id)");
        }
        
        // Vérifier si la colonne 'section' existe dans la table des liens
        $section_column_exists = $wpdb->get_results(
            "SHOW COLUMNS FROM {$this->table_links} LIKE 'section'"
        );
        
        if (empty($section_column_exists)) {
            $wpdb->query("ALTER TABLE {$this->table_links} ADD COLUMN section varchar(50)");
            $wpdb->query("ALTER TABLE {$this->table_links} ADD COLUMN weight float DEFAULT 1.0");
            $wpdb->query("ALTER TABLE {$this->table_links} ADD INDEX (section)");
        }
        
        // Créer les nouvelles tables si elles n'existent pas
        $this->create_tables();
    }



    /**
     * Met à jour les compteurs de liens pour une page
     */
    public function update_link_counts($page_id) {
        global $wpdb;
        
        // Compter les liens sortants
        $outbound_count = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $this->table_links WHERE source_id = %d", $page_id)
        );
        
        // Compter les liens entrants
        $inbound_count = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $this->table_links WHERE target_id = %d", $page_id)
        );
        
        // Mettre à jour les compteurs
        $wpdb->update(
            $this->table_pages,
            array(
                'outbound_links_count' => $outbound_count,
                'inbound_links_count' => $inbound_count
            ),
            array('id' => $page_id)
        );
    }



    /**
     * Sauvegarde les données d'un lien
     */
    public function save_link($link_data) {
        global $wpdb;
        
        // Vérifier si le lien existe déjà
        $existing_link = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $this->table_links WHERE source_id = %d AND target_url = %s",
                $link_data['source_id'],
                $link_data['target_url']
            )
        );
        
        if ($existing_link) {
            // Mise à jour
            $wpdb->update(
                $this->table_links,
                $link_data,
                array('id' => $existing_link->id)
            );
            return $existing_link->id;
        } else {
            // Insertion
            $wpdb->insert($this->table_links, $link_data);
            return $wpdb->insert_id;
        }
    }


    /**
     * Sauvegarde les données d'une page avec les nouveaux champs
     */
    public function save_page($page_data) {
        global $wpdb;
        
        // Vérifier si la page existe déjà
        $existing_page = $wpdb->get_row(
            $wpdb->prepare("SELECT id FROM $this->table_pages WHERE post_id = %d", $page_data['post_id'])
        );
        
        if ($existing_page) {
            // Mise à jour
            $wpdb->update(
                $this->table_pages,
                $page_data,
                array('post_id' => $page_data['post_id'])
            );
            return $existing_page->id;
        } else {
            // Insertion
            $wpdb->insert($this->table_pages, $page_data);
            return $wpdb->insert_id;
        }
    }

    /**
     * Met à jour la profondeur d'une page
     */
    public function update_page_depth($page_id, $depth, $parent_id = 0) {
        global $wpdb;
        
        $wpdb->update(
            $this->table_pages,
            array(
                'depth' => $depth,
                'parent_id' => $parent_id
            ),
            array('id' => $page_id)
        );
    }

    /**
     * Met à jour le ratio mots/liens d'une page
     */
    public function update_word_link_ratio($page_id, $word_count, $outbound_links_count) {
        global $wpdb;
        
        $ratio = ($outbound_links_count > 0) ? ($word_count / $outbound_links_count) : 0;
        
        $wpdb->update(
            $this->table_pages,
            array('word_link_ratio' => $ratio),
            array('id' => $page_id)
        );
    }

    /**
     * Sauvegarde les termes d'une page
     */
    public function save_page_terms($terms_data) {
        global $wpdb;
        
        // Supprimer les termes existants pour cette page
        $wpdb->delete(
            $this->table_terms,
            array('page_id' => $terms_data[0]['page_id'])
        );
        
        // Insérer les nouveaux termes
        foreach ($terms_data as $term_data) {
            $wpdb->insert($this->table_terms, $term_data);
        }
    }

    /**
     * Récupère les termes d'une page
     */
    public function get_page_terms($page_id) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $this->table_terms WHERE page_id = %d", $page_id)
        );
    }

    /**
     * Récupère les pages par taxonomie
     */
    public function get_pages_by_taxonomy($taxonomy, $term_id) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.* FROM $this->table_pages p
                INNER JOIN $this->table_terms t ON p.id = t.page_id
                WHERE t.taxonomy = %s AND t.term_id = %d",
                $taxonomy,
                $term_id
            )
        );
    }

    /**
     * Sauvegarde les statistiques d'ancre
     */
    public function save_anchor_stats($target_id, $anchor_text) {
        global $wpdb;
        
        // Normaliser le texte d'ancre (supprimer espaces inutiles, mettre en minuscules)
        $normalized_anchor = strtolower(trim($anchor_text));
        
        // Ignorer les ancres vides ou trop longues
        if (empty($normalized_anchor) || strlen($normalized_anchor) > 255) {
            return;
        }
        
        // Vérifier si cette ancre existe déjà pour cette page
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, count FROM $this->table_anchors WHERE page_id = %d AND anchor_text = %s",
                $target_id,
                $normalized_anchor
            )
        );
        
        if ($existing) {
            // Incrémenter le compteur
            $wpdb->update(
                $this->table_anchors,
                array('count' => $existing->count + 1),
                array('id' => $existing->id)
            );
        } else {
            // Nouvelle ancre
            $wpdb->insert(
                $this->table_anchors,
                array(
                    'page_id' => $target_id,
                    'anchor_text' => $normalized_anchor,
                    'count' => 1,
                    'variations' => 0
                )
            );
        }
        
        // Mettre à jour le nombre de variations
        $this->update_anchor_variations($target_id);
    }

    /**
     * Met à jour le nombre de variations d'ancres pour une page
     */
    private function update_anchor_variations($page_id) {
        global $wpdb;
        
        // Compter les différentes ancres pour cette page
        $variations = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(DISTINCT anchor_text) FROM $this->table_anchors WHERE page_id = %d", $page_id)
        );
        
        // Mettre à jour chaque entrée
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $this->table_anchors SET variations = %d WHERE page_id = %d",
                $variations,
                $page_id
            )
        );
    }

    /**
     * Calcule et met à jour le score de diversité des ancres pour une page
     */
    public function update_anchor_diversity_score($page_id) {
        global $wpdb;
        
        // Récupérer le nombre total de liens entrants
        $total_links = $wpdb->get_var(
            $wpdb->prepare("SELECT inbound_links_count FROM $this->table_pages WHERE id = %d", $page_id)
        );
        
        // Si aucun lien entrant, mettre le score à 0
        if ($total_links <= 0) {
            $wpdb->update(
                $this->table_pages,
                array('anchor_diversity_score' => 0),
                array('id' => $page_id)
            );
            return;
        }
        
        // Récupérer les statistiques d'ancres
        $anchors = $wpdb->get_results(
            $wpdb->prepare("SELECT anchor_text, count FROM $this->table_anchors WHERE page_id = %d", $page_id)
        );
        
        // Calculer le score de diversité (formule simplifiée: variations / total_links)
        $unique_anchors = count($anchors);
        $diversity_score = ($unique_anchors / $total_links) * 100;
        
        // Limiter à 100%
        $diversity_score = min(100, $diversity_score);
        
        // Mettre à jour le score
        $wpdb->update(
            $this->table_pages,
            array('anchor_diversity_score' => $diversity_score),
            array('id' => $page_id)
        );
    }

    /**
     * Récupère toutes les pages avec filtres améliorés
     */
    public function get_pages($args = array()) {
        global $wpdb;
        
        $default_args = array(
            'orderby' => 'juice_score',
            'order' => 'DESC',
            'limit' => 50,
            'offset' => 0,
            'post_type' => '',
            'taxonomy' => '',
            'term_id' => 0,
            'min_depth' => '',
            'max_depth' => '',
            'min_word_count' => '',
            'max_word_count' => '',
        );
        
        $args = wp_parse_args($args, $default_args);
        
        $where = '1=1';
        
        // Filtre par type de publication
        if (!empty($args['post_type'])) {
            $where .= $wpdb->prepare(" AND p.post_type = %s", $args['post_type']);
        }
        
        // Filtres de profondeur
        if ($args['min_depth'] !== '') {
            $where .= $wpdb->prepare(" AND p.depth >= %d", intval($args['min_depth']));
        }
        
        if ($args['max_depth'] !== '') {
            $where .= $wpdb->prepare(" AND p.depth <= %d", intval($args['max_depth']));
        }
        
        // Filtres de nombre de mots
        if ($args['min_word_count'] !== '') {
            $where .= $wpdb->prepare(" AND p.word_count >= %d", intval($args['min_word_count']));
        }
        
        if ($args['max_word_count'] !== '') {
            $where .= $wpdb->prepare(" AND p.word_count <= %d", intval($args['max_word_count']));
        }
        
        // Base de la requête
        $query = "SELECT p.* FROM $this->table_pages p";
        
        // Jointure avec taxonomie si nécessaire
        if (!empty($args['taxonomy']) && $args['term_id'] > 0) {
            $query .= " INNER JOIN $this->table_terms t ON p.id = t.page_id";
            $where .= $wpdb->prepare(" AND t.taxonomy = %s AND t.term_id = %d", $args['taxonomy'], $args['term_id']);
        }
        
        // Finaliser la requête
        $query .= " WHERE $where ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset'])
        );
    }

    /**
     * Récupère toutes les taxonomies utilisées dans les pages analysées
     */
    public function get_taxonomies() {
        global $wpdb;
        
        return $wpdb->get_col("SELECT DISTINCT taxonomy FROM $this->table_terms ORDER BY taxonomy");
    }

    /**
     * Récupère tous les termes d'une taxonomie
     */
    public function get_terms_by_taxonomy($taxonomy) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT term_id, name, slug FROM $this->table_terms WHERE taxonomy = %s ORDER BY name",
                $taxonomy
            )
        );
    }

    /**
     * Récupère les statistiques d'ancre pour une page
     */
    public function get_anchor_stats($page_id) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $this->table_anchors WHERE page_id = %d ORDER BY count DESC", $page_id)
        );
    }

    /**
     * Récupère les ancres les plus utilisées sur tout le site
     */
    public function get_top_anchors($limit = 10) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT anchor_text, SUM(count) as total_count, COUNT(DISTINCT page_id) as page_count 
                FROM $this->table_anchors 
                GROUP BY anchor_text 
                ORDER BY total_count DESC 
                LIMIT %d",
                $limit
            )
        );
    }
}
