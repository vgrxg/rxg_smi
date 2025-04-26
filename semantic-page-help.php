<?php
/**
 * Template pour l'analyse sémantique globale avec aide intégrée
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Analyse Sémantique', 'rxg-smi'); ?></h1>
    
    <div class="rxg-smi-semantic-header">
        <p class="rxg-smi-semantic-intro">
            <?php _e('L\'analyse sémantique identifie les relations thématiques entre vos pages et révèle les opportunités de maillage interne basées sur la similarité de contenu.', 'rxg-smi'); ?>
            <span class="rxg-smi-help-toggle"><?php _e('En savoir plus', 'rxg-smi'); ?> <span class="dashicons dashicons-info"></span></span>
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
    
    <!-- Aide contextuelle sur l'analyse sémantique -->
    <div class="rxg-smi-help-container" style="display:none;">
        <div class="rxg-smi-help-content">
            <h2><?php _e('Comprendre l\'Analyse Sémantique', 'rxg-smi'); ?></h2>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Ce que fait l\'analyse sémantique', 'rxg-smi'); ?></h3>
                <p><?php _e('L\'analyse sémantique examine le contenu textuel de vos pages pour identifier les relations thématiques entre elles, au-delà de la simple structure de liens.', 'rxg-smi'); ?></p>
                <ol>
                    <li><strong><?php _e('Extraction des termes significatifs', 'rxg-smi'); ?></strong>: <?php _e('Le système analyse chaque page pour identifier les mots et expressions qui représentent le mieux son contenu, en excluant les mots vides (le, la, et, etc.).', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Pondération TF-IDF', 'rxg-smi'); ?></strong>: <?php _e('Chaque terme reçoit un score basé sur sa fréquence dans la page (TF) et sa rareté sur l\'ensemble du site (IDF). Un terme qui apparaît souvent dans une page mais rarement ailleurs est considéré comme très représentatif.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Calcul de similarité', 'rxg-smi'); ?></strong>: <?php _e('Le système compare les pages entre elles pour établir leur proximité thématique en analysant le chevauchement de leurs termes significatifs.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Identification de clusters', 'rxg-smi'); ?></strong>: <?php _e('Des groupes de pages partageant des thématiques communes sont identifiés automatiquement.', 'rxg-smi'); ?></li>
                </ol>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Comment fonctionnent les clusters thématiques', 'rxg-smi'); ?></h3>
                <p><?php _e('L\'algorithme d\'identification des clusters fonctionne selon ces principes:', 'rxg-smi'); ?></p>
                <ol>
                    <li><strong><?php _e('Calcul de la similarité cosinus', 'rxg-smi'); ?></strong>: <?php _e('Chaque page est représentée par un vecteur de termes pondérés. L\'algorithme calcule la similarité cosinus entre ces vecteurs (mesure mathématique de l\'angle entre deux vecteurs).', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Seuil de similarité', 'rxg-smi'); ?></strong>: <?php _e('Deux pages sont considérées liées thématiquement si leur similarité dépasse un seuil défini (0.4 ou 40% par défaut).', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Propagation par groupes', 'rxg-smi'); ?></strong>: <?php _e('L\'algorithme commence par placer chaque page dans son propre cluster, puis fusionne progressivement les clusters dont les pages sont fortement similaires.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Filtrage des résultats', 'rxg-smi'); ?></strong>: <?php _e('Seuls les clusters contenant au moins 2 pages sont conservés pour éviter le bruit.', 'rxg-smi'); ?></li>
                </ol>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Utilisation pratique', 'rxg-smi'); ?></h3>
                <ul>
                    <li><strong><?php _e('Opportunités sémantiques', 'rxg-smi'); ?></strong>: <?php _e('Cette section identifie les paires de pages qui partagent des thématiques communes mais ne sont pas encore liées entre elles.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Clusters thématiques', 'rxg-smi'); ?></strong>: <?php _e('Explorez les groupes de pages naturellement liées par leur contenu.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Termes fréquents', 'rxg-smi'); ?></strong>: <?php _e('Visualisez les termes les plus significatifs sur l\'ensemble de votre site.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Suggestions d\'ancres', 'rxg-smi'); ?></strong>: <?php _e('Le système suggère des textes d\'ancre optimaux basés sur les termes communs entre les pages.', 'rxg-smi'); ?></li>
                </ul>
            </div>
            
            <div class="rxg-smi-help-section">
                <h3><?php _e('Avantages SEO', 'rxg-smi'); ?></h3>
                <ul>
                    <li><strong><?php _e('Renforcement des silos thématiques', 'rxg-smi'); ?></strong>: <?php _e('En liant des pages de même thématique, vous renforcez l\'autorité de votre site sur ces sujets.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Diversification des ancres', 'rxg-smi'); ?></strong>: <?php _e('Les suggestions d\'ancres favorisent une variété naturelle de textes d\'ancre.', 'rxg-smi'); ?></li>
                    <li><strong><?php _e('Découverte de connexions', 'rxg-smi'); ?></strong>: <?php _e('Identifiez des liens pertinents que vous n\'auriez pas envisagés.', 'rxg-smi'); ?></li>
                </ul>
            </div>
            
            <button class="button rxg-smi-help-close"><?php _e('Fermer l\'aide', 'rxg-smi'); ?></button>
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
/* Styles existants... */

/* Styles pour l'aide contextuelle */
.rxg-smi-help-toggle {
    display: inline-block;
    margin-left: 10px;
    color: #2271b1;
    cursor: pointer;
    vertical-align: middle;
}

.rxg-smi-help-toggle:hover {
    text-decoration: underline;
}

.rxg-smi-help-toggle .dashicons {
    vertical-align: text-top;
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
</style>

<script>
jQuery(document).ready(function($) {
    // Initialisation des onglets existants
    $("#rxg-smi-semantic-tabs").tabs();
    
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
