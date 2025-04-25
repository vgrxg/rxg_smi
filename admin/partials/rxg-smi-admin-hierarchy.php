<?php
/**
 * Template pour la visualisation de la hiérarchie des pages
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-hierarchy-summary">
        <div class="rxg-smi-hierarchy-stats">
            <div class="rxg-smi-hierarchy-stat-box">
                <span class="rxg-smi-stat-value"><?php echo intval($max_depth); ?></span>
                <span class="rxg-smi-stat-title"><?php _e('Profondeur maximale', 'rxg-smi'); ?></span>
            </div>
            
            <div class="rxg-smi-hierarchy-stat-box">
                <span class="rxg-smi-stat-value"><?php echo count($orphan_pages); ?></span>
                <span class="rxg-smi-stat-title"><?php _e('Pages orphelines', 'rxg-smi'); ?></span>
            </div>
            
            <?php
            // Compter les pages par niveau de profondeur
            global $wpdb;
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            $depths = $wpdb->get_results("
                SELECT depth, COUNT(*) as count
                FROM $table_pages
                GROUP BY depth
                ORDER BY depth
            ");
            ?>
            
            <div class="rxg-smi-hierarchy-stat-box">
                <span class="rxg-smi-stat-value">
                    <?php
                    $level_0_count = 0;
                    foreach ($depths as $depth) {
                        if ($depth->depth == 0) {
                            $level_0_count = $depth->count;
                            break;
                        }
                    }
                    echo intval($level_0_count);
                    ?>
                </span>
                <span class="rxg-smi-stat-title"><?php _e('Pages de niveau 0', 'rxg-smi'); ?></span>
            </div>
        </div>
        
        <div class="rxg-smi-depth-distribution">
            <h3><?php _e('Distribution par profondeur', 'rxg-smi'); ?></h3>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Niveau', 'rxg-smi'); ?></th>
                        <th><?php _e('Nombre de pages', 'rxg-smi'); ?></th>
                        <th><?php _e('Pourcentage', 'rxg-smi'); ?></th>
                        <th><?php _e('Actions', 'rxg-smi'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_pages = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages");
                    
                    foreach ($depths as $depth) {
                        $percentage = ($total_pages > 0) ? round(($depth->count / $total_pages) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><?php echo intval($depth->depth); ?></td>
                            <td><?php echo intval($depth->count); ?></td>
                            <td>
                                <div class="rxg-smi-percentage-bar">
                                    <div class="rxg-smi-percentage-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                    <span class="rxg-smi-percentage-text"><?php echo esc_html($percentage); ?>%</span>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-pages&min_depth=' . $depth->depth . '&max_depth=' . $depth->depth)); ?>" class="button button-small">
                                    <?php _e('Voir pages', 'rxg-smi'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="rxg-smi-hierarchy-tabs" class="rxg-smi-tabs">
        <ul class="rxg-smi-tabs-nav">
            <li><a href="#tabs-tree"><?php _e('Arborescence', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-orphans"><?php _e('Pages orphelines', 'rxg-smi'); ?></a></li>
        </ul>
        
        <div id="tabs-tree" class="rxg-smi-tab-content">
            <h3><?php _e('Arborescence du site', 'rxg-smi'); ?></h3>
            
            <div class="rxg-smi-tree-container">
                <p><?php _e('L\'arborescence ci-dessous montre la structure hiérarchique des pages de votre site.', 'rxg-smi'); ?></p>
                
                <?php if (!empty($tree)) : ?>
                    <?php echo $this->hierarchy_analyzer->render_page_tree($tree); ?>
                <?php else : ?>
                    <p><?php _e('Aucune structure hiérarchique n\'a été détectée.', 'rxg-smi'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div id="tabs-orphans" class="rxg-smi-tab-content">
            <h3><?php _e('Pages orphelines', 'rxg-smi'); ?></h3>
            
            <p>
                <?php _e('Les pages orphelines sont des pages qui ne reçoivent aucun lien interne. Elles sont difficiles à découvrir pour les utilisateurs et les moteurs de recherche.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($orphan_pages)) : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Titre', 'rxg-smi'); ?></th>
                            <th><?php _e('Type', 'rxg-smi'); ?></th>
                            <th><?php _e('Profondeur', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orphan_pages as $page) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url($page->url); ?>" target="_blank">
                                        <?php echo esc_html($page->title); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($page->post_type); ?></td>
                                <td><?php echo intval($page->depth); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities&page_id=' . $page->id)); ?>" class="button button-small">
                                        <?php _e('Suggestions', 'rxg-smi'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $page->post_id . '&action=edit')); ?>" class="button button-small">
                                        <?php _e('Éditer', 'rxg-smi'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('Félicitations ! Aucune page orpheline n\'a été détectée sur votre site.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="rxg-smi-hierarchy-recommendations">
        <h3><?php _e('Recommandations', 'rxg-smi'); ?></h3>
        
        <div class="rxg-smi-recommendation-boxes">
            <?php
            // Recommandation pour les pages trop profondes
            $deep_pages_count = 0;
            foreach ($depths as $depth) {
                if ($depth->depth > 3) {
                    $deep_pages_count += $depth->count;
                }
            }
            
            if ($deep_pages_count > 0) {
                ?>
                <div class="rxg-smi-recommendation-box rxg-smi-warning">
                    <h4>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Pages trop profondes', 'rxg-smi'); ?>
                    </h4>
                    <p>
                        <?php printf(
                            __('Vous avez %d pages à une profondeur supérieure à 3 niveaux. Les pages trop profondes sont plus difficiles à découvrir et à indexer.', 'rxg-smi'),
                            $deep_pages_count
                        ); ?>
                    </p>
                    <p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-pages&min_depth=4')); ?>" class="button button-small">
                            <?php _e('Voir ces pages', 'rxg-smi'); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
            
            // Recommandation pour les pages orphelines
            if (count($orphan_pages) > 0) {
                ?>
                <div class="rxg-smi-recommendation-box rxg-smi-warning">
                    <h4>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Pages orphelines', 'rxg-smi'); ?>
                    </h4>
                    <p>
                        <?php printf(
                            __('Vous avez %d pages orphelines qui ne reçoivent aucun lien interne. Il est recommandé de créer des liens vers ces pages.', 'rxg-smi'),
                            count($orphan_pages)
                        ); ?>
                    </p>
                    <p>
                        <a href="#tabs-orphans" class="rxg-smi-tab-link button button-small">
                            <?php _e('Voir les pages orphelines', 'rxg-smi'); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
            
            // Recommandation pour la structure plate ou profonde
            if ($max_depth <= 2 && $total_pages > 10) {
                ?>
                <div class="rxg-smi-recommendation-box rxg-smi-info">
                    <h4>
                        <span class="dashicons dashicons-info"></span>
                        <?php _e('Structure plate', 'rxg-smi'); ?>
                    </h4>
                    <p>
                        <?php _e('Votre site a une structure plutôt plate. Une structure bien organisée peut faciliter la navigation et améliorer l\'expérience utilisateur.', 'rxg-smi'); ?>
                    </p>
                    <p>
                        <?php _e('Envisagez de créer des pages de catégorie ou des sections thématiques pour mieux organiser votre contenu.', 'rxg-smi'); ?>
                    </p>
                </div>
                <?php
            } elseif ($max_depth >= 5) {
                ?>
                <div class="rxg-smi-recommendation-box rxg-smi-warning">
                    <h4>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Structure trop profonde', 'rxg-smi'); ?>
                    </h4>
                    <p>
                        <?php _e('Votre site a une structure très profonde avec des pages jusqu\'à 5 niveaux ou plus. Cela peut compliquer la navigation et l\'indexation.', 'rxg-smi'); ?>
                    </p>
                    <p>
                        <?php _e('Envisagez de réorganiser certaines sections pour réduire la profondeur maximale à 3-4 niveaux.', 'rxg-smi'); ?>
                    </p>
                </div>
                <?php
            } else {
                ?>
                <div class="rxg-smi-recommendation-box rxg-smi-success">
                    <h4>
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php _e('Bonne structure', 'rxg-smi'); ?>
                    </h4>
                    <p>
                        <?php _e('Votre site présente une bonne structure de profondeur (entre 3 et 4 niveaux), ce qui est généralement optimal pour l\'UX et le SEO.', 'rxg-smi'); ?>
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<style>
.rxg-smi-hierarchy-summary {
    margin: 20px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.rxg-smi-hierarchy-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    flex: 1;
    min-width: 300px;
}

.rxg-smi-hierarchy-stat-box {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: center;
    flex: 1;
    min-width: 120px;
}

.rxg-smi-hierarchy-stat-box .rxg-smi-stat-value {
    display: block;
    font-size: 36px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #2271b1;
}

.rxg-smi-hierarchy-stat-box .rxg-smi-stat-title {
    display: block;
    font-size: 14px;
    color: #50575e;
}

.rxg-smi-depth-distribution {
    flex: 2;
    min-width: 500px;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-percentage-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.rxg-smi-percentage-fill {
    height: 100%;
    background: #2271b1;
    border-radius: 10px;
}

.rxg-smi-percentage-text {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    text-align: center;
    line-height: 20px;
    color: #fff;
    font-weight: 500;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
}

.rxg-smi-tabs {
    margin-top: 30px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-tree-container {
    margin: 20px 0;
    max-height: 500px;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.rxg-smi-page-tree {
    margin: 0;
    padding: 0;
    list-style: none;
}

.rxg-smi-page-tree ul {
    margin-left: 20px;
    padding: 0;
    list-style: none;
}

.rxg-smi-page-tree li {
    margin: 5px 0;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.rxg-smi-page-tree li:last-child {
    border-bottom: none;
}

.depth-0 {
    font-weight: bold;
}

.rxg-smi-hierarchy-recommendations {
    margin-top: 30px;
}

.rxg-smi-recommendation-boxes {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.rxg-smi-recommendation-box {
    flex: 1;
    min-width: 250px;
    padding: 15px;
    border-radius: 5px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-recommendation-box h4 {
    margin-top: 0;
    display: flex;
    align-items: center;
}

.rxg-smi-recommendation-box h4 .dashicons {
    margin-right: 5px;
}

.rxg-smi-warning {
    border-left: 4px solid #ffb900;
}

.rxg-smi-warning h4 .dashicons {
    color: #ffb900;
}

.rxg-smi-info {
    border-left: 4px solid #00a0d2;
}

.rxg-smi-info h4 .dashicons {
    color: #00a0d2;
}

.rxg-smi-success {
    border-left: 4px solid #46b450;
}

.rxg-smi-success h4 .dashicons {
    color: #46b450;
}
</style>

<script>
jQuery(document).ready(function($) {
    $("#rxg-smi-hierarchy-tabs").tabs();
    
    $(".rxg-smi-tab-link").on("click", function(e) {
        e.preventDefault();
        var tabId = $(this).attr("href");
        $("#rxg-smi-hierarchy-tabs").tabs("option", "active", $("#rxg-smi-hierarchy-tabs .rxg-smi-tabs-nav a[href='" + tabId + "']").parent().index());
    });
});
</script>
