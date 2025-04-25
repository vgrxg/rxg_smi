<?php
/**
 * Classe d'activation du plugin
 */
class RXG_SMI_Activator {

    /**
     * Méthode exécutée lors de l'activation du plugin
     */
    public static function activate() {
        // Créer les tables en base de données
        $db = new RXG_SMI_DB();
        $db->create_tables();
        
        // Créer les répertoires nécessaires
        self::create_directories();
        
        // Enregistrer la date d'activation
        update_option('rxg_smi_activation_date', current_time('mysql'));
        
        // Planifier la première analyse
        if (!wp_next_scheduled('rxg_smi_daily_analysis')) {
            wp_schedule_event(time() + 300, 'daily', 'rxg_smi_daily_analysis');
        }
        
        // Vider le cache des transients
        delete_transient('rxg_smi_cache');
    }

    /**
     * Crée les répertoires nécessaires
     */
    private static function create_directories() {
        // Créer le répertoire des exports si nécessaire
        $upload_dir = wp_upload_dir();
        $rxg_smi_dir = trailingslashit($upload_dir['basedir']) . 'rxg-smi';
        
        if (!file_exists($rxg_smi_dir)) {
            wp_mkdir_p($rxg_smi_dir);
        }
        
        // Créer un fichier .htaccess pour protéger le répertoire
        $htaccess_file = $rxg_smi_dir . '/.htaccess';
        if (!file_exists($htaccess_file)) {
            $htaccess_content = "# Sécurité des fichiers d'export RXG SMI\n";
            $htaccess_content .= "<Files \"*.json\">\n";
            $htaccess_content .= "  <IfModule mod_authz_core.c>\n";
            $htaccess_content .= "    Require all denied\n";
            $htaccess_content .= "  </IfModule>\n";
            $htaccess_content .= "  <IfModule !mod_authz_core.c>\n";
            $htaccess_content .= "    Order deny,allow\n";
            $htaccess_content .= "    Deny from all\n";
            $htaccess_content .= "  </IfModule>\n";
            $htaccess_content .= "</Files>\n";
            
            file_put_contents($htaccess_file, $htaccess_content);
        }
    }
}
