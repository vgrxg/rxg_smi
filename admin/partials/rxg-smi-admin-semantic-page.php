<?php
/**
 * Template pour l'analyse sémantique d'une page spécifique
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Analyse Sémantique', 'rxg-smi'); ?></h1>
    
    <div class="rxg-smi-page-header">
        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic')); ?>" class="rxg-smi-back-link">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php _e('Retour à l\'analyse sémantique', 'rxg-smi'); ?>
        </a>
        
        <h2>
            <?php _e('Analyse sémantique pour:', 'rxg-smi'); ?> 
            <a href="<?php echo esc_url($page_details->url); ?>" target="_blank">
                <?php echo esc_html($page_details->title); ?>
            </a>
        </h2>
        
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
    
    <div class="rxg-smi-semantic-content">
        <!-- Termes sémantiques clés -->
        <div class="rxg-smi-semantic-box">
            <div class="rxg-smi-semantic-box-header">
                <h3><?php _e('Termes clés de cette page', 'rxg-smi'); ?></h3>
                <span class="rxg-smi-info-badge" title="<?php esc_attr_e('Ces termes sont les plus significatifs pour cette page selon l\'analyse TF-IDF.', 'rxg-smi'); ?>">?</span>
            </div>
            
            <?php if (!empty($semantic_terms)) : ?>
                <div class="rxg-smi-semantic-terms">
                    <?php foreach ($semantic_terms as $term) : ?>
                        <div class="rxg-smi-semantic-term">
                            <span class="rxg-smi-term-text"><?php echo esc_html($term->term); ?></span>
                            <div class="rxg-smi-term-weight-bar">
                                <div class="rxg-smi-term-weight-fill" style="width: <?php echo min(100, $term->weight * 20); ?>%"></div>
                            </div>
                            <span class="rxg-smi-term-occurences"><?php echo intval($term->count); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php _e('Aucun terme significatif trouvé pour cette page.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Suggestions de liens sémantiques -->
        <div class="rxg-smi-semantic-box">
            <div class="rxg-smi-semantic-box-header">
                <h3><?php _e('Suggestions de liens sémantiques', 'rxg-smi'); ?></h3>
                <span class="rxg-smi-info-badge" title="<?php esc_attr_e('Ces pages partagent des thématiques communes avec celle-ci mais ne sont pas encore liées.', 'rxg-smi'); ?>">?</span>
            </div>
            
            <?php if (!empty($semantic_links)) : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Page', 'rxg-smi'); ?></th>
                            <th><?php _e('Similarité', 'rxg-smi'); ?></th>
                            <th><?php _e('Texte d\'ancre suggéré', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semantic_links as $link) : 
                            // Récupérer des suggestions d'ancres
                            $anchor_suggestions = $semantic_analyzer->suggest_anchor_texts($page_details->id, $link->id, 3);
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url($link->url); ?>" target="_blank">
                                        <?php echo esc_html($link->title); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="rxg-smi-similarity-meter">
                                        <div class="rxg-smi-similarity-fill" style="width: <?php echo esc_attr($link->similarity * 100); ?>%"></div>
                                        <span class="rxg-smi-similarity-value"><?php echo round($link->similarity * 100); ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="rxg-smi-anchor-suggestions">
                                        <select class="rxg-smi-anchor-selector" data-url="<?php echo esc_attr($link->url); ?>">
                                            <option value=""><?php _e('-- Choisir un texte d\'ancre --', 'rxg-smi'); ?></option>
                                            <?php foreach ($anchor_suggestions as $suggestion) : ?>
                                                <option value="<?php echo esc_attr($suggestion['text']); ?>">
                                                    <?php echo esc_html($suggestion['text']); ?> 
                                                    (<?php echo esc_html($suggestion['source']); ?>)
                                                </option>
                                            <?php endforeach; ?>
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
            <?php else : ?>
                <p><?php _e('Aucune suggestion de lien sémantique trouvée pour cette page.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Carte thématique -->
        <?php if (!empty($thematic_map)) : ?>
            <div class="rxg-smi-semantic-box">
                <div class="rxg-smi-semantic-box-header">
                    <h3><?php _e('Carte thématique', 'rxg-smi'); ?></h3>
                    <span class="rxg-smi-info-badge" title="<?php esc_attr_e('Ce cluster thématique regroupe des pages partageant des sujets similaires.', 'rxg-smi'); ?>">?</span>
                </div>
                
                <div class="rxg-smi-thematic-map">
                    <div class="rxg-smi-thematic-terms">
                        <h4><?php _e('Thématiques principales:', 'rxg-smi'); ?></h4>
                        <div class="rxg-smi-term-tags">
                            <?php foreach ($thematic_map['terms'] as $term) : ?>
                                <span class="rxg-smi-term-tag">
                                    <?php echo esc_html($term->term); ?>
                                    <span class="rxg-smi-term-weight">(<?php echo round($term->total_weight, 1); ?>)</span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="rxg-smi-thematic-pages">
                        <h4><?php _e('Pages du même cluster thématique:', 'rxg-smi'); ?></h4>
                        <ul>
                            <?php foreach ($thematic_map['pages'] as $related_page) : ?>
                                <li>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $related_page->id)); ?>">
                                        <?php echo esc_html($related_page->title); ?>
                                    </a>
                                    <?php 
                                    // Vérifier si la page est déjà liée
                                    global $wpdb;
                                    $table_links = $wpdb->prefix . 'rxg_smi_links';
                                    $is_linked = $wpdb->get_var($wpdb->prepare(
                                        "SELECT COUNT(*) FROM $table_links 
                                        WHERE (source_id = %d AND target_id = %d) 
                                        OR (source_id = %d AND target_id = %d)",
                                        $page_details->id, $related_page->id,
                                        $related_page->id, $page_details->id
                                    ));
                                    
                                    if ($is_linked) {
                                        echo ' <span class="rxg-smi-linked-badge">' . __('Déjà liée', 'rxg-smi') . '</span>';
                                    } else {
                                        echo ' <span class="rxg-smi-not-linked-badge">' . __('Non liée', 'rxg-smi') . '</span>';
                                    }
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="rxg-smi-thematic-visualization">
                        <h4><?php _e('Visualisation du cluster:', 'rxg-smi'); ?></h4>
                        <div class="rxg-smi-cluster-preview">
                            <!-- Ici on pourrait mettre une visualisation simple du cluster -->
                            <div class="rxg-smi-cluster-graph">
                                <div class="rxg-smi-cluster-node rxg-smi-current-node" title="<?php echo esc_attr($page_details->title); ?>">
                                    <?php echo esc_html(mb_substr($page_details->title, 0, 2)); ?>
                                </div>
                                
                                <?php foreach ($thematic_map['pages'] as $i => $related_page) : 
                                    // Calculer une position approximative en cercle autour du nœud central
                                    $angle = (2 * M_PI * $i) / count($thematic_map['pages']);
                                    $x = 50 + 40 * cos($angle);
                                    $y = 50 + 40 * sin($angle);
                                    
                                    // Vérifier si la page est déjà liée
                                    global $wpdb;
                                    $table_links = $wpdb->prefix . 'rxg_smi_links';
                                    $is_linked = $wpdb->get_var($wpdb->prepare(
                                        "SELECT COUNT(*) FROM $table_links 
                                        WHERE (source_id = %d AND target_id = %d) 
                                        OR (source_id = %d AND target_id = %d)",
                                        $page_details->id, $related_page->id,
                                        $related_page->id, $page_details->id
                                    ));
                                    
                                    $node_class = $is_linked ? 'rxg-smi-linked-node' : 'rxg-smi-unlinked-node';
                                ?>
                                    <div class="rxg-smi-cluster-node <?php echo $node_class; ?>" 
                                         style="left: <?php echo $x; ?>%; top: <?php echo $y; ?>%;"
                                         title="<?php echo esc_attr($related_page->title); ?>">
                                        <?php echo esc_html(mb_substr($related_page->title, 0, 2)); ?>
                                    </div>
                                    
                                    <div class="rxg-smi-cluster-link <?php echo $is_linked ? 'rxg-smi-link-active' : 'rxg-smi-link-potential'; ?>"
                                         style="--start-x: 50%; --start-y: 50%; --end-x: <?php echo $x; ?>%; --end-y: <?php echo $y; ?>%;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="rxg-smi-cluster-legend">
                                <div class="rxg-smi-legend-item">
                                    <span class="rxg-smi-legend-color rxg-smi-current-color"></span>
                                    <span class="rxg-smi-legend-text"><?php _e('Page actuelle', 'rxg-smi'); ?></span>
                                </div>
                                <div class="rxg-smi-legend-item">
                                    <span class="rxg-smi-legend-color rxg-smi-linked-color"></span>
                                    <span class="rxg-smi-legend-text"><?php _e('Pages liées', 'rxg-smi'); ?></span>
                                </div>
                                <div class="rxg-smi-legend-item">
                                    <span class="rxg-smi-legend-color rxg-smi-unlinked-color"></span>
                                    <span class="rxg-smi-legend-text"><?php _e('Pages non liées', 'rxg-smi'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.rxg-smi-page-header {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.rxg-smi-page-header h2 {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.rxg-smi-page-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.rxg-smi-semantic-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.rxg-smi-semantic-box {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-semantic-box-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.rxg-smi-semantic-box-header h3 {
    margin: 0;
    flex: 1;
}

.rxg-smi-info-badge {
    display: inline-block;
    width: 18px;
    height: 18px;
    background: #f0f0f1;
    border-radius: 50%;
    text-align: center;
    line-height: 18px;
    font-size: 12px;
    font-weight: bold;
    color: #50575e;
    cursor: help;
}

.rxg-smi-semantic-terms {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
}

.rxg-smi-semantic-term {
    display: flex;
    flex-direction: column;
    gap: 5px;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}

.rxg-smi-term-text {
    font-weight: 500;
}

.rxg-smi-term-weight-bar {
    width: 100%;
    height: 5px;
    background: #e0e0e0;
    border-radius: 2px;
}

.rxg-smi-term-weight-fill {
    height: 100%;
    background: #2271b1;
    border-radius: 2px;
}

.rxg-smi-term-occurences {
    font-size: 0.8em;
    color: #666;
    text-align: right;
}

.rxg-smi-anchor-suggestions {
    width: 100%;
}

.rxg-smi-anchor-selector {
    width: 100%;
}

.rxg-smi-custom-anchor {
    width: 100%;
    margin-top: 5px;
}

.rxg-smi-thematic-map {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.rxg-smi-thematic-visualization {
    grid-column: 1 / -1;
}

.rxg-smi-thematic-terms h4,
.rxg-smi-thematic-pages h4,
.rxg-smi-thematic-visualization h4 {
    margin: 0 0 10px 0;
    font-size: 1em;
    font-weight: 600;
    color: #333;
}

.rxg-smi-term-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.rxg-smi-term-tag {
    background: #f0f8ff;
    border: 1px solid #cce5ff;
    color: #0066cc;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.9em;
}

.rxg-smi-term-weight {
    opacity: 0.7;
    font-size: 0.9em;
}

.rxg-smi-thematic-pages ul {
    margin: 0;
    padding-left: 20px;
}

.rxg-smi-thematic-pages li {
    margin-bottom: 5px;
}

.rxg-smi-linked-badge {
    display: inline-block;
    padding: 2px 5px;
    font-size: 0.8em;
    background: #edfaef;
    color: #46b450;
    border-radius: 3px;
}

.rxg-smi-not-linked-badge {
    display: inline-block;
    padding: 2px 5px;
    font-size: 0.8em;
    background: #fbeaea;
    color: #dc3232;
    border-radius: 3px;
}

.rxg-smi-cluster-preview {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

.rxg-smi-cluster-graph {
    width: 100%;
    height: 250px;
    background: #f8f9fa;
    border-radius: 5px;
    position: relative;
    margin-bottom: 10px;
}

.rxg-smi-cluster-node {
    position: absolute;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #fff;
    transform: translate(-50%, -50%);
    z-index: 2;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.rxg-smi-current-node {
    background: #2271b1;
    width: 50px;
    height: 50px;
    z-index: 3;
    left: 50%;
    top: 50%;
}

.rxg-smi-linked-node {
    background: #46b450;
}

.rxg-smi-unlinked-node {
    background: #dc3232;
}

.rxg-smi-cluster-link {
    position: absolute;
    width: 2px;
    background: #ccc;
    transform-origin: top center;
    z-index: 1;
    top: var(--start-y);
    left: var(--start-x);
    height: calc(
        sqrt(
            (var(--end-x) - var(--start-x)) * (var(--end-x) - var(--start-x)) +
            (var(--end-y) - var(--start-y)) * (var(--end-y) - var(--start-y))
        ) * 1px
    );
    transform: rotate(
        calc(
            atan2(
                (var(--end-y) - var(--start-y)),
                (var(--end-x) - var(--start-x))
            ) * 1rad
        )
    );
}

.rxg-smi-link-active {
    background: #46b450;
    opacity: 0.7;
}

.rxg-smi-link-potential {
    background: #dc3232;
    opacity: 0.3;
    stroke-dasharray: 4;
}

.rxg-smi-cluster-legend {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
}

.rxg-smi-legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9em;
}

.rxg-smi-legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.rxg-smi-current-color {
    background: #2271b1;
}

.rxg-smi-linked-color {
    background: #46b450;
}

.rxg-smi-unlinked-color {
    background: #dc3232;
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
    
    // Infobulle pour les badges d'information
    $('.rxg-smi-info-badge').tooltip({
        position: {
            my: 'center bottom-10',
            at: 'center top'
        }
    });
});
</script>