<?php
/**
 * Template pour le tableau de bord principal - Mise à jour Phase 2
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php
    // Afficher un message après une analyse
    if (isset($_GET['analyzed']) && $_GET['analyzed'] == 1) {
        $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php printf(__('Analyse terminée ! %d pages ont été analysées.', 'rxg-smi'), $count); ?></p>
        </div>
        <?php
    }
    ?>
    
    <div class="rxg-smi-dashboard-header">
        <div class="rxg-smi-dashboard-actions">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('rxg_smi_analyze_site', 'rxg_smi_nonce'); ?>
                <input type="hidden" name="action" value="rxg_smi_analyze_site">
                <button type="submit" id="rxg-smi-analyze-button" class="button button-primary">
                    <?php _e('Analyser le site maintenant', 'rxg-smi'); ?>
                </button>
            </form>
        </div>
        
        <div class="rxg-smi-dashboard-info">
            <div class="rxg-smi-stat-box">
                <span class="rxg-smi-stat-title"><?php _e('Pages analysées', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo intval($page_count); ?></span>
            </div>
            
            <div class="rxg-smi-stat-box">
                <span class="rxg-smi-stat-title"><?php _e('Liens totaux', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo intval($link_count); ?></span>
            </div>
            
            <div class="rxg-smi-stat-box">
                <span class="rxg-smi-stat-title"><?php _e('Liens internes', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo intval($internal_links); ?></span>
            </div>
            
            <div class="rxg-smi-stat-box">
                <span class="rxg-smi-stat-title"><?php _e('Liens externes', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo intval($external_links); ?></span>
            </div>
        </div>
    </div>
    
    <div class="rxg-smi-dashboard-status">
        <h2><?php _e('Statut de l\'analyse', 'rxg-smi'); ?></h2>
        
        <?php if ($last_analyzed) : ?>
            <p><?php printf(__('Dernière analyse : %s', 'rxg-smi'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($last_analyzed))); ?></p>
        <?php else : ?>
            <p><?php _e('Aucune analyse n\'a encore été effectuée.', 'rxg-smi'); ?></p>
        <?php endif; ?>
        
        <h3><?php _e('Prochaine analyse planifiée', 'rxg-smi'); ?></h3>
        <?php
        $next_scheduled = wp_next_scheduled('rxg_smi_daily_analysis');
        if ($next_scheduled) {
            echo '<p>' . sprintf(__('Prochaine analyse automatique : %s', 'rxg-smi'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_scheduled)) . '</p>';
        } else {
            echo '<p>' . __('Aucune analyse automatique n\'est planifiée.', 'rxg-smi') . '</p>';
        }
        ?>
    </div>
    
    <!-- Nouvelles statistiques Phase 2 -->
    <div class="rxg-smi-dashboard-advanced-stats">
        <div class="rxg-smi-stat-row">
            <div class="rxg-smi-advanced-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-warning"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($orphan_pages); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Pages orphelines', 'rxg-smi'); ?></span>
                </div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities')); ?>" class="rxg-smi-stat-action">
                    <?php _e('Voir', 'rxg-smi'); ?>
                </a>
            </div>
            
            <div class="rxg-smi-advanced-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-networking"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($max_depth); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Profondeur max', 'rxg-smi'); ?></span>
                </div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-hierarchy')); ?>" class="rxg-smi-stat-action">
                    <?php _e('Voir', 'rxg-smi'); ?>
                </a>
            </div>
            
            <div class="rxg-smi-advanced-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-category"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($taxonomies_count); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Taxonomies', 'rxg-smi'); ?></span>
                </div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-taxonomies')); ?>" class="rxg-smi-stat-action">
                    <?php _e('Voir', 'rxg-smi'); ?>
                </a>
            </div>
            
            <div class="rxg-smi-advanced-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-tag"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($terms_count); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Termes', 'rxg-smi'); ?></span>
                </div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-taxonomies')); ?>" class="rxg-smi-stat-action">
                    <?php _e('Voir', 'rxg-smi'); ?>
                </a>
            </div>
        </div>
    </div>
    
    <div id="rxg-smi-dashboard-tabs" class="rxg-smi-tabs">
        <ul class="rxg-smi-tabs-nav">
            <li><a href="#tabs-1"><?php _e('Pages populaires', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-2"><?php _e('Textes d\'ancre', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-3"><?php _e('Clusters thématiques', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-4"><?php _e('Opportunités', 'rxg-smi'); ?></a></li>
        </ul>
        
        <div id="tabs-1" class="rxg-smi-tab-content">
            <h3><?php _e('Pages les plus liées', 'rxg-smi'); ?></h3>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Page', 'rxg-smi'); ?></th>
                        <th><?php _e('Liens entrants', 'rxg-smi'); ?></th>
                        <th><?php _e('Liens sortants', 'rxg-smi'); ?></th>
                        <th><?php _e('Profondeur', 'rxg-smi'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($top_pages) {
                        foreach ($top_pages as $page) {
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id)); ?>">
                                        <?php echo esc_html($page->title); ?>
                                    </a>
                                </td>
                                <td><?php echo intval($page->inbound_links_count); ?></td>
                                <td><?php echo intval($page->outbound_links_count); ?></td>
                                <td><?php echo intval($page->depth); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="4"><?php _e('Aucune donnée disponible.', 'rxg-smi'); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-pages')); ?>" class="button">
                    <?php _e('Voir toutes les pages', 'rxg-smi'); ?>
                </a>
            </p>
        </div>
        
        <div id="tabs-2" class="rxg-smi-tab-content">
            <h3><?php _e('Textes d\'ancre les plus utilisés', 'rxg-smi'); ?></h3>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th>
                        <th><?php _e('Occurrences', 'rxg-smi'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($top_anchors) {
                        foreach ($top_anchors as $anchor) {
                            ?>
                            <tr>
                                <td><?php echo esc_html($anchor->anchor_text); ?></td>
                                <td><?php echo intval($anchor->count); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="2"><?php _e('Aucune donnée disponible.', 'rxg-smi'); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-anchors')); ?>" class="button">
                    <?php _e('Voir l\'analyse d\'ancres', 'rxg-smi'); ?>
                </a>
            </p>
        </div>
        
        <div id="tabs-3" class="rxg-smi-tab-content">
            <h3><?php _e('Clusters thématiques', 'rxg-smi'); ?></h3>
            
            <?php if (!empty($taxonomy_clusters)) : ?>
                <div class="rxg-smi-clusters">
                    <?php foreach ($taxonomy_clusters as $taxonomy => $terms) : ?>
                        <div class="rxg-smi-cluster">
                            <h4><?php echo esc_html($taxonomy); ?></h4>
                            <ul class="rxg-smi-term-list">
                                <?php foreach ($terms as $term) : ?>
                                    <li>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-taxonomies&taxonomy=' . urlencode($taxonomy) . '&term_id=' . $term->term_id)); ?>">
                                            <?php echo esc_html($term->name); ?>
                                            <span class="rxg-smi-term-count">(<?php echo intval($term->page_count); ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php _e('Aucun cluster thématique n\'a été identifié.', 'rxg-smi'); ?></p>
            <?php endif; ?>
            
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-taxonomies')); ?>" class="button">
                    <?php _e('Explorer les taxonomies', 'rxg-smi'); ?>
                </a>
            </p>
        </div>
        
        <div id="tabs-4" class="rxg-smi-tab-content">
            <h3><?php _e('Opportunités de maillage', 'rxg-smi'); ?></h3>
            
            <div class="rxg-smi-opportunity-stats">
                <div class="rxg-smi-opportunity-box">
                    <h4><?php _e('Pages orphelines', 'rxg-smi'); ?></h4>
                    <p class="rxg-smi-big-number"><?php echo intval($orphan_pages); ?></p>
                    <p><?php _e('Pages sans liens entrants', 'rxg-smi'); ?></p>
                </div>
                
                <div class="rxg-smi-opportunity-box">
                    <h4><?php _e('Pages sans sortie', 'rxg-smi'); ?></h4>
                    <p class="rxg-smi-big-number">
                        <?php 
                    // Cette variable sera déjà définie dans class-rxg-smi-admin.php
                    if (!isset($no_outlinks)) {
                        $no_outlinks = 0;
                    }                        
                        echo intval($no_outlinks);
                        ?>
                    </p>
                    <p><?php _e('Pages sans liens sortants', 'rxg-smi'); ?></p>
                </div>
                
                <div class="rxg-smi-opportunity-box">
                    <h4><?php _e('Ancres similaires', 'rxg-smi'); ?></h4>
                    <p class="rxg-smi-big-number">
                        <?php 
                        $similar_anchors = $wpdb->get_var("
                            SELECT COUNT(*) 
                            FROM (
                                SELECT anchor_text, COUNT(*) as count 
                                FROM $table_links 
                                WHERE anchor_text != '' 
                                GROUP BY anchor_text 
                                HAVING COUNT(*) > 5
                            ) as a
                        ");
                        echo intval($similar_anchors);
                        ?>
                    </p>
                    <p><?php _e('Ancres utilisées 5+ fois', 'rxg-smi'); ?></p>
                </div>
            </div>
            
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities')); ?>" class="button button-primary">
                    <?php _e('Exploiter les opportunités', 'rxg-smi'); ?>
                </a>
            </p>
        </div>
    </div>
</div>

<style>
.rxg-smi-dashboard-advanced-stats {
    margin: 20px 0;
}

.rxg-smi-stat-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 15px;
}

.rxg-smi-advanced-stat-box {
    flex: 1;
    min-width: 200px;
    background: #fff;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
}

.rxg-smi-stat-icon {
    font-size: 30px;
    margin-right: 15px;
    color: #2271b1;
}

.rxg-smi-stat-content {
    flex: 1;
}

.rxg-smi-advanced-stat-box .rxg-smi-stat-value {
    display: block;
    font-size: 24px;
    font-weight: 600;
    line-height: 1.2;
}

.rxg-smi-advanced-stat-box .rxg-smi-stat-title {
    display: block;
    font-size: 14px;
    color: #50575e;
}

.rxg-smi-stat-action {
    padding: 5px 10px;
    background: #f0f0f1;
    border-radius: 3px;
    text-decoration: none;
}

.rxg-smi-tabs {
    margin-top: 30px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-tabs-nav {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin: 0;
    padding: 0;
    list-style: none;
}

.rxg-smi-tabs-nav li {
    margin: 0;
}

.rxg-smi-tabs-nav a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #50575e;
    font-weight: 500;
}

.rxg-smi-tabs-nav .ui-tabs-active a {
    background: #f0f0f1;
    border-bottom: 2px solid #2271b1;
    color: #2271b1;
}

.rxg-smi-tab-content {
    padding: 20px;
}

.rxg-smi-clusters {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.rxg-smi-cluster {
    flex: 1;
    min-width: 250px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 3px solid #2271b1;
}

.rxg-smi-term-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.rxg-smi-term-list li {
    margin-bottom: 5px;
}

.rxg-smi-term-count {
    color: #50575e;
    font-size: 0.9em;
}

.rxg-smi-opportunity-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.rxg-smi-opportunity-box {
    flex: 1;
    min-width: 180px;
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    border-top: 3px solid #2271b1;
}

.rxg-smi-big-number {
    font-size: 36px;
    font-weight: 600;
    margin: 10px 0;
    color: #2271b1;
}
</style>

<script>
jQuery(document).ready(function($) {
    $("#rxg-smi-dashboard-tabs").tabs();
});
</script>
