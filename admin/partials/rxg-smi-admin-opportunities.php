<?php
/**
 * Template pour la page des opportunités de maillage
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php if (empty($page_id)) : ?>
        <div class="rxg-smi-opportunities-intro">
            <p>
                <?php _e('Cette page vous aide à identifier les opportunités d\'amélioration de votre maillage interne. Utilisez ces informations pour renforcer la structure de votre site.', 'rxg-smi'); ?>
            </p>
        </div>
        
        <div class="rxg-smi-opportunity-sections">
            <!-- Pages orphelines -->
            <div class="rxg-smi-opportunity-section">
                <div class="rxg-smi-opportunity-header">
                    <h2>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Pages orphelines', 'rxg-smi'); ?>
                    </h2>
                    <span class="rxg-smi-opportunity-count"><?php echo count($orphan_pages); ?></span>
                </div>
                
                <div class="rxg-smi-opportunity-description">
                    <p>
                        <?php _e('Ces pages ne reçoivent aucun lien interne, ce qui les rend difficiles à découvrir pour les utilisateurs et les moteurs de recherche.', 'rxg-smi'); ?>
                    </p>
                </div>
                
                <?php if (!empty($orphan_pages)) : ?>
                    <div class="rxg-smi-opportunity-table-container">
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Page', 'rxg-smi'); ?></th>
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
                    </div>
                <?php else : ?>
                    <div class="rxg-smi-no-opportunity">
                        <p><?php _e('Félicitations! Aucune page orpheline détectée.', 'rxg-smi'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pages sans liens sortants -->
            <div class="rxg-smi-opportunity-section">
                <div class="rxg-smi-opportunity-header">
                    <h2>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Pages sans liens sortants', 'rxg-smi'); ?>
                    </h2>
                    <span class="rxg-smi-opportunity-count"><?php echo count($no_outbound_pages); ?></span>
                </div>
                
                <div class="rxg-smi-opportunity-description">
                    <p>
                        <?php _e('Ces pages ne contiennent aucun lien sortant vers d\'autres pages du site, ce qui crée des impasses dans le maillage.', 'rxg-smi'); ?>
                    </p>
                </div>
                
                <?php if (!empty($no_outbound_pages)) : ?>
                    <div class="rxg-smi-opportunity-table-container">
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Page', 'rxg-smi'); ?></th>
                                    <th><?php _e('Type', 'rxg-smi'); ?></th>
                                    <th><?php _e('Profondeur', 'rxg-smi'); ?></th>
                                    <th><?php _e('Actions', 'rxg-smi'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($no_outbound_pages as $page) : ?>
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
                    </div>
                <?php else : ?>
                    <div class="rxg-smi-no-opportunity">
                        <p><?php _e('Félicitations! Toutes vos pages contiennent des liens sortants.', 'rxg-smi'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pages avec ratio mots/liens élevé -->
            <div class="rxg-smi-opportunity-section">
                <div class="rxg-smi-opportunity-header">
                    <h2>
                        <span class="dashicons dashicons-warning"></span>
                        <?php _e('Pages avec trop peu de liens', 'rxg-smi'); ?>
                    </h2>
                    <span class="rxg-smi-opportunity-count"><?php echo count($high_ratio_pages); ?></span>
                </div>
                
                <div class="rxg-smi-opportunity-description">
                    <p>
                        <?php _e('Ces pages ont un contenu substantiel mais relativement peu de liens sortants. Ajouter plus de liens pourrait améliorer la navigation et le référencement.', 'rxg-smi'); ?>
                    </p>
                </div>
                
                <?php if (!empty($high_ratio_pages)) : ?>
                    <div class="rxg-smi-opportunity-table-container">
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Page', 'rxg-smi'); ?></th>
                                    <th><?php _e('Mots', 'rxg-smi'); ?></th>
                                    <th><?php _e('Liens sortants', 'rxg-smi'); ?></th>
                                    <th><?php _e('Ratio mots/liens', 'rxg-smi'); ?></th>
                                    <th><?php _e('Actions', 'rxg-smi'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($high_ratio_pages as $page) : ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url($page->url); ?>" target="_blank">
                                                <?php echo esc_html($page->title); ?>
                                            </a>
                                        </td>
                                        <td><?php echo intval($page->word_count); ?></td>
                                        <td><?php echo intval($page->outbound_links_count); ?></td>
                                        <td><?php echo round($page->word_link_ratio, 1); ?></td>
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
                    </div>
                <?php else : ?>
                    <div class="rxg-smi-no-opportunity">
                        <p><?php _e('Félicitations! Vos pages ont une bonne densité de liens.', 'rxg-smi'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- Affichage des suggestions pour une page spécifique -->
        <?php if ($page_details) : ?>
            <div class="rxg-smi-page-suggestions">
                <div class="rxg-smi-page-header">
                    <a href="<?php echo esc_url(remove_query_arg('page_id')); ?>" class="rxg-smi-back-link">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Retour aux opportunités', 'rxg-smi'); ?>
                    </a>
                    
                    <h2>
                        <?php _e('Suggestions pour:', 'rxg-smi'); ?> 
                        <a href="<?php echo esc_url($page_details->url); ?>" target="_blank">
                            <?php echo esc_html($page_details->title); ?>
                        </a>
                    </h2>
                </div>
                
                <div class="rxg-smi-page-details">
                    <div class="rxg-smi-page-stats">
                        <div class="rxg-smi-page-stat">
                            <span class="rxg-smi-stat-label"><?php _e('Type:', 'rxg-smi'); ?></span>
                            <span class="rxg-smi-stat-value"><?php echo esc_html($page_details->post_type); ?></span>
                        </div>
                        
                        <div class="rxg-smi-page-stat">
                            <span class="rxg-smi-stat-label"><?php _e('Mots:', 'rxg-smi'); ?></span>
                            <span class="rxg-smi-stat-value"><?php echo intval($page_details->word_count); ?></span>
                        </div>
                        
                        <div class="rxg-smi-page-stat">
                            <span class="rxg-smi-stat-label"><?php _e('Liens entrants:', 'rxg-smi'); ?></span>
                            <span class="rxg-smi-stat-value"><?php echo intval($page_details->inbound_links_count); ?></span>
                        </div>
                        
                        <div class="rxg-smi-page-stat">
                            <span class="rxg-smi-stat-label"><?php _e('Liens sortants:', 'rxg-smi'); ?></span>
                            <span class="rxg-smi-stat-value"><?php echo intval($page_details->outbound_links_count); ?></span>
                        </div>
                        
                        <div class="rxg-smi-page-stat">
                            <span class="rxg-smi-stat-label"><?php _e('Profondeur:', 'rxg-smi'); ?></span>
                            <span class="rxg-smi-stat-value"><?php echo intval($page_details->depth); ?></span>
                        </div>
                    </div>
                    
                    <div class="rxg-smi-page-actions">
                        <a href="<?php echo esc_url(admin_url('post.php?post=' . $page_details->post_id . '&action=edit')); ?>" class="button button-primary">
                            <span class="dashicons dashicons-edit"></span>
                            <?php _e('Éditer cette page', 'rxg-smi'); ?>
                        </a>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page_details->id)); ?>" class="button">
                            <span class="dashicons dashicons-admin-links"></span>
                            <?php _e('Voir les liens actuels', 'rxg-smi'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="rxg-smi-suggestions-content">
                    <!-- Suggestions de taxonomies manquantes -->
                    <?php if (!empty($taxonomy_suggestions)) : ?>
                        <div class="rxg-smi-suggestion-box">
                            <h3>
                                <span class="dashicons dashicons-category"></span>
                                <?php _e('Taxonomies suggérées', 'rxg-smi'); ?>
                            </h3>
                            
                            <div class="rxg-smi-suggestion-description">
                                <p>
                                    <?php _e('Ces taxonomies sont utilisées sur les pages liées à celle-ci, mais pas sur cette page elle-même.', 'rxg-smi'); ?>
                                </p>
                            </div>
                            
                            <div class="rxg-smi-taxonomy-suggestions">
                                <?php foreach ($taxonomy_suggestions as $taxonomy => $terms) : ?>
                                    <div class="rxg-smi-taxonomy-suggestion">
                                        <h4><?php echo esc_html($taxonomy); ?></h4>
                                        <div class="rxg-smi-suggested-terms">
                                            <?php foreach ($terms as $term) : ?>
                                                <span class="rxg-smi-suggested-term">
                                                    <?php echo esc_html($term->name); ?>
                                                    <span class="rxg-smi-term-usage"><?php echo intval($term->usage_count); ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Suggestions de liens potentiels -->
                    <?php if (!empty($potential_links)) : ?>
                        <div class="rxg-smi-suggestion-box">
                            <h3>
                                <span class="dashicons dashicons-admin-links"></span>
                                <?php _e('Pages à lier', 'rxg-smi'); ?>
                            </h3>
                            
                            <div class="rxg-smi-suggestion-description">
                                <p>
                                    <?php _e('Ces pages partagent des thématiques avec cette page mais ne sont pas liées. Ajoutez des liens vers ces pages.', 'rxg-smi'); ?>
                                </p>
                            </div>
                            
                            <div class="rxg-smi-potential-links">
                                <table class="widefat striped">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Page', 'rxg-smi'); ?></th>
                                            <th><?php _e('Termes communs', 'rxg-smi'); ?></th>
                                            <th><?php _e('Texte d\'ancre suggéré', 'rxg-smi'); ?></th>
                                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($potential_links as $link) : ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo esc_url($link->url); ?>" target="_blank">
                                                        <?php echo esc_html($link->title); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo intval($link->shared_terms); ?></td>
                                                <td>
                                                    <div class="rxg-smi-suggested-anchors">
                                                        <select class="rxg-smi-anchor-selector" data-url="<?php echo esc_attr($link->url); ?>">
                                                            <option value=""><?php _e('-- Choisir un texte d\'ancre --', 'rxg-smi'); ?></option>
                                                            <option value="<?php echo esc_attr($link->title); ?>"><?php echo esc_html($link->title); ?></option>
                                                            <option value="<?php _e('Cliquez ici', 'rxg-smi'); ?>"><?php _e('Cliquez ici', 'rxg-smi'); ?></option>
                                                            <option value="<?php _e('En savoir plus', 'rxg-smi'); ?>"><?php _e('En savoir plus', 'rxg-smi'); ?></option>
                                                            <option value="custom"><?php _e('Personnalisé...', 'rxg-smi'); ?></option>
                                                        </select>
                                                        <input type="text" class="rxg-smi-custom-anchor" style="display: none;" placeholder="<?php esc_attr_e('Texte d\'ancre personnalisé', 'rxg-smi'); ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $page_details->post_id . '&action=edit')); ?>" class="button button-small">
                                                        <?php _e('Éditer', 'rxg-smi'); ?>
                                                    </a>
                                                    <button class="button button-small rxg-smi-copy-html">
                                                        <?php _e('Copier HTML', 'rxg-smi'); ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Suggestions d'ancres -->
                    <?php if (!empty($suggested_anchors)) : ?>
                        <div class="rxg-smi-suggestion-box">
                            <h3>
                                <span class="dashicons dashicons-tag"></span>
                                <?php _e('Textes d\'ancre suggérés', 'rxg-smi'); ?>
                            </h3>
                            
                            <div class="rxg-smi-suggestion-description">
                                <p>
                                    <?php _e('Ces textes d\'ancre sont recommandés pour les liens pointant vers cette page. Utilisez-les pour améliorer la diversité des ancres.', 'rxg-smi'); ?>
                                </p>
                            </div>
                            
                            <div class="rxg-smi-suggested-anchors-list">
                                <table class="widefat striped">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th>
                                            <th><?php _e('Source', 'rxg-smi'); ?></th>
                                            <th><?php _e('Pertinence', 'rxg-smi'); ?></th>
                                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($suggested_anchors as $anchor) : ?>
                                            <tr>
                                                <td><?php echo esc_html($anchor['text']); ?></td>
                                                <td><?php echo esc_html($anchor['source']); ?></td>
                                                <td>
                                                    <div class="rxg-smi-score-meter">
                                                        <div class="rxg-smi-score-fill" style="width: <?php echo intval($anchor['score']); ?>%"></div>
                                                        <span class="rxg-smi-score-text"><?php echo intval($anchor['score']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="button button-small rxg-smi-copy-anchor" data-anchor="<?php echo esc_attr($anchor['text']); ?>">
                                                        <?php _e('Copier', 'rxg-smi'); ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($potential_links) && empty($suggested_anchors) && empty($taxonomy_suggestions)) : ?>
                        <div class="rxg-smi-no-suggestions">
                            <p><?php _e('Aucune suggestion disponible pour cette page.', 'rxg-smi'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="rxg-smi-error">
                <p><?php _e('Page introuvable.', 'rxg-smi'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities')); ?>" class="button">
                    <?php _e('Retour aux opportunités', 'rxg-smi'); ?>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.rxg-smi-opportunities-intro {
    margin: 20px 0;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-opportunity-sections {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.rxg-smi-opportunity-section {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.rxg-smi-opportunity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.rxg-smi-opportunity-header h2 {
    margin: 0;
    font-size: 1.2em;
    display: flex;
    align-items: center;
}

.rxg-smi-opportunity-header h2 .dashicons {
    margin-right: 10px;
    color: #ffb900;
}

.rxg-smi-opportunity-count {
    background: #ffb900;
    color: #fff;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9em;
}

.rxg-smi-opportunity-description {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.rxg-smi-opportunity-description p {
    margin: 0;
}

.rxg-smi-opportunity-table-container {
    padding: 0 20px 20px;
    overflow-x: auto;
}

.rxg-smi-no-opportunity {
    padding: 15px 20px;
    color: #46b450;
}

.rxg-smi-page-suggestions {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    padding: 20px;
}

.rxg-smi-page-header {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.rxg-smi-back-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #2271b1;
    font-size: 0.9em;
}

.rxg-smi-back-link .dashicons {
    margin-right: 5px;
}

.rxg-smi-page-details {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.rxg-smi-page-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-page-stat {
    background: #fff;
    padding: 8px 15px;
    border-radius: 5px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.rxg-smi-stat-label {
    font-weight: 500;
    margin-right: 5px;
}

.rxg-smi-page-actions {
    display: flex;
    gap: 10px;
}

.rxg-smi-page-actions .dashicons {
    margin-right: 5px;
}

.rxg-smi-suggestions-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.rxg-smi-suggestion-box {
    background: #f8f9fa;
    border-radius: 5px;
    padding: 20px;
    border-left: 4px solid #2271b1;
}

.rxg-smi-suggestion-box h3 {
    margin-top: 0;
    display: flex;
    align-items: center;
}

.rxg-smi-suggestion-box h3 .dashicons {
    margin-right: 8px;
    color: #2271b1;
}

.rxg-smi-suggestion-description {
    margin-bottom: 15px;
}

.rxg-smi-taxonomy-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-taxonomy-suggestion {
    flex: 1;
    min-width: 250px;
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.rxg-smi-taxonomy-suggestion h4 {
    margin-top: 0;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
}

.rxg-smi-suggested-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.rxg-smi-suggested-term {
    background: #f0f0f1;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 0.9em;
    display: flex;
    align-items: center;
}

.rxg-smi-term-usage {
    background: #2271b1;
    color: #fff;
    padding: 1px 5px;
    border-radius: 10px;
    font-size: 0.8em;
    margin-left: 5px;
}

.rxg-smi-potential-links {
    margin-top: 15px;
}

.rxg-smi-suggested-anchors {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.rxg-smi-anchor-selector {
    width: 100%;
}

.rxg-smi-suggested-anchors-list {
    margin-top: 15px;
}

.rxg-smi-score-meter {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.rxg-smi-score-fill {
    height: 100%;
    background: #2271b1;
    border-radius: 10px;
}

.rxg-smi-score-text {
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

.rxg-smi-no-suggestions, .rxg-smi-error {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Gestionnaire pour le sélecteur d'ancre
    $('.rxg-smi-anchor-selector').on('change', function() {
        var $this = $(this);
        var $customInput = $this.next('.rxg-smi-custom-anchor');
        
        if ($this.val() === 'custom') {
            $customInput.show().focus();
        } else {
            $customInput.hide();
        }
    });
    
    // Gestionnaire pour le bouton de copie HTML
    $('.rxg-smi-copy-html').on('click', function() {
        var $row = $(this).closest('tr');
        var url = $row.find('a').first().attr('href');
        var $anchorSelector = $row.find('.rxg-smi-anchor-selector');
        var $customInput = $row.find('.rxg-smi-custom-anchor');
        
        var anchorText = $anchorSelector.val() === 'custom' ? $customInput.val() : $anchorSelector.val();
        
        if (!anchorText) {
            anchorText = $row.find('a').first().text();
        }
        
        var html = '<a href="' + url + '">' + anchorText + '</a>';
        
        // Copier dans le presse-papier
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(html).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Feedback visuel
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Copié !', 'rxg-smi'); ?>');
        
        setTimeout(function() {
            $button.text(originalText);
        }, 2000);
    });
    
    // Gestionnaire pour le bouton de copie d'ancre
    $('.rxg-smi-copy-anchor').on('click', function() {
        var anchorText = $(this).data('anchor');
        
        // Copier dans le presse-papier
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(anchorText).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Feedback visuel
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Copié !', 'rxg-smi'); ?>');
        
        setTimeout(function() {
            $button.text(originalText);
        }, 2000);
    });
});
</script>
