<?php
/**
 * Template pour les paramètres du plugin
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php
    // Traiter les soumissions de formulaire
    if (isset($_POST['rxg_smi_settings_nonce']) && wp_verify_nonce($_POST['rxg_smi_settings_nonce'], 'rxg_smi_save_settings')) {
        // Sauvegarder les paramètres
        $post_types = isset($_POST['rxg_smi_post_types']) ? (array) $_POST['rxg_smi_post_types'] : array();
        $schedule = isset($_POST['rxg_smi_schedule']) ? sanitize_text_field($_POST['rxg_smi_schedule']) : 'daily';
        
        // Sauvegarder dans les options
        update_option('rxg_smi_post_types', $post_types);
        update_option('rxg_smi_schedule', $schedule);
        
        // Reprogrammer les tâches CRON
        wp_clear_scheduled_hook('rxg_smi_daily_analysis');
        
        if ($schedule !== 'manual') {
            wp_schedule_event(time(), $schedule, 'rxg_smi_daily_analysis');
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Paramètres sauvegardés avec succès.', 'rxg-smi') . '</p></div>';
    }
    
    // Récupérer les paramètres actuels
    $selected_post_types = get_option('rxg_smi_post_types', array('post', 'page'));
    $current_schedule = get_option('rxg_smi_schedule', 'daily');
    ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('rxg_smi_save_settings', 'rxg_smi_settings_nonce'); ?>
        
        <div class="rxg-smi-settings-section">
            <h2><?php _e('Paramètres généraux', 'rxg-smi'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="rxg_smi_post_types"><?php _e('Types de contenu à analyser', 'rxg-smi'); ?></label>
                    </th>
                    <td>
                        <?php
                        $post_types = get_post_types(array('public' => true), 'objects');
                        foreach ($post_types as $post_type) :
                        ?>
                            <label style="display: block; margin-bottom: 8px;">
                                <input type="checkbox" name="rxg_smi_post_types[]" value="<?php echo esc_attr($post_type->name); ?>"
                                       <?php checked(in_array($post_type->name, $selected_post_types)); ?>>
                                <?php echo esc_html($post_type->labels->name); ?>
                            </label>
                        <?php endforeach; ?>
                        <p class="description">
                            <?php _e('Sélectionnez les types de contenu que vous souhaitez inclure dans l\'analyse du maillage interne.', 'rxg-smi'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="rxg_smi_schedule"><?php _e('Fréquence d\'analyse automatique', 'rxg-smi'); ?></label>
                    </th>
                    <td>
                        <select name="rxg_smi_schedule" id="rxg_smi_schedule">
                            <option value="manual" <?php selected($current_schedule, 'manual'); ?>><?php _e('Manuel uniquement', 'rxg-smi'); ?></option>
                            <option value="hourly" <?php selected($current_schedule, 'hourly'); ?>><?php _e('Toutes les heures', 'rxg-smi'); ?></option>
                            <option value="twicedaily" <?php selected($current_schedule, 'twicedaily'); ?>><?php _e('Deux fois par jour', 'rxg-smi'); ?></option>
                            <option value="daily" <?php selected($current_schedule, 'daily'); ?>><?php _e('Une fois par jour', 'rxg-smi'); ?></option>
                            <option value="weekly" <?php selected($current_schedule, 'weekly'); ?>><?php _e('Une fois par semaine', 'rxg-smi'); ?></option>
                        </select>
                        <p class="description">
                            <?php _e('Définissez la fréquence à laquelle l\'analyse automatique du maillage interne sera effectuée.', 'rxg-smi'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="rxg-smi-settings-section">
            <h2><?php _e('Maintenance des données', 'rxg-smi'); ?></h2>
            
            <div class="rxg-smi-data-actions">
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=rxg-smi-settings&action=clear_data'), 'rxg_smi_clear_data', 'rxg_smi_nonce'); ?>" 
                       class="button button-secondary" 
                       onclick="return confirm('<?php esc_attr_e('Êtes-vous sûr de vouloir effacer toutes les données d\'analyse ? Cette action est irréversible.', 'rxg-smi'); ?>');">
                        <?php _e('Effacer toutes les données', 'rxg-smi'); ?>
                    </a>
                </p>
                <p class="description">
                    <?php _e('Cette action supprimera toutes les données d\'analyse du maillage interne. Utilisez-la si vous souhaitez repartir de zéro.', 'rxg-smi'); ?>
                </p>
            </div>
        </div>
        
        <div class="rxg-smi-settings-section">
            <h2><?php _e('Exportation des données', 'rxg-smi'); ?></h2>
            
            <div class="rxg-smi-export-actions">
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=rxg-smi-settings&action=export_json'), 'rxg_smi_export_json', 'rxg_smi_nonce'); ?>" 
                       class="button button-primary">
                        <?php _e('Exporter au format JSON', 'rxg-smi'); ?>
                    </a>
                    
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=rxg-smi-settings&action=export_csv'), 'rxg_smi_export_csv', 'rxg_smi_nonce'); ?>" 
                       class="button button-secondary">
                        <?php _e('Exporter au format CSV', 'rxg-smi'); ?>
                    </a>
                </p>
                <p class="description">
                    <?php _e('Exportez les données d\'analyse du maillage interne pour les utiliser dans d\'autres outils.', 'rxg-smi'); ?>
                </p>
            </div>
        </div>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Enregistrer les modifications', 'rxg-smi'); ?>">
        </p>
    </form>
</div>

<style>
    .rxg-smi-settings-section {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .rxg-smi-settings-section h2 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .rxg-smi-data-actions,
    .rxg-smi-export-actions {
        margin: 15px 0;
    }
</style>
