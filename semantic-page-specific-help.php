<?php
/**
 * Template pour l'analyse sémantique d'une page spécifique avec aide intégrée
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
            <span class="rxg-smi-help-toggle"><?php _e('Aide', 'rxg-smi'); ?> <span class="dashicons dashicons-info"></span></span>
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
    gap: 10px;
}

.rxg-smi-help-toggle {
    font-size: 0.7em;
    background: #f0f0f1;
    padding: 3px 8px;
    border-radius: 3px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    color: #50575e;
}

.rxg-smi-help-toggle .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
    margin-left: 3px;
}

.rxg-smi-help-toggle:hover {
    background: #e0e0e0;
}

.rxg-smi-page-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.rxg-smi-help-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    overflow-y: auto;
    padding: 40px 0;
}

.rxg-smi-help-content {
    background: #fff;
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
    border-radius: 5px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.rxg-smi-help-section {
    margin-bottom: 25px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.rxg-smi-help-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.rxg-smi-help-close {
    margin-top: 20px;
    display: block;
    margin-left: auto;
}

/* Le reste des styles reste le même */
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
    
    // Fonctionnalité d'aide
    $(".rxg-smi-help-toggle").on("click", function() {
        $(".rxg-smi-help-container").fadeIn(300);
    });
    
    $(".rxg-smi-help-close").on("click", function() {
        $(".rxg-smi-help-container").fadeOut(200);
    });
    
    // Fermer l'aide en cliquant en dehors de la boîte de contenu
    $(".rxg-smi-help-container").on("click", function(e) {
        if ($(e.target).is(".rxg-smi-help-container")) {
            $(this).fadeOut(200);
        }
    });
    
    // Fermer l'aide avec la touche ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape" && $(".rxg-smi-help-container").is(":visible")) {
            $(".rxg-smi-help-container").fadeOut(200);
        }
    });
});
</script>
    </div>
    
    <!-- Aide contextuelle sur l'analyse sémantique -->
    <div class="rxg-smi-help-container" style="display:none;">
        <div class="rxg-smi-help-content">
            <h2><?php _e('Comprendre l\'Analyse Sémantique d\'une Page', 'rxg-smi'); ?></h2>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Termes clés', 'rxg-smi'); ?></h3>
                <p><?php _e('Cette section présente les termes les plus significatifs identifiés dans cette page.', 'rxg-smi'); ?></p>
                <ul>
                    <li><strong><?php _e('Terme', 'rxg-smi'); ?></strong>: <?php _e('Mot significatif extrait du contenu.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Occurrences', 'rxg-smi'); ?></strong>: <?php _e('Nombre de fois où le terme apparaît dans la page.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Poids TF-IDF', 'rxg-smi'); ?></strong>: <?php _e('Score de pertinence du terme pour cette page. Plus le score est élevé, plus le terme est unique et représentatif de cette page par rapport au reste du site.', 'rxg-smi'); ?></li>
                </ul>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Suggestions de liens sémantiques', 'rxg-smi'); ?></h3>
                <p><?php _e('Cette section identifie des pages thématiquement proches mais non encore liées.', 'rxg-smi'); ?></p>
                <ul>
                    <li><strong><?php _e('Page', 'rxg-smi'); ?></strong>: <?php _e('Page thématiquement proche de celle-ci.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Similarité', 'rxg-smi'); ?></strong>: <?php _e('Degré de proximité thématique entre les deux pages (de 0 à 100%).', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Texte d\'ancre suggéré', 'rxg-smi'); ?></strong>: <?php _e('Propositions de textes d\'ancre optimaux pour créer un lien vers cette page, basés sur les termes communs aux deux pages.', 'rxg-smi'); ?></li>
                </ul>
                <p><?php _e('Utilisez le bouton "Copier HTML" pour obtenir directement le code HTML du lien avec l\'ancre choisie.', 'rxg-smi'); ?></p>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Carte thématique', 'rxg-smi'); ?></h3>
                <p><?php _e('Représentation visuelle du cluster thématique auquel appartient cette page.', 'rxg-smi'); ?></p>
                <ul>
                    <li><strong><?php _e('Thématiques principales', 'rxg-smi'); ?></strong>: <?php _e('Termes les plus représentatifs du cluster entier.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Pages du même cluster', 'rxg-smi'); ?></strong>: <?php _e('Autres pages partageant la même thématique.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Visualisation', 'rxg-smi'); ?></strong>: <?php _e('La représentation graphique montre les relations entre la page actuelle (point central) et les autres pages du cluster.', 'rxg-smi'); ?></li>
                </ul>
                <p><?php _e('Les pages déjà liées sont indiquées en vert, celles non liées en rouge. Idéalement, toutes les pages d\'un même cluster devraient être liées entre elles pour créer un silo thématique solide.', 'rxg-smi'); ?></p>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Comment utiliser ces informations', 'rxg-smi'); ?></h3>
                <ol>
                    <li><?php _e('Identifiez les termes clés qui pourraient être mieux mis en valeur dans le contenu.', 'rxg-smi'); ?></li>
                    <li><?php _e('Créez des liens vers les pages suggérées en utilisant les textes d\'ancre recommandés.', 'rxg-smi'); ?></li>
                    <li><?php _e('Renforcez le cluster thématique en assurant que toutes ses pages sont bien interconnectées.', 'rxg-smi'); ?></li>
                    <li><?php _e('Vérifiez la diversité des ancres pointant vers cette page pour éviter la sur-optimisation.', 'rxg-smi'); ?></li>
                </ol>
            </div>
            
            <button class="button rxg-smi-help-close"><?php _e('Fermer l\'aide', 'rxg-smi'); ?></button>
        </div>
    </div>