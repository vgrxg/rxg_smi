<?php
/**
 * Template pour la visualisation des taxonomies
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-taxonomy-header">
        <div class="rxg-smi-taxonomy-selector">
            <form method="get" action="">
                <input type="hidden" name="page" value="rxg-smi-taxonomies">
                <label for="rxg-smi-taxonomy-select"><?php _e('Sélectionner une taxonomie:', 'rxg-smi'); ?></label>
                <select id="rxg-smi-taxonomy-select" name="taxonomy" onchange="this.form.submit()">
                    <?php foreach ($taxonomies as $tax) : ?>
                        <option value="<?php echo esc_attr($tax); ?>" <?php selected($taxonomy, $tax); ?>>
                            <?php echo esc_html($tax); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        
        <div class="rxg-smi-taxonomy-info">
            <?php
            // Récupérer des informations sur cette taxonomie
            $tax_obj = get_taxonomy($taxonomy);
            $term_count = count($terms);
            
            // Calculer le nombre de pages pour cette taxonomie
            global $wpdb;
            $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
            $page_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT page_id) FROM $table_terms WHERE taxonomy = %s",
                $taxonomy
            ));
            ?>
            
            <div class="rxg-smi-tax-info-item">
                <span class="rxg-smi-tax-info-label"><?php _e('Nom:', 'rxg-smi'); ?></span>
                <span class="rxg-smi-tax-info-value"><?php echo isset($tax_obj->labels->singular_name) ? esc_html($tax_obj->labels->singular_name) : esc_html($taxonomy); ?></span>
            </div>
            
            <div class="rxg-smi-tax-info-item">
                <span class="rxg-smi-tax-info-label"><?php _e('Nombre de termes:', 'rxg-smi'); ?></span>
                <span class="rxg-smi-tax-info-value"><?php echo intval($term_count); ?></span>
            </div>
            
            <div class="rxg-smi-tax-info-item">
                <span class="rxg-smi-tax-info-label"><?php _e('Pages associées:', 'rxg-smi'); ?></span>
                <span class="rxg-smi-tax-info-value"><?php echo intval($page_count); ?></span>
            </div>
        </div>
    </div>
    
    <div class="rxg-smi-taxonomy-content">
        <div class="rxg-smi-terms-list">
            <h3><?php _e('Termes de la taxonomie', 'rxg-smi'); ?></h3>
            
            <?php if (!empty($terms)) : ?>
                <div class="rxg-smi-terms-grid">
                    <?php foreach ($terms as $term) : 
                        // Récupérer le nombre de pages pour ce terme
                        $term_page_count = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT page_id) FROM $table_terms WHERE taxonomy = %s AND term_id = %d",
                            $taxonomy, $term->term_id
                        ));
                        
                        // Récupérer le terme WordPress pour plus d'informations
                        $wp_term = get_term($term->term_id, $taxonomy);
                        $term_link = get_term_link($wp_term);
                    ?>
                        <div class="rxg-smi-term-card">
                            <h4 class="rxg-smi-term-name">
                                <?php echo esc_html($term->name); ?>
                                <span class="rxg-smi-term-count">(<?php echo intval($term_page_count); ?>)</span>
                            </h4>
                            
                            <div class="rxg-smi-term-meta">
                                <div class="rxg-smi-term-meta-item">
                                    <span class="rxg-smi-term-meta-label"><?php _e('Slug:', 'rxg-smi'); ?></span>
                                    <span class="rxg-smi-term-meta-value"><?php echo esc_html($term->slug); ?></span>
                                </div>
                                
                                <?php if (isset($wp_term->description) && !empty($wp_term->description)) : ?>
                                    <div class="rxg-smi-term-meta-item">
                                        <span class="rxg-smi-term-meta-label"><?php _e('Description:', 'rxg-smi'); ?></span>
                                        <span class="rxg-smi-term-meta-value"><?php echo esc_html(wp_trim_words($wp_term->description, 10)); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="rxg-smi-term-actions">
                                <a href="<?php echo esc_url(add_query_arg(array('term_id' => $term->term_id))); ?>" class="button button-small">
                                    <?php _e('Voir pages', 'rxg-smi'); ?>
                                </a>
                                
                                <?php if (!is_wp_error($term_link)) : ?>
                                    <a href="<?php echo esc_url($term_link); ?>" class="button button-small" target="_blank">
                                        <?php _e('Voir archive', 'rxg-smi'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php _e('Aucun terme trouvé pour cette taxonomie.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($term_id) && !empty($term_pages)) : 
            // Récupérer les informations sur le terme sélectionné
            $selected_term = null;
            foreach ($terms as $term) {
                if ($term->term_id == $term_id) {
                    $selected_term = $term;
                    break;
                }
            }
        ?>
            <div class="rxg-smi-term-pages">
                <h3>
                    <?php printf(__('Pages associées à : %s', 'rxg-smi'), esc_html($selected_term->name)); ?>
                    <a href="<?php echo esc_url(remove_query_arg('term_id')); ?>" class="rxg-smi-back-link">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Retour aux termes', 'rxg-smi'); ?>
                    </a>
                </h3>
                
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Page', 'rxg-smi'); ?></th>
                            <th><?php _e('Type', 'rxg-smi'); ?></th>
                            <th><?php _e('Liens entrants', 'rxg-smi'); ?></th>
                            <th><?php _e('Liens sortants', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($term_pages as $page) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url($page->url); ?>" target="_blank">
                                        <?php echo esc_html($page->title); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($page->post_type); ?></td>
                                <td><?php echo intval($page->inbound_links_count); ?></td>
                                <td><?php echo intval($page->outbound_links_count); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id)); ?>" class="button button-small">
                                        <?php _e('Voir liens', 'rxg-smi'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $page->post_id . '&action=edit')); ?>" class="button button-small">
                                        <?php _e('Éditer', 'rxg-smi'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="rxg-smi-term-connections">
                    <h4><?php _e('Autres termes fréquemment associés', 'rxg-smi'); ?></h4>
                    
                    <?php
                    // Trouver les termes qui apparaissent souvent avec ce terme
                    $related_terms = $wpdb->get_results($wpdb->prepare("
                        SELECT t2.taxonomy, t2.name, t2.term_id, COUNT(*) as common_pages
                        FROM $table_terms t1
                        JOIN $table_terms t2 ON t1.page_id = t2.page_id AND (t1.taxonomy != t2.taxonomy OR t1.term_id != t2.term_id)
                        WHERE t1.taxonomy = %s AND t1.term_id = %d
                        GROUP BY t2.taxonomy, t2.name, t2.term_id
                        ORDER BY common_pages DESC
                        LIMIT 10
                    ", $taxonomy, $term_id));
                    ?>
                    
                    <?php if (!empty($related_terms)) : ?>
                        <div class="rxg-smi-related-terms">
                            <?php foreach ($related_terms as $related) : ?>
                                <div class="rxg-smi-related-term">
                                    <span class="rxg-smi-related-term-name"><?php echo esc_html($related->name); ?></span>
                                    <span class="rxg-smi-related-term-tax"><?php echo esc_html($related->taxonomy); ?></span>
                                    <span class="rxg-smi-related-term-count"><?php echo intval($related->common_pages); ?> <?php _e('pages communes', 'rxg-smi'); ?></span>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-taxonomies&taxonomy=' . urlencode($related->taxonomy) . '&term_id=' . $related->term_id)); ?>" class="rxg-smi-related-term-link">
                                        <?php _e('Voir', 'rxg-smi'); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p><?php _e('Aucun terme associé trouvé.', 'rxg-smi'); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="rxg-smi-internal-linking">
                    <h4><?php _e('Analyse du maillage interne', 'rxg-smi'); ?></h4>
                    
                    <?php
                    // Analyser les liens internes entre les pages de ce terme
                    $pages_ids = array_map(function($p) { return $p->id; }, $term_pages);
                    
                    if (count($pages_ids) > 1) {
                        $pages_ids_str = implode(',', array_map('intval', $pages_ids));
                        
                        // Compter les liens entre les pages de ce terme
                        $internal_links_count = $wpdb->get_var("
                            SELECT COUNT(*)
                            FROM {$wpdb->prefix}rxg_smi_links
                            WHERE source_id IN ($pages_ids_str) AND target_id IN ($pages_ids_str)
                        ");
                        
                        // Nombre théorique de liens possibles (n*(n-1) pour un graphe complet dirigé)
                        $possible_links = count($pages_ids) * (count($pages_ids) - 1);
                        
                        // Calculer le pourcentage de liaison
                        $linking_percentage = ($possible_links > 0) ? round(($internal_links_count / $possible_links) * 100, 1) : 0;
                        
                        // Couleur en fonction du pourcentage (rouge à vert)
                        $color_class = '';
                        if ($linking_percentage < 20) {
                            $color_class = 'rxg-smi-poor';
                        } elseif ($linking_percentage < 50) {
                            $color_class = 'rxg-smi-average';
                        } else {
                            $color_class = 'rxg-smi-good';
                        }
                        ?>
                        
                        <div class="rxg-smi-linking-stats">
                            <div class="rxg-smi-linking-stat">
                                <span class="rxg-smi-linking-stat-label"><?php _e('Liens internes au cluster:', 'rxg-smi'); ?></span>
                                <span class="rxg-smi-linking-stat-value"><?php echo intval($internal_links_count); ?></span>
                            </div>
                            
                            <div class="rxg-smi-linking-stat">
                                <span class="rxg-smi-linking-stat-label"><?php _e('Liens possibles:', 'rxg-smi'); ?></span>
                                <span class="rxg-smi-linking-stat-value"><?php echo intval($possible_links); ?></span>
                            </div>
                            
                            <div class="rxg-smi-linking-stat">
                                <span class="rxg-smi-linking-stat-label"><?php _e('Pourcentage de liaison:', 'rxg-smi'); ?></span>
                                <span class="rxg-smi-linking-stat-value <?php echo esc_attr($color_class); ?>"><?php echo esc_html($linking_percentage); ?>%</span>
                            </div>
                        </div>
                        
                        <?php if ($linking_percentage < 30) : ?>
                            <div class="rxg-smi-recommendation rxg-smi-warning">
                                <span class="dashicons dashicons-warning"></span>
                                <p>
                                    <?php _e('Le maillage interne entre les pages de ce terme est faible. Envisagez d\'ajouter plus de liens entre ces pages pour renforcer ce cluster thématique.', 'rxg-smi'); ?>
                                </p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities')); ?>" class="button button-small">
                                    <?php _e('Voir les opportunités', 'rxg-smi'); ?>
                                </a>
                            </div>
                        <?php elseif ($linking_percentage > 70) : ?>
                            <div class="rxg-smi-recommendation rxg-smi-success">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <p>
                                    <?php _e('Excellent maillage interne entre les pages de ce terme. Ces pages forment un cluster thématique bien connecté.', 'rxg-smi'); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                    <?php } else { ?>
                        <p><?php _e('Il faut au moins deux pages pour analyser le maillage interne du cluster.', 'rxg-smi'); ?></p>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.rxg-smi-taxonomy-header {
    margin: 20px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-taxonomy-selector {
    flex: 1;
    min-width: 250px;
}

.rxg-smi-taxonomy-selector label {
    margin-right: 10px;
    font-weight: 500;
}

.rxg-smi-taxonomy-selector select {
    min-width: 200px;
}

.rxg-smi-taxonomy-info {
    flex: 2;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-tax-info-item {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    min-width: 150px;
}

.rxg-smi-tax-info-label {
    font-weight: 500;
    margin-right: 5px;
}

.rxg-smi-tax-info-value {
    font-weight: 600;
}

.rxg-smi-taxonomy-content {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
}

.rxg-smi-terms-list {
    flex: 1;
    min-width: 300px;
}

.rxg-smi-terms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.rxg-smi-term-card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 15px;
    border-left: 3px solid #2271b1;
}

.rxg-smi-term-name {
    margin-top: 0;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rxg-smi-term-count {
    font-size: 0.8em;
    font-weight: normal;
    color: #50575e;
}

.rxg-smi-term-meta {
    margin-bottom: 15px;
}

.rxg-smi-term-meta-item {
    margin-bottom: 5px;
}

.rxg-smi-term-meta-label {
    font-weight: 500;
    margin-right: 5px;
}

.rxg-smi-term-actions {
    display: flex;
    gap: 5px;
}

.rxg-smi-term-pages {
    flex: 2;
    min-width: 500px;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-term-pages h3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0;
}

.rxg-smi-back-link {
    font-size: 0.8em;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.rxg-smi-term-connections {
    margin-top: 30px;
}

.rxg-smi-related-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.rxg-smi-related-term {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.rxg-smi-related-term-name {
    font-weight: 500;
}

.rxg-smi-related-term-tax {
    font-size: 0.8em;
    color: #50575e;
    font-style: italic;
}

.rxg-smi-related-term-count {
    font-size: 0.8em;
    color: #50575e;
}

.rxg-smi-internal-linking {
    margin-top: 30px;
}

.rxg-smi-linking-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.rxg-smi-linking-stat {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    min-width: 150px;
}

.rxg-smi-linking-stat-label {
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
}

.rxg-smi-linking-stat-value {
    font-size: 1.5em;
    font-weight: 600;
}

.rxg-smi-poor {
    color: #dc3232;
}

.rxg-smi-average {
    color: #ffb900;
}

.rxg-smi-good {
    color: #46b450;
}

.rxg-smi-recommendation {
    margin-top: 15px;
    padding: 15px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.rxg-smi-warning {
    background: #fff8e5;
    border-left: 4px solid #ffb900;
}

.rxg-smi-warning .dashicons {
    color: #ffb900;
}

.rxg-smi-success {
    background: #f0f8e5;
    border-left: 4px solid #46b450;
}

.rxg-smi-success .dashicons {
    color: #46b450;
}

.rxg-smi-recommendation p {
    margin: 0;
    flex: 1;
}
</style>
