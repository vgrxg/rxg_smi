<?php
/**
 * Template pour la vue détaillée d'un cluster thématique
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Cluster', 'rxg-smi'); ?> #<?php echo intval($cluster_id); ?></h1>
    
    <div class="rxg-smi-cluster-header">
        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic')); ?>" class="rxg-smi-back-link">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php _e('Retour à l\'analyse sémantique', 'rxg-smi'); ?>
        </a>
        
        <div class="rxg-smi-cluster-stats">
            <div class="rxg-smi-cluster-stat">
                <span class="rxg-smi-stat-label"><?php _e('Pages dans ce cluster:', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo count($cluster_pages); ?></span>
            </div>
            
            <div class="rxg-smi-cluster-stat">
                <span class="rxg-smi-stat-label"><?php _e('Termes principaux:', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value">
                    <?php 
                    $top_terms = array_slice($cluster_terms, 0, 3);
                    $term_names = array_map(function($term) {
                        return $term->term;
                    }, $top_terms);
                    echo esc_html(implode(', ', $term_names));
                    ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="rxg-smi-cluster-content">
        <div class="rxg-smi-cluster-terms">
            <h2><?php _e('Termes caractéristiques', 'rxg-smi'); ?></h2>
               <div class="rxg-smi-term-cloud">
                <?php foreach ($cluster_terms as $index => $term) : 
                    // Calculer la taille relative du terme (entre 1 et 5)
                    $size = 1 + floor(4 * $index / count($cluster_terms));
                    $size = 6 - $size; // Inverser pour que les plus importants soient plus grands
                ?>
                    <span class="rxg-smi-cloud-term rxg-smi-term-size-<?php echo $size; ?>">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-term-view&term=' . urlencode($term->term))); ?>">
                            <?php echo esc_html($term->term); ?>
                            <span class="rxg-smi-term-weight">(<?php echo round($term->total_weight, 1); ?>)</span>
                        </a>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="rxg-smi-cluster-pages">
            <h2><?php _e('Pages du cluster', 'rxg-smi'); ?></h2>
            
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Titre', 'rxg-smi'); ?></th>
                        <th><?php _e('Type', 'rxg-smi'); ?></th>
                        <th><?php _e('Liens entrants', 'rxg-smi'); ?></th>
                        <th><?php _e('Liens sortants', 'rxg-smi'); ?></th>
                        <th><?php _e('Score', 'rxg-smi'); ?></th>
                        <th><?php _e('Actions', 'rxg-smi'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cluster_pages as $page) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url($page->url); ?>" target="_blank"><?php echo esc_html($page->title); ?></a></td>
                            <td><?php echo esc_html($page->post_type); ?></td>
                            <td><?php echo intval($page->inbound_links_count); ?></td>
                            <td><?php echo intval($page->outbound_links_count); ?></td>
                            <td><?php echo number_format($page->juice_score, 2); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $page->id)); ?>" class="button button-small">
                                    <?php _e('Analyse sémantique', 'rxg-smi'); ?>
                                </a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id)); ?>" class="button button-small">
                                    <?php _e('Voir liens', 'rxg-smi'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="rxg-smi-cluster-suggestions">
            <h2><?php _e('Opportunités de maillage', 'rxg-smi'); ?></h2>
            
            <?php
            // Tableau pour stocker les liens manquants entre les pages du cluster
            $missing_links = array();
            
            // Vérifier les liens entre chaque paire de pages du cluster
            foreach ($cluster_pages as $page1) {
                foreach ($cluster_pages as $page2) {
                    if ($page1->id === $page2->id) continue;
                    
                    // Vérifier si un lien existe entre ces deux pages
                    $link_exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}rxg_smi_links 
                        WHERE source_id = %d AND target_id = %d",
                        $page1->id, $page2->id
                    ));
                    
                    if ($link_exists == 0) {
                        $missing_links[] = array(
                            'source' => $page1,
                            'target' => $page2
                        );
                    }
                }
            }
            
            // Limiter aux 10 premiers liens manquants
            $missing_links = array_slice($missing_links, 0, 10);
            ?>
            
            <?php if (!empty($missing_links)) : ?>
                <p>
                    <?php _e('Ces pages partagent des thématiques communes mais ne sont pas liées entre elles. Voici des suggestions de liens à créer pour renforcer votre cluster.', 'rxg-smi'); ?>
                </p>
                
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Page source', 'rxg-smi'); ?></th>
                            <th><?php _e('Page cible', 'rxg-smi'); ?></th>
                            <th><?php _e('Actions', 'rxg-smi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($missing_links as $link) : ?>
                            <tr>
                                <td><a href="<?php echo esc_url($link['source']->url); ?>" target="_blank"><?php echo esc_html($link['source']->title); ?></a></td>
                                <td><a href="<?php echo esc_url($link['target']->url); ?>" target="_blank"><?php echo esc_html($link['target']->title); ?></a></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $link['source']->post_id . '&action=edit')); ?>" class="button button-small">
                                        <?php _e('Éditer source', 'rxg-smi'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-opportunities&page_id=' . $link['source']->id)); ?>" class="button button-small">
                                        <?php _e('Voir opportunités', 'rxg-smi'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>
                    <?php _e('Excellent ! Toutes les pages de ce cluster sont déjà liées entre elles.', 'rxg-smi'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.rxg-smi-cluster-header {
    display: flex;
    flex-direction: column;
    gap: 15px;
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

.rxg-smi-cluster-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-cluster-stat {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    min-width: 200px;
}

.rxg-smi-stat-label {
    font-weight: 500;
    margin-right: 5px;
}

.rxg-smi-stat-value {
    font-weight: 600;
}

.rxg-smi-cluster-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.rxg-smi-cluster-terms, 
.rxg-smi-cluster-pages, 
.rxg-smi-cluster-suggestions {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    padding: 3px 8px;
    border-radius: 3px;
    background: #f1f1f1;
    transition: all 0.2s ease;
}

.rxg-smi-cloud-term:hover {
    background: #e0e0e0;
    transform: scale(1.05);
}

.rxg-smi-term-weight {
    font-size: 0.7em;
    color: #777;
    vertical-align: super;
}

.rxg-smi-term-size-1 { font-size: 0.8em; opacity: 0.7; }
.rxg-smi-term-size-2 { font-size: 1em; opacity: 0.8; }
.rxg-smi-term-size-3 { font-size: 1.2em; opacity: 0.9; }
.rxg-smi-term-size-4 { font-size: 1.5em; font-weight: 500; }
.rxg-smi-term-size-5 { font-size: 1.8em; font-weight: 600; }
</style>