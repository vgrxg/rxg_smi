<?php
/**
 * Template pour l'analyse des textes d'ancre
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-anchors-summary">
        <div class="rxg-smi-anchors-stats">
            <?php
            global $wpdb;
            $table_anchors = $wpdb->prefix . 'rxg_smi_anchor_stats';
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            
            // Statistiques globales
            $total_anchors = $wpdb->get_var("SELECT COUNT(DISTINCT anchor_text) FROM $table_anchors");
            $total_anchor_uses = $wpdb->get_var("SELECT SUM(count) FROM $table_anchors");
            $avg_diversity = $wpdb->get_var("SELECT AVG(anchor_diversity_score) FROM $table_pages WHERE inbound_links_count > 0");
            ?>
            
            <div class="rxg-smi-anchors-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-tag"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($total_anchors); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Textes d\'ancre uniques', 'rxg-smi'); ?></span>
                </div>
            </div>
            
            <div class="rxg-smi-anchors-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-admin-links"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo intval($total_anchor_uses); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Utilisations totales', 'rxg-smi'); ?></span>
                </div>
            </div>
            
            <div class="rxg-smi-anchors-stat-box">
                <span class="rxg-smi-stat-icon dashicons dashicons-chart-bar"></span>
                <div class="rxg-smi-stat-content">
                    <span class="rxg-smi-stat-value"><?php echo round($avg_diversity, 1); ?></span>
                    <span class="rxg-smi-stat-title"><?php _e('Score de diversité moyen', 'rxg-smi'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div id="rxg-smi-anchors-tabs" class="rxg-smi-tabs">
        <ul class="rxg-smi-tabs-nav">
            <li><a href="#tabs-1"><?php _e('Textes d\'ancre populaires', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-2"><?php _e('Ancres similaires', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-3"><?php _e('Pages à faible diversité', 'rxg-smi'); ?></a></li>
            <li><a href="#tabs-4"><?php _e('Analyse d\'ancre', 'rxg-smi'); ?></a></li>
        </ul>
        
        <div id="tabs-1" class="rxg-smi-tab-content">
            <h3><?php _e('Textes d\'ancre les plus utilisés', 'rxg-smi'); ?></h3>
            <p>
                <?php _e('Cette liste présente les textes d\'ancre les plus fréquemment utilisés sur votre site. Une utilisation excessive des mêmes ancres peut indiquer une sur-optimisation.', 'rxg-smi'); ?>
            </p>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th>
                        <th><?php _e('Occurrences', 'rxg-smi'); ?></th>
                        <th><?php _e('Pages cibles', 'rxg-smi'); ?></th>
                        <th><?php _e('Longueur', 'rxg-smi'); ?></th>
                        <th><?php _e('Analyse', 'rxg-smi'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_anchors as $anchor) : 
                        $anchor_analysis = $this->anchor_analyzer->analyze_anchor_text($anchor->anchor_text);
                    ?>
                        <tr>
                            <td>
                                <span class="rxg-smi-anchor-text"><?php echo esc_html($anchor->anchor_text); ?></span>
                            </td>
                            <td><?php echo intval($anchor->total_count); ?></td>
                            <td><?php echo intval($anchor->page_count); ?></td>
                            <td><?php echo mb_strlen($anchor->anchor_text); ?></td>
                            <td>
                                <div class="rxg-smi-anchor-analysis-summary">
                                    <?php if ($anchor_analysis['is_keyword_rich']) : ?>
                                        <span class="rxg-smi-tag rxg-smi-tag-success"><?php _e('Riche en mots-clés', 'rxg-smi'); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($anchor_analysis['has_stopwords']) : ?>
                                        <span class="rxg-smi-tag rxg-smi-tag-warning"><?php _e('Contient des mots vides', 'rxg-smi'); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($anchor_analysis['word_count'] <= 1 && mb_strlen($anchor->anchor_text) < 4) : ?>
                                        <span class="rxg-smi-tag rxg-smi-tag-error"><?php _e('Trop court', 'rxg-smi'); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($anchor->total_count > 20) : ?>
                                        <span class="rxg-smi-tag rxg-smi-tag-warning"><?php _e('Suroptimisé', 'rxg-smi'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div id="tabs-2" class="rxg-smi-tab-content">
            <h3><?php _e('Textes d\'ancre similaires', 'rxg-smi'); ?></h3>
            <p>
                <?php _e('Cette section identifie les textes d\'ancre qui sont très similaires entre eux. Vous pourriez envisager de les uniformiser ou de les diversifier.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($similar_anchors)) : ?>
                <div class="rxg-smi-similar-anchors">
                    <?php foreach ($similar_anchors as $pair) : ?>
                        <div class="rxg-smi-similar-pair">
                            <div class="rxg-smi-similar-pair-header">
                                <span class="rxg-smi-similarity-score">
                                    <?php echo intval($pair['similarity']); ?>% <?php _e('de similarité', 'rxg-smi'); ?>
                                </span>
                            </div>
                            
                            <div class="rxg-smi-similar-anchors-pair">
                                <div class="rxg-smi-similar-anchor">
                                    <span class="rxg-smi-anchor-text"><?php echo esc_html($pair['anchor1']); ?></span>
                                    <span class="rxg-smi-anchor-count"><?php echo intval($pair['count1']); ?> <?php _e('occurrences', 'rxg-smi'); ?></span>
                                </div>
                                
                                <div class="rxg-smi-similarity-arrow">
                                    <span class="dashicons dashicons-arrow-right-alt"></span>
                                </div>
                                
                                <div class="rxg-smi-similar-anchor">
                                    <span class="rxg-smi-anchor-text"><?php echo esc_html($pair['anchor2']); ?></span>
                                    <span class="rxg-smi-anchor-count"><?php echo intval($pair['count2']); ?> <?php _e('occurrences', 'rxg-smi'); ?></span>
                                </div>
                            </div>
                            
                            <div class="rxg-smi-similar-pair-recommendation">
                                <?php
                                // Générer une recommandation
                                if ($pair['count1'] > $pair['count2']) {
                                    printf(__('Suggestion: Remplacer "%s" par "%s" pour une meilleure cohérence.', 'rxg-smi'), 
                                        esc_html($pair['anchor2']), 
                                        esc_html($pair['anchor1'])
                                    );
                                } else {
                                    printf(__('Suggestion: Remplacer "%s" par "%s" pour une meilleure cohérence.', 'rxg-smi'), 
                                        esc_html($pair['anchor1']), 
                                        esc_html($pair['anchor2'])
                                    );
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php _e('Aucun texte d\'ancre similaire n\'a été détecté.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <div id="tabs-3" class="rxg-smi-tab-content">
            <h3><?php _e('Pages avec une faible diversité d\'ancres', 'rxg-smi'); ?></h3>
            <p>
                <?php _e('Ces pages reçoivent plusieurs liens internes mais avec peu de variations dans les textes d\'ancre. Diversifier les ancres peut améliorer le référencement.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($low_diversity_pages)) : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Page', 'rxg-smi'); ?></th>
                            <th><?php _e('Score de diversité', 'rxg-smi'); ?></th>
                            <th><?php _e('Liens entrants', 'rxg-smi'); ?></th>
                            <th><?php _e('Ancres uniques', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_diversity_pages as $page) : 
                            // Récupérer le nombre d'ancres uniques
                            $unique_anchors = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(DISTINCT anchor_text) FROM $table_anchors WHERE page_id = %d",
                                $page->id
                            ));
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url($page->url); ?>" target="_blank">
                                        <?php echo esc_html($page->title); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="rxg-smi-diversity-meter">
                                        <div class="rxg-smi-diversity-fill" style="width: <?php echo esc_attr($page->anchor_diversity_score); ?>%"></div>
                                        <span class="rxg-smi-diversity-value"><?php echo round($page->anchor_diversity_score, 1); ?>%</span>
                                    </div>
                                </td>
                                <td><?php echo intval($page->inbound_links_count); ?></td>
                                <td><?php echo intval($unique_anchors); ?></td>
                                <td>
                                    <a href="#" class="button button-small rxg-smi-view-anchors" data-page-id="<?php echo intval($page->id); ?>" data-page-title="<?php echo esc_attr($page->title); ?>">
                                        <?php _e('Voir ancres', 'rxg-smi'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id . '&direction=inbound')); ?>" class="button button-small">
                                        <?php _e('Voir liens', 'rxg-smi'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('Toutes les pages ont une bonne diversité de textes d\'ancre.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <div id="tabs-4" class="rxg-smi-tab-content">
            <h3><?php _e('Analyseur de texte d\'ancre', 'rxg-smi'); ?></h3>
            <p>
                <?php _e('Utilisez cet outil pour analyser un texte d\'ancre spécifique et obtenir des suggestions d\'amélioration.', 'rxg-smi'); ?>
            </p>
            
            <div class="rxg-smi-anchor-analyzer">
                <div class="rxg-smi-anchor-input">
                    <label for="rxg-smi-anchor-to-analyze"><?php _e('Texte d\'ancre à analyser:', 'rxg-smi'); ?></label>
                    <input type="text" id="rxg-smi-anchor-to-analyze" class="regular-text" placeholder="<?php esc_attr_e('Entrez un texte d\'ancre...', 'rxg-smi'); ?>">
                    <button id="rxg-smi-analyze-anchor" class="button button-primary"><?php _e('Analyser', 'rxg-smi'); ?></button>
                </div>
                
                <div id="rxg-smi-anchor-analysis-results" class="rxg-smi-anchor-analysis-results" style="display: none;">
                    <h4><?php _e('Résultats de l\'analyse', 'rxg-smi'); ?></h4>
                    
                    <div class="rxg-smi-anchor-analysis-metrics">
                        <div class="rxg-smi-analysis-metric">
                            <span class="rxg-smi-metric-label"><?php _e('Longueur:', 'rxg-smi'); ?></span>
                            <span id="rxg-smi-length-value" class="rxg-smi-metric-value"></span>
                        </div>
                        
                        <div class="rxg-smi-analysis-metric">
                            <span class="rxg-smi-metric-label"><?php _e('Mots:', 'rxg-smi'); ?></span>
                            <span id="rxg-smi-words-value" class="rxg-smi-metric-value"></span>
                        </div>
                        
                        <div class="rxg-smi-analysis-metric">
                            <span class="rxg-smi-metric-label"><?php _e('Mots-clés:', 'rxg-smi'); ?></span>
                            <span id="rxg-smi-keywords-value" class="rxg-smi-metric-value"></span>
                        </div>
                    </div>
                    
                    <div class="rxg-smi-anchor-analysis-details">
                        <div id="rxg-smi-anchor-qualities" class="rxg-smi-anchor-qualities"></div>
                        
                        <div id="rxg-smi-anchor-suggestions" class="rxg-smi-anchor-suggestions">
                            <h5><?php _e('Suggestions d\'amélioration:', 'rxg-smi'); ?></h5>
                            <ul id="rxg-smi-suggestions-list"></ul>
                        </div>
                        
                        <div id="rxg-smi-anchor-existing" class="rxg-smi-anchor-existing">
                            <h5><?php _e('Utilisation existante:', 'rxg-smi'); ?></h5>
                            <div id="rxg-smi-existing-usage"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fenêtre modale pour afficher les ancres d'une page -->
    <div id="rxg-smi-anchor-modal" class="rxg-smi-modal">
        <div class="rxg-smi-modal-content">
            <div class="rxg-smi-modal-header">
                <h2 id="rxg-smi-modal-title"></h2>
                <span class="rxg-smi-modal-close">&times;</span>
            </div>
            <div class="rxg-smi-modal-body">
                <div id="rxg-smi-page-anchors-loading" class="rxg-smi-loading">
                    <span class="spinner is-active"></span>
                    <p><?php _e('Chargement des données...', 'rxg-smi'); ?></p>
                </div>
                <div id="rxg-smi-page-anchors-content"></div>
            </div>
            <div class="rxg-smi-modal-footer">
                <button class="button rxg-smi-modal-close"><?php _e('Fermer', 'rxg-smi'); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.rxg-smi-anchors-summary {
    margin: 20px 0;
}

.rxg-smi-anchors-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-anchors-stat-box {
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

.rxg-smi-stat-value {
    display: block;
    font-size: 24px;
    font-weight: 600;
    line-height: 1.2;
}

.rxg-smi-stat-title {
    display: block;
    font-size: 14px;
    color: #50575e;
}

.rxg-smi-anchor-text {
    font-weight: 500;
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
}

.rxg-smi-anchor-analysis-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.rxg-smi-tag {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
    font-weight: 500;
}

.rxg-smi-tag-success {
    background: #edfaef;
    color: #46b450;
}

.rxg-smi-tag-warning {
    background: #fff8e5;
    color: #ffb900;
}

.rxg-smi-tag-error {
    background: #fbeaea;
    color: #dc3232;
}

.rxg-smi-similar-anchors {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 15px;
}

.rxg-smi-similar-pair {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 15px;
    border-left: 3px solid #2271b1;
}

.rxg-smi-similar-pair-header {
    margin-bottom: 10px;
}

.rxg-smi-similarity-score {
    font-weight: 600;
    color: #2271b1;
}

.rxg-smi-similar-anchors-pair {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.rxg-smi-similar-anchor {
    flex: 1;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}

.rxg-smi-anchor-count {
    display: block;
    font-size: 0.8em;
    color: #50575e;
    margin-top: 5px;
}

.rxg-smi-similarity-arrow {
    color: #50575e;
}

.rxg-smi-similar-pair-recommendation {
    background: #f0f8ff;
    padding: 10px;
    border-radius: 5px;
    font-style: italic;
}

.rxg-smi-diversity-meter {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.rxg-smi-diversity-fill {
    height: 100%;
    background: #2271b1;
    border-radius: 10px;
}

.rxg-smi-diversity-value {
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

.rxg-smi-diversity-fill.low {
    background: #dc3232;
}

.rxg-smi-diversity-fill.medium {
    background: #ffb900;
}

.rxg-smi-diversity-fill.high {
    background: #46b450;
}

.rxg-smi-anchor-analyzer {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-top: 15px;
}

.rxg-smi-anchor-input {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
}

.rxg-smi-anchor-input label {
    min-width: 150px;
}

.rxg-smi-anchor-analysis-results {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
}

.rxg-smi-anchor-analysis-metrics {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 15px;
}

.rxg-smi-analysis-metric {
    background: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    min-width: 100px;
}

.rxg-smi-metric-label {
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
}

.rxg-smi-metric-value {
    font-size: 1.2em;
    font-weight: 600;
}

.rxg-smi-anchor-analysis-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 15px;
}

.rxg-smi-anchor-qualities {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    grid-column: 1 / -1;
}

.rxg-smi-anchor-suggestions, .rxg-smi-anchor-existing {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
}

.rxg-smi-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.rxg-smi-modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 80%;
    max-width: 800px;
    max-height: 80%;
    display: flex;
    flex-direction: column;
}

.rxg-smi-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e5e5;
}

.rxg-smi-modal-header h2 {
    margin: 0;
    font-size: 1.5em;
}

.rxg-smi-modal-close {
    color: #aaa;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.rxg-smi-modal-close:hover {
    color: #000;
}

.rxg-smi-modal-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
}

.rxg-smi-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e5e5;
    text-align: right;
}

.rxg-smi-loading {
    text-align: center;
    padding: 20px;
}

.rxg-smi-loading .spinner {
    float: none;
    margin: 0 auto 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialiser les onglets
    $("#rxg-smi-anchors-tabs").tabs();
    
    // Analyseur d'ancre
    $('#rxg-smi-analyze-anchor').on('click', function() {
        var anchorText = $('#rxg-smi-anchor-to-analyze').val().trim();
        
        if (anchorText === '') {
            return;
        }
        
        // Analyse côté client simple
        var length = anchorText.length;
        var words = anchorText.split(/\s+/).filter(function(word) { return word.length > 0; });
        var wordCount = words.length;
        
        var stopwords = ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'de', 'du', 'au', 'aux', 'ce', 'cette', 'ces'];
        var keywords = words.filter(function(word) {
            return word.length >= 4 && !stopwords.includes(word.toLowerCase());
        });
        
        // Afficher les résultats
        $('#rxg-smi-length-value').text(length);
        $('#rxg-smi-words-value').text(wordCount);
        $('#rxg-smi-keywords-value').text(keywords.length);
        
        // Qualités
        var qualities = [];
        if (keywords.length > 0 && keywords.length / wordCount >= 0.5) {
            qualities.push('<span class="rxg-smi-tag rxg-smi-tag-success"><?php _e('Riche en mots-clés', 'rxg-smi'); ?></span>');
        }
        
        if (words.some(function(word) { return stopwords.includes(word.toLowerCase()); })) {
            qualities.push('<span class="rxg-smi-tag rxg-smi-tag-warning"><?php _e('Contient des mots vides', 'rxg-smi'); ?></span>');
        }
        
        if (wordCount <= 1 && length < 4) {
            qualities.push('<span class="rxg-smi-tag rxg-smi-tag-error"><?php _e('Trop court', 'rxg-smi'); ?></span>');
        }
        
        if (length > 60) {
            qualities.push('<span class="rxg-smi-tag rxg-smi-tag-warning"><?php _e('Très long', 'rxg-smi'); ?></span>');
        }
        
        $('#rxg-smi-anchor-qualities').html(qualities.join(' ') || '<p><?php _e('Aucune qualité particulière détectée.', 'rxg-smi'); ?></p>');
        
        // Suggestions
        var suggestions = [];
        if (wordCount <= 1 && length < 4) {
            suggestions.push('<li><?php _e('Cette ancre est trop courte. Utilisez des textes plus descriptifs.', 'rxg-smi'); ?></li>');
        }
        
        if (wordCount > 0 && keywords.length === 0) {
            suggestions.push('<li><?php _e('Aucun mot-clé significatif détecté. Ajoutez des termes pertinents.', 'rxg-smi'); ?></li>');
        }
        
        if (length > 60) {
            suggestions.push('<li><?php _e('Cette ancre est très longue. Envisagez de la raccourcir pour plus d\'impact.', 'rxg-smi'); ?></li>');
        }
        
        $('#rxg-smi-suggestions-list').html(suggestions.join('') || '<li><?php _e('Pas de suggestions d\'amélioration. Cette ancre semble bien équilibrée.', 'rxg-smi'); ?></li>');
        
        // Vérifier si cette ancre est déjà utilisée sur le site
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rxg_smi_check_anchor_usage',
                anchor: anchorText,
                nonce: rxg_smi.nonce
            },
            success: function(response) {
                if (response.success && response.data.usage > 0) {
                    $('#rxg-smi-existing-usage').html(
                        '<p>' + 
                        '<?php _e('Cette ancre est utilisée', 'rxg-smi'); ?> <strong>' + 
                        response.data.usage + ' <?php _e('fois', 'rxg-smi'); ?></strong> ' + 
                        '<?php _e('sur', 'rxg-smi'); ?> <strong>' + 
                        response.data.pages + ' <?php _e('pages', 'rxg-smi'); ?></strong>.' +
                        '</p>'
                    );
                } else {
                    $('#rxg-smi-existing-usage').html('<p><?php _e('Cette ancre n\'est pas encore utilisée sur votre site.', 'rxg-smi'); ?></p>');
                }
            },
            error: function() {
                $('#rxg-smi-existing-usage').html('<p><?php _e('Impossible de vérifier l\'utilisation de cette ancre.', 'rxg-smi'); ?></p>');
            }
        });
        
        // Afficher les résultats
        $('#rxg-smi-anchor-analysis-results').show();
    });
    
    // Fenêtre modale pour les ancres d'une page
    $('.rxg-smi-view-anchors').on('click', function(e) {
        e.preventDefault();
        
        var pageId = $(this).data('page-id');
        var pageTitle = $(this).data('page-title');
        
        // Mise à jour du titre
        $('#rxg-smi-modal-title').text('<?php _e('Textes d\'ancre pour:', 'rxg-smi'); ?> ' + pageTitle);
        
        // Afficher la fenêtre modale
        $('#rxg-smi-anchor-modal').css('display', 'block');
        $('#rxg-smi-page-anchors-loading').show();
        $('#rxg-smi-page-anchors-content').hide();
        
        // Récupérer les données
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rxg_smi_get_page_anchors',
                page_id: pageId,
                nonce: rxg_smi.nonce
            },
            success: function(response) {
                $('#rxg-smi-page-anchors-loading').hide();
                
                if (response.success) {
                    var content = '<div class="rxg-smi-page-anchors">';
                    
                    if (response.data.details.length > 0) {
                        content += '<table class="widefat striped">';
                        content += '<thead><tr><th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th><th><?php _e('Occurrences', 'rxg-smi'); ?></th><th><?php _e('Pourcentage', 'rxg-smi'); ?></th></tr></thead>';
                        content += '<tbody>';
                        
                        $.each(response.data.details, function(i, detail) {
                            content += '<tr>';
                            content += '<td>' + detail.text + '</td>';
                            content += '<td>' + detail.count + '</td>';
                            content += '<td>' + detail.percentage + '%' + (detail.overused ? ' <span class="rxg-smi-tag rxg-smi-tag-warning"><?php _e('Suroptimisé', 'rxg-smi'); ?></span>' : '') + '</td>';
                            content += '</tr>';
                        });
                        
                        content += '</tbody></table>';
                        
                        content += '<div class="rxg-smi-anchor-suggestions">';
                        content += '<h4><?php _e('Suggestions d\'ancres alternatives', 'rxg-smi'); ?></h4>';
                        
                        // Ajouter ici une requête AJAX pour obtenir des suggestions d'ancres
                        
                        content += '</div>';
                    } else {
                        content += '<p><?php _e('Aucun texte d\'ancre trouvé pour cette page.', 'rxg-smi'); ?></p>';
                    }
                    
                    content += '</div>';
                    
                    $('#rxg-smi-page-anchors-content').html(content).show();
                } else {
                    $('#rxg-smi-page-anchors-content').html('<p><?php _e('Erreur lors de la récupération des données.', 'rxg-smi'); ?></p>').show();
                }
            },
            error: function() {
                $('#rxg-smi-page-anchors-loading').hide();
                $('#rxg-smi-page-anchors-content').html('<p><?php _e('Erreur lors de la récupération des données.', 'rxg-smi'); ?></p>').show();
            }
        });
    });
    
    // Fermer la fenêtre modale
    $('.rxg-smi-modal-close').on('click', function() {
        $('#rxg-smi-anchor-modal').css('display', 'none');
    });
    
    // Fermer la fenêtre modale en cliquant en dehors
    $(window).on('click', function(e) {
        if ($(e.target).is('#rxg-smi-anchor-modal')) {
            $('#rxg-smi-anchor-modal').css('display', 'none');
        }
    });
});
</script>
