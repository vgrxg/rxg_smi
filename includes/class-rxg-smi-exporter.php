<?php
/**
 * Classe de gestion des exports de données pour visualisation
 */
class RXG_SMI_Exporter {
    
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
     * Exporte les données de maillage au format JSON
     */
    public function export_maillage_json() {
        global $wpdb;
        
        // Tables
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        $table_semantic_clusters = $wpdb->prefix . 'rxg_smi_semantic_clusters';
        
        // 1. Récupérer toutes les pages
        $pages = $wpdb->get_results("
            SELECT id, post_id, title, url, post_type, 
                   juice_score, inbound_links_count, outbound_links_count, 
                   depth, parent_id, word_count
            FROM $table_pages
        ");
        
        // 2. Récupérer tous les liens internes
        $links = $wpdb->get_results("
            SELECT source_id, target_id, anchor_text, weight, position, section
            FROM $table_links
            WHERE external = 0 AND target_id IS NOT NULL
        ");
        
        // 3. Récupérer les termes pour chaque page
        $page_terms = [];
        foreach ($pages as $page) {
            $terms = $wpdb->get_results($wpdb->prepare("
                SELECT taxonomy, name
                FROM $table_terms
                WHERE page_id = %d
            ", $page->id));
            
            if (!empty($terms)) {
                $page_terms[$page->id] = $terms;
            }
        }
        
        // 4. Récupérer les clusters pour chaque page
        $page_clusters = [];
        $cluster_query = $wpdb->prepare("
            SELECT page_id, cluster_id
            FROM $table_semantic_clusters
        ");
        
        $clusters = $wpdb->get_results($cluster_query);
        foreach ($clusters as $cluster) {
            $page_clusters[$cluster->page_id] = $cluster->cluster_id;
        }
        
        // 5. Formater les données pour l'export
        $formatted_pages = [];
        foreach ($pages as $page) {
            // Extraire les taxonomies pour cette page
            $taxonomies = [];
            if (isset($page_terms[$page->id])) {
                foreach ($page_terms[$page->id] as $term) {
                    $taxonomies[] = $term->name;
                }
            }
            
            // Déterminer le cluster
            $cluster = isset($page_clusters[$page->id]) ? 'Cluster ' . $page_clusters[$page->id] : 'Non classé';
            
            // Ajouter la page formatée
            $formatted_pages[] = [
                'id' => $page->id,
                'title' => $page->title,
                'url' => $page->url,
                'type' => $page->post_type,
                'inbound_links_count' => intval($page->inbound_links_count),
                'outbound_links_count' => intval($page->outbound_links_count),
                'juice_score' => floatval($page->juice_score),
                'depth' => intval($page->depth),
                'word_count' => intval($page->word_count),
                'taxonomies' => array_unique($taxonomies),
                'cluster' => $cluster
            ];
        }
        
        // Formater les liens
        $formatted_links = [];
        foreach ($links as $link) {
            $formatted_links[] = [
                'source' => intval($link->source_id),
                'target' => intval($link->target_id),
                'anchor' => $link->anchor_text,
                'weight' => floatval($link->weight),
                'position' => $link->position ?: 'unknown',
                'section' => $link->section ?: 'content'
            ];
        }
        
        // 6. Construire le JSON final
        $export_data = [
            'pages' => $formatted_pages,
            'links' => $formatted_links
        ];
        
        return json_encode($export_data, JSON_PRETTY_PRINT);
    }
    
    /**
     * Gère la requête d'export et le téléchargement
     */
    public function handle_export_request() {
        // Vérification de sécurité
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.', 'rxg-smi'));
        }
        
        // Générer le JSON
        $json_data = $this->export_maillage_json();
        
        // Définir les headers pour le téléchargement
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="maillage-interne-' . date('Y-m-d') . '.json"');
        header('Content-Length: ' . strlen($json_data));
        
        // Envoyer le contenu
        echo $json_data;
        exit;
    }
    
    /**
     * Affiche un bouton d'export dans l'interface d'administration
     */
    public function render_export_button() {
        $export_url = wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_export_json'), 'rxg_smi_export', 'rxg_smi_nonce');
        ?>
        <div class="rxg-smi-export-box">
            <h3><?php _e('Exporter les données pour visualisation externe', 'rxg-smi'); ?></h3>
            <p><?php _e('Téléchargez les données au format JSON pour les utiliser dans un outil de visualisation comme Gephi, Freemind ou d\'autres.', 'rxg-smi'); ?></p>
            <a href="<?php echo esc_url($export_url); ?>" class="button button-primary">
                <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                <?php _e('Télécharger les données (JSON)', 'rxg-smi'); ?>
            </a>
        </div>
        <?php
    }
}

// Ajouter cette fonction à rxg-smi.php pour initialiser l'exportateur

/**
 * Initialisation de l'exportateur
 */
function rxg_smi_init_exporter($db) {
    $exporter = new RXG_SMI_Exporter($db);
    
    // Ajouter l'action pour l'export
    add_action('admin_post_rxg_smi_export_json', array($exporter, 'handle_export_request'));
    
    // Ajouter le bouton d'export dans le tableau de bord
    add_action('rxg_smi_dashboard_after_status', array($exporter, 'render_export_button'));
}

// Ajouter l'appel à la fonction d'initialisation dans rxg_smi_init():
// rxg_smi_init_exporter($db);
