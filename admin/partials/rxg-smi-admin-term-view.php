<?php
/**
 * Template pour la vue détaillée d'un terme sémantique
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Terme :', 'rxg-smi'); ?> "<?php echo esc_html($term); ?>"</h1>
    
    <div class="rxg-smi-term-header">
        <a href="<?php echo esc_url(wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=rxg-smi-semantic')); ?>" class="rxg-smi-back-link">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php _e('Retour', 'rxg-smi'); ?>
        </a>
        
        <div class="rxg-smi-term-stats">
            <div class="rxg-smi-term-stat">
                <span class="rxg-smi-stat-label"><?php _e('Pages contenant ce terme :', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value"><?php echo count($term_occurrences); ?></span>
            </div>
            
            <div class="rxg-smi-term-stat">
                <span class="rxg-smi-stat-label"><?php _e('Poids total :', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value">
                    <?php 
                    $total_weight = 0;
                    foreach ($term_occurrences as $occurrence) {
                        $total_weight += $occurrence['weight'];
                    }
                    echo number_format($total_weight, 2); 
                    ?>
                </span>
            </div>
            
            <?php if (!empty($clusters)) : ?>
            <div class="rxg-smi-term-stat">
                <span class="rxg-smi-stat-label"><?php _e('Présent dans :', 'rxg-smi'); ?></span>
                <span class="rxg-smi-stat-value">
                    <?php echo count($clusters); ?> <?php _e('clusters', 'rxg-smi'); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="rxg-smi-term-content">
        <div class="rxg-smi-term-occurrences">
            <h2><?php _e('Occurrences et contextes', 'rxg-smi'); ?></h2>
            <p class="description">
                <?php _e('Cette liste montre les pages où le terme apparaît, triées par pertinence. Pour chaque page, un extrait du contexte est affiché.', 'rxg-smi'); ?>
            </p>
            
            <?php if (!empty($term_occurrences)) : ?>
                <div class="rxg-smi-occurrences-list">
                    <?php foreach ($term_occurrences as $occurrence) : ?>
                        <div class="rxg-smi-occurrence-card">
                            <div class="rxg-smi-occurrence-header">
                                <h3>
                                    <a href="<?php echo esc_url($occurrence['url']); ?>" target="_blank">
                                        <?php echo esc_html($occurrence['title']); ?>
                                    </a>
                                </h3>
                                <div class="rxg-smi-occurrence-meta">
                                    <span class="rxg-smi-meta-item">
                                        <strong><?php _e('Type :', 'rxg-smi'); ?></strong> 
                                        <?php echo esc_html($occurrence['post_type']); ?>
                                    </span>
                                    <span class="rxg-smi-meta-item">
                                        <strong><?php _e('Poids :', 'rxg-smi'); ?></strong> 
                                        <?php echo number_format($occurrence['weight'], 2); ?>
                                    </span>
                                    <span class="rxg-smi-meta-item">
                                        <strong><?php _e('Occurrences :', 'rxg-smi'); ?></strong> 
                                        <?php echo intval($occurrence['count']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if (!empty($occurrence['context'])) : ?>
                                <div class="rxg-smi-occurrence-context">
                                    <h4><?php _e('Contexte :', 'rxg-smi'); ?></h4>
                                    <div class="rxg-smi-context-excerpts">
                                        <?php foreach ($occurrence['context'] as $context) : ?>
                                            <div class="rxg-smi-context-excerpt">
                                                <p>
                                                    <?php echo $context; ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="rxg-smi-occurrence-actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-semantic&page_id=' . $occurrence['id'])); ?>" class="button button-small">
                                    <?php _e('Analyse sémantique', 'rxg-smi'); ?>
                                </a>
                                <a href="<?php echo esc_url(admin_url('post.php?post=' . $occurrence['post_id'] . '&action=edit')); ?>" class="button button-small">
                                    <?php _e('Éditer', 'rxg-smi'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php _e('Aucune occurrence trouvée pour ce terme.', 'rxg-smi'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($similar_terms)) : ?>
        <div class="rxg-smi-similar-terms">
            <h2><?php _e('Termes similaires', 'rxg-smi'); ?></h2>
            <p class="description">
                <?php _e('Ces termes apparaissent souvent dans les mêmes pages que le terme actuel.', 'rxg-smi'); ?>
            </p>
            
            <div class="rxg-smi-similar-terms-list">
                <?php foreach ($similar_terms as $similar_term) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-term-view&term=' . urlencode($similar_term['term']))); ?>" class="rxg-smi-similar-term">
                        <?php echo esc_html($similar_term['term']); ?>
                        <span class="rxg-smi-cooccurrence">(<?php echo intval($similar_term['count']); ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($clusters)) : ?>
        <div class="rxg-smi-term-clusters">
            <h2><?php _e('Clusters associés', 'rxg-smi'); ?></h2>
            <p class="description">
                <?php _e('Ce terme est significatif dans les clusters thématiques suivants.', 'rxg-smi'); ?>
            </p>
            
            <div class="rxg-smi-clusters-list">
                <?php foreach ($clusters as $cluster) : ?>
                    <div class="rxg-smi-cluster-card">
                        <h3><?php _e('Cluster', 'rxg-smi'); ?> #<?php echo intval($cluster['id']); ?></h3>
                        <div class="rxg-smi-cluster-info">
                            <span class="rxg-smi-cluster-meta">
                                <strong><?php _e('Pages :', 'rxg-smi'); ?></strong> 
                                <?php echo intval($cluster['page_count']); ?>
                            </span>
                            <span class="rxg-smi-cluster-meta">
                                <strong><?php _e('Poids du terme :', 'rxg-smi'); ?></strong> 
                                <?php echo number_format($cluster['term_weight'], 2); ?>
                            </span>
                        </div>
                        <div class="rxg-smi-cluster-terms">
                            <?php foreach ($cluster['terms'] as $cluster_term) : ?>
                                <span class="rxg-smi-cluster-term">
                                    <?php echo esc_html($cluster_term); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <div class="rxg-smi-cluster-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-cluster-view&cluster_id=' . $cluster['id'])); ?>" class="button button-small">
                                <?php _e('Explorer le cluster', 'rxg-smi'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.rxg-smi-term-header {
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

.rxg-smi-term-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.rxg-smi-term-stat {
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

.rxg-smi-term-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.rxg-smi-term-occurrences, 
.rxg-smi-similar-terms,
.rxg-smi-term-clusters {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-occurrences-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.rxg-smi-occurrence-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.rxg-smi-occurrence-header h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.rxg-smi-occurrence-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9em;
}

.rxg-smi-occurrence-context {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.rxg-smi-occurrence-context h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 0.95em;
    color: #50575e;
}

.rxg-smi-context-excerpts {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rxg-smi-context-excerpt {
    padding: 10px;
    background: #f0f7fc;
    border-radius: 3px;
    font-style: italic;
    color: #333;
}

.rxg-smi-context-excerpt p {
    margin: 0;
}

.rxg-smi-context-excerpt mark {
    background: #ffeb3b;
    padding: 0 3px;
    border-radius: 2px;
}

.rxg-smi-similar-terms-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.rxg-smi-similar-term {
    display: inline-block;
    padding: 5px 10px;
    background: #f0f7fc;
    border-radius: 15px;
    text-decoration: none;
    color: #2271b1;
    font-size: 0.9em;
    transition: all 0.2s ease;
}

.rxg-smi-similar-term:hover {
    background: #e0edf9;
    transform: translateY(-2px);
}

.rxg-smi-cooccurrence {
    font-size: 0.85em;
    color: #50575e;
}

.rxg-smi-clusters-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.rxg-smi-cluster-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.rxg-smi-cluster-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.rxg-smi-cluster-info {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9em;
}

.rxg-smi-cluster-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 15px;
}

.rxg-smi-cluster-term {
    display: inline-block;
    padding: 3px 8px;
    background: #fff;
    border-radius: 3px;
    font-size: 0.85em;
    color: #50575e;
}

.rxg-smi-occurrence-actions,
.rxg-smi-cluster-actions {
    display: flex;
    gap: 5px;
}

.description {
    color: #666;
    font-style: italic;
    margin-top: 5px;
}
</style>