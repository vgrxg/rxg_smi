<?php
/**
 * Classe pour la gestion des exports et visualisations
 */
class RXG_SMI_Visualization {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Instance de l'exportateur
     */
    private $exporter;
    
    /**
     * Constructeur
     */
    public function __construct($db, $exporter) {
        $this->db = $db;
        $this->exporter = $exporter;
        
        add_action('admin_menu', array($this, 'add_visualization_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_visualization_scripts'));
        add_action('wp_ajax_rxg_smi_get_visualization_data', array($this, 'get_visualization_data'));
    }
    
    /**
     * Ajoute la page de menu pour les visualisations
     */
    public function add_visualization_menu() {
        add_submenu_page(
            'rxg-smi',
            __('Visualisation & Export', 'rxg-smi'),
            __('Visualisation', 'rxg-smi'),
            'manage_options',
            'rxg-smi-visualization',
            array($this, 'render_visualization_page')
        );
    }
    
    /**
     * Charge les scripts pour la visualisation
     */
    public function enqueue_visualization_scripts($hook) {
        // Condition plus permissive pour capturer toutes les pages de visualisation
        if (strpos($hook, 'rxg-smi-visualization') === false && strpos($hook, 'page=rxg-smi-visualization') === false) {
            return;
        }
        
        // Log de débogage (sera visible en console)
        wp_add_inline_script('jquery', 'console.log("RXG SMI: Scripts en cours de chargement sur hook: ' . esc_js($hook) . '");', 'after');
        
        // D3.js pour les visualisations
        wp_enqueue_script(
            'rxg-smi-d3',
            'https://d3js.org/d3.v7.min.js',
            array('jquery'),
            '7.0.0',
            true
        );
        
        // Script de visualisation principal avec timestamp pour forcer le rechargement
        wp_enqueue_script(
            'rxg-smi-visualization',
            RXG_SMI_PLUGIN_URL . 'admin/js/rxg-smi-visualization.js',
            array('jquery', 'rxg-smi-d3'),
            RXG_SMI_VERSION . '.' . time(),
            true
        );
        
        // Passer les données au script (avec toutes les variables d'origine)
        wp_localize_script(
            'rxg-smi-visualization',
            'rxgSmiData',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('rxg-smi-visualization'),
                'adminUrl' => admin_url('admin.php'),
                'exportUrl' => wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_export_json'), 'rxg_smi_export', 'rxg_smi_nonce'),
                'exportCsvUrl' => wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_export_csv'), 'rxg_smi_export_csv', 'rxg_smi_nonce'),
                'i18n' => array(
                    'error' => __('Erreur lors du chargement des données', 'rxg-smi')
                ),
                'debugMode' => true
            )
        );
        
        // CSS personnalisé
        wp_enqueue_style(
            'rxg-smi-visualization-style',
            RXG_SMI_PLUGIN_URL . 'admin/css/rxg-smi-visualization.css',
            array(),
            RXG_SMI_VERSION . '.' . time()
        );
        
        // Ajouter un script de débogage
        wp_add_inline_script('rxg-smi-visualization', '
            console.log("RXG SMI: Scripts chargés avec succès");
            jQuery(document).ready(function($) {
                console.log("RXG SMI: Document ready");
            });
        ', 'after');
    }

    /**
     * Affiche la page de visualisation
     */
    public function render_visualization_page() {
        include RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-visualization.php';
    }
    
    /**
     * Endpoint AJAX pour récupérer les données pour D3.js
     */
    public function get_visualization_data() {
        // Vérifier le nonce
        check_ajax_referer('rxg-smi-visualization', 'nonce');
        
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissions insuffisantes', 'rxg-smi')));
        }
        
        // Vérifier si des données existent
        global $wpdb;
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages");
        
        if (!$count || $count == 0) {
            wp_send_json_error(array(
                'message' => __('Aucune donnée de maillage disponible. Veuillez d\'abord analyser votre site.', 'rxg-smi')
            ));
            return;
        }
        
        // Récupérer les données via l'exportateur
        $data = json_decode($this->exporter->export_maillage_json(), true);
        
        // Vérifier et corriger le format des liens
        if (isset($data['links']) && !is_array($data['links'])) {
            // Si links est un objet, le convertir en tableau
            $links_array = array();
            foreach ($data['links'] as $link) {
                $links_array[] = $link;
            }
            $data['links'] = $links_array;
        }
        
        // Limiter la taille des données pour éviter de surcharger le navigateur
        if (count($data['pages']) > 100) {
            // Trier les pages par juice_score
            usort($data['pages'], function($a, $b) {
                return $b['juice_score'] - $a['juice_score'];
            });
            
            // Garder seulement les 100 pages les plus importantes
            $data['pages'] = array_slice($data['pages'], 0, 100);
            
            // Filtrer les liens pour ne garder que ceux concernant ces pages
            $page_ids = array_map(function($page) {
                return $page['id'];
            }, $data['pages']);

            // Filtrer les liens pour ne garder que ceux qui référencent des nœuds existants
            $data['links'] = array_values(array_filter($data['links'], function($link) use ($page_ids) {
                // S'assurer que source et target existent dans les nœuds filtrés
                return in_array($link['source'], $page_ids) && in_array($link['target'], $page_ids);
            }));
        }
        
        wp_send_json_success($data);
    }
    
    /**
     * Génère un export CSV pour Gephi
     */
    public function export_csv() {
        // Vérification de sécurité
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.', 'rxg-smi'));
        }
        
        check_admin_referer('rxg_smi_export_csv', 'rxg_smi_nonce');
        
        // Récupérer les données du maillage
        $data = json_decode($this->exporter->export_maillage_json(), true);
        
        // Créer un dossier temporaire pour les fichiers CSV
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/rxg-smi-temp';
        if (!file_exists($temp_dir)) {
            wp_mkdir_p($temp_dir);
        }
        
        // Créer les fichiers CSV
        $nodes_file = $temp_dir . '/nodes.csv';
        $edges_file = $temp_dir . '/edges.csv';
        
        // Fichier des nœuds (pages)
        $nodes_handle = fopen($nodes_file, 'w');
        fputcsv($nodes_handle, ['Id', 'Label', 'Size', 'Depth', 'Type', 'Cluster', 'JuiceScore']);
        
        foreach ($data['pages'] as $page) {
            fputcsv($nodes_handle, [
                $page['id'],
                $page['title'],
                max(1, $page['inbound_links_count']), // Taille minimum 1
                $page['depth'],
                $page['type'],
                $page['cluster'],
                $page['juice_score']
            ]);
        }
        fclose($nodes_handle);
        
        // Fichier des arêtes (liens)
        $edges_handle = fopen($edges_file, 'w');
        fputcsv($edges_handle, ['Source', 'Target', 'Weight', 'Label']);
        
        foreach ($data['links'] as $link) {
            fputcsv($edges_handle, [
                $link['source'],
                $link['target'],
                $link['weight'],
                $link['anchor']
            ]);
        }
        fclose($edges_handle);
        
        // Créer un fichier ZIP
        $zip_file = $temp_dir . '/maillage-gephi-export.zip';
        $zip = new ZipArchive();
        
        if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($nodes_file, 'nodes.csv');
            $zip->addFile($edges_file, 'edges.csv');
            
            // Ajouter un README
            $readme = "# Export pour Gephi\n\n";
            $readme .= "Ce package contient deux fichiers CSV à importer dans Gephi:\n\n";
            $readme .= "1. `nodes.csv` - Les pages du site\n";
            $readme .= "2. `edges.csv` - Les liens entre les pages\n\n";
            $readme .= "## Comment importer dans Gephi\n\n";
            $readme .= "1. Ouvrez Gephi et créez un nouveau projet\n";
            $readme .= "2. Allez dans le 'Laboratoire de données'\n";
            $readme .= "3. Cliquez sur 'Importer feuille de calcul' et sélectionnez nodes.csv\n";
            $readme .= "4. Répétez pour edges.csv\n";
            
            $zip->addFromString('README.md', $readme);
            $zip->close();
            
            // Envoyer le ZIP au navigateur
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="maillage-gephi-export.zip"');
            header('Content-Length: ' . filesize($zip_file));
            readfile($zip_file);
            
            // Nettoyer les fichiers temporaires
            unlink($nodes_file);
            unlink($edges_file);
            unlink($zip_file);
            
            exit;
        } else {
            wp_die(__('Erreur lors de la création du fichier ZIP', 'rxg-smi'));
        }
    }
}
