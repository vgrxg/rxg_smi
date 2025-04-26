<?php
/**
 * Template pour l'analyse sémantique globale
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Analyse Sémantique', 'rxg-smi'); ?></h1>
    
    <div class="rxg-smi-semantic-header">
        <p class="rxg-smi-semantic-intro">
            <?php _e('L\'analyse sémantique identifie les relations thématiques entre vos pages et révèle les opportunités de maillage interne basées sur la similarité de contenu.', 'rxg-smi'); ?>
        </p>
        
        <div class="rxg-smi-semantic-actions">
            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_semantic_analysis'), 'rxg_smi_semantic_analysis', 'rxg_smi_nonce'); ?>" class="button button-primary">
                <span class="dashicons dashicons-update"></span>
                <?php _e('Lancer l\'analyse sémantique', 'rxg-smi'); ?>
            </a>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-export&type=semantic')); ?>" class="button">
                <span class="dashicons dashicons-download"></span>
                <?php _e('Exporter les données', 'rxg-smi'); ?>
            </a>
        </div>
    </div>
    
    <div id="rxg-smi-semantic-tabs" class="rxg-smi-tabs">
        <ul class="rxg-smi-tabs-nav">
            <li><a href="#tabs-1"><?php _e('Opportunités sémantiques', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-2"><?php _e('Clusters thématiques', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-3"><?php _e('Termes fréquents', 'rxg-smi'); ?></a></li>
        </ul>
        
        <div id="tabs-1" class="rxg-smi-tab-content">
            <h2><?php _e('Pages sémantiquement similaires mais non liées', 'rxg-smi'); ?></h2>
            <p class="rxg-smi-tab-description">
                <?php _e('Ces pages partagent des thématiques communes mais ne sont pas liées entre elles. Ce sont d\'excellentes opportunités pour améliorer votre maillage interne.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($high_similarity_pages)) : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Page 1', 'rxg-smi'); ?></th>
                            <th><?php _e('Page 2', 'rxg-smi'); ?></th>
                            <th><?php _e('Similarité', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($high_similarity_pages as $pair) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $pair->page1_id)); ?>">
                                        <?php echo esc_html($pair->page1_title); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $pair->page2_id)); ?>">
                                        <?php echo esc_html($pair->page2_title); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="rxg-smi-similarity-meter">
                                        <div class="rxg-smi-similarity-fill" style="width: <?php echo esc_attr($pair->similarity * 100); ?>%"></div>
                                        <span class="rxg-smi-similarity-value"><?php echo round($pair->similarity * 100); ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . get_post_id_by_page_id($pair->page1_id) . '&action=edit')); ?>" class="button button-small">
                                        <?php _e('Éditer page 1', 'rxg-smi'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . get_post_id_by_page_id($pair->page2_id) . '&action=edit')); ?>" class="button button-small">
                                        <?php _e('Éditer page 2', 'rxg-smi'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="rxg-smi-no-data">
                    <p><?php _e('Aucune opportunité sémantique trouvée. Lancez l\'analyse sémantique pour découvrir des liens potentiels.', 'rxg-smi'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="tabs-2" class="rxg-smi-tab-content">
            <h2><?php _e('Clusters thématiques identifiés', 'rxg-smi'); ?></h2>
            <p class="rxg-smi-tab-description">
                <?php _e('Ces groupes de pages partagent des thématiques communes et forment des "îlots de contenu" sur votre site. Un bon maillage interne devrait renforcer ces clusters.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($thematic_clusters)) : ?>
                <div class="rxg-smi-clusters">
                    <?php foreach ($thematic_clusters as $cluster) : ?>
                        <div class="rxg-smi-cluster-box">
                            <div class="rxg-smi-cluster-header">
                                <h3>
                                    <?php printf(__('Cluster #%d: %s', 'rxg-smi'), $cluster['id'], esc_html(implode(', ', array_slice($cluster['terms'], 0, 3)))); ?>
                                </h3>
                                <span class="rxg-smi-cluster-count"><?php echo intval($cluster['page_count']); ?> <?php _e('pages', 'rxg-smi'); ?></span>
                            </div>
                            
                            <div class="rxg-smi-cluster-terms">
                                <h4><?php _e('Termes principaux:', 'rxg-smi'); ?></h4>
                                <div class="rxg-smi-term-tags">
                                    <?php foreach ($cluster['terms'] as $term) : ?>
                                        <span class="rxg-smi-term-tag"><?php echo esc_html($term); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="rxg-smi-cluster-pages">
                                <h4><?php _e('Pages représentatives:', 'rxg-smi'); ?></h4>
                                <ul>
                                    <?php foreach ($cluster['pages'] as $page) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $page->id)); ?>">
                                                <?php echo esc_html($page->title); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ($cluster['page_count'] > count($cluster['pages'])) : ?>
                                    <p class="rxg-smi-more-pages">
                                        <?php printf(
                                            __('... et %d autres pages', 'rxg-smi'),
                                            $cluster['page_count'] - count($cluster['pages'])
                                        ); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="rxg-smi-cluster-actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-cluster-view&cluster_id=' . $cluster['id'])); ?>" class="button">
                                    <?php _e('Explorer le cluster', 'rxg-smi'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="rxg-smi-no-data">
                    <p><?php _e('Aucun cluster thématique trouvé. Lancez l\'analyse sémantique pour identifier les clusters.', 'rxg-smi'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="tabs-3" class="rxg-smi-tab-content">
            <h2><?php _e('Termes les plus significatifs sur le site', 'rxg-smi'); ?></h2>
            <p class="rxg-smi-tab-description">
                <?php _e('Ces termes sont les plus importants sur l\'ensemble de votre site, selon l\'analyse TF-IDF. Ils représentent les thématiques centrales de votre contenu.', 'rxg-smi'); ?>
            </p>
            
            <?php
            global $wpdb;
            $table_semantic_terms = $wpdb->prefix . 'rxg_smi_semantic_terms';
            
            $top_terms = $wpdb->get_results("
                SELECT term, COUNT(DISTINCT page_id) as page_count, SUM(weight) as total_weight
                FROM $table_semantic_terms
                GROUP BY term
                ORDER BY total_weight DESC
                LIMIT 50
            ");
            ?>
            
            <?php if (!empty($top_terms)) : ?>
                <div class="rxg-smi-term-cloud">
                    <?php foreach ($top_terms as $index => $term) : 
                        // Calculer la taille relative du terme (entre 1 et 5)
                        $size = 1 + floor(4 * $index / count($top_terms));
                        $size = 6 - $size; // Inverser pour que les plus importants soient plus grands
                    ?>
                        <span class="rxg-smi-cloud-term rxg-smi-term-size-<?php echo $size; ?>">
                            <?php echo esc_html($term->term); ?>
                            <span class="rxg-smi-term-count">(<?php echo intval($term->page_count); ?>)</span>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <table class="widefat striped" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th><?php _e('Terme', 'rxg-smi'); ?></th>
                            <th><?php _e('Pages', 'rxg-smi'); ?></th>
                            <th><?php _e('Poids total', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_terms as $term) : ?>
                            <tr>
                                <td><?php echo esc_html($term->term); ?></td>
                                <td><?php echo intval($term->page_count); ?></td>
                                <td><?php echo round($term->total_weight, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="rxg-smi-no-data">
                    <p><?php _e('Aucun terme significatif trouvé. Lancez l\'analyse sémantique pour identifier les termes importants.', 'rxg-smi'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.rxg-smi-semantic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-semantic-intro {
    margin: 0;
    max-width: 70%;
}

.rxg-smi-semantic-actions {
    display: flex;
    gap: 10px;
}

.rxg-smi-tab-description {
    margin-bottom: 20px;
    color: #555;
    font-style: italic;
}

.rxg-smi-similarity-meter {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.rxg-smi-similarity-fill {
    height: 100%;
    background: #2271b1;
    border-radius: 10px;
}

.rxg-smi-similarity-value {
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

.rxg-smi-clusters {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.rxg-smi-cluster-box {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #2271b1;
}

.rxg-smi-cluster-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.rxg-smi-cluster-header h3 {
    margin: 0;
    font-size: 1.1em;
}

.rxg-smi-cluster-count {
    background: #f0f0f1;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 0.9em;
    color: #50575e;
}

.rxg-smi-cluster-terms h4,
.rxg-smi-cluster-pages h4 {
    margin: 0 0 10px 0;
    font-size: 0.9em;
    font-weight: 600;
    color: #50575e;
}

.rxg-smi-term-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 15px;
}

.rxg-smi-term-tag {
    background: #f0f8ff;
    border: 1px solid #cce5ff;
    color: #0066cc;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.9em;
}

.rxg-smi-cluster-pages ul {
    margin: 0 0 15px 0;
    padding-left: 20px;
}

.rxg-smi-cluster-pages ul li {
    margin-bottom: 5px;
}

.rxg-smi-more-pages {
    font-size: 0.9em;
    font-style: italic;
    color: #50575e;
    margin: 5px 0;
}

.rxg-smi-cluster-actions {
    text-align: right;
    margin-top: 10px;
}

.rxg-smi-term-cloud {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    line-height: 2.2;
}

.rxg-smi-cloud-term {
    display: inline-block;
    margin: 5px;
    padding: 3px 5px;
    border-radius: 3px;
    background: #f1f1f1;
    transition: all 0.2s ease;
}

.rxg-smi-cloud-term:hover {
    background: #e0e0e0;
    transform: scale(1.05);
}

.rxg-smi-term-count {
    font-size: 0.7em;
    color: #777;
    vertical-align: super;
}

.rxg-smi-term-size-1 { font-size: 0.8em; opacity: 0.7; }
.rxg-smi-term-size-2 { font-size: 1em; opacity: 0.8; }
.rxg-smi-term-size-3 { font-size: 1.2em; opacity: 0.9; }
.rxg-smi-term-size-4 { font-size: 1.5em; font-weight: 500; }
.rxg-smi-term-size-5 { font-size: 1.8em; font-weight: 600; }

.rxg-smi-no-data {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    color: #50575e;
}
</style>

<script>
jQuery(document).ready(function($) {
    $("#rxg-smi-semantic-tabs").tabs();
});
</script>