<?php
/**
 * Classe de désactivation du plugin
 */
class RXG_SMI_Deactivator {

    /**
     * Méthode exécutée lors de la désactivation du plugin
     */
    public static function deactivate() {
        // Supprimer les tâches planifiées
        wp_clear_scheduled_hook('rxg_smi_daily_analysis');
        
        // Nettoyer les transients
        delete_transient('rxg_smi_cache');
        
        // Note: Nous ne supprimons pas les tables de la base de données
        // pour ne pas perdre les données en cas de réactivation
    }
}
