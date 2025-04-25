<?php
/**
 * Template pour la liste des pages
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-filters">
        <?php
        // Filtres par type de contenu
        $post_types = get_post_types(array('public' => true), 'objects');
        $current_post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
        ?>
        <div class="rxg-smi-filter">
            <label for="rxg-smi-filter-post-type"><?php _e('Type de contenu:', 'rxg-smi'); ?></label>
            <select id="rxg-smi-filter-post-type" name="post_type">
                <option value=""><?php _e('Tous les types', 'rxg-smi'); ?></option>
                <?php foreach ($post_types as $type) : ?>
                    <option value="<?php echo esc_attr($type->name); ?>" <?php selected($current_post_type, $type->name); ?>>
                        <?php echo esc_html($type->labels->singular_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php
        // Ordre et tri
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'juice_score';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
        ?>
    </div>
    
    <table class="widefat striped rxg-smi-table">
        <thead>
            <tr>
                <th class="column-title">
                    <a href="#" class="rxg-smi-sort-link" data-orderby="title" data-order="<?php echo ($orderby === 'title' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('Titre', 'rxg-smi'); ?>
                        <?php if ($orderby === 'title') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="#" class="rxg-smi-sort-link" data-orderby="url" data-order="<?php echo ($orderby === 'url' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('URL', 'rxg-smi'); ?>
                        <?php if ($orderby === 'url') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th class="column-post-type">
                    <a href="#" class="rxg-smi-sort-link" data-orderby="post_type" data-order="<?php echo ($orderby === 'post_type' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('Type', 'rxg-smi'); ?>
                        <?php if ($orderby === 'post_type') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th class="column-inbound-links">
                    <a href="#" class="rxg-smi-sort-link" data-orderby="inbound_links_count" data-order="<?php echo ($orderby === 'inbound_links_count' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('Liens entrants', 'rxg-smi'); ?>
                        <?php if ($orderby === 'inbound_links_count') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th class="column-outbound-links">
                    <a href="#" class="rxg-smi-sort-link" data-orderby="outbound_links_count" data-order="<?php echo ($orderby === 'outbound_links_count' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('Liens sortants', 'rxg-smi'); ?>
                        <?php if ($orderby === 'outbound_links_count') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th class="column-juice-score">
                    <a href="#" class="rxg-smi-sort-link" data-orderby="juice_score" data-order="<?php echo ($orderby === 'juice_score' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>">
                        <?php _e('Score', 'rxg-smi'); ?>
                        <?php if ($orderby === 'juice_score') : ?>
                            <span class="dashicons dashicons-<?php echo ($order === 'ASC') ? 'arrow-up' : 'arrow-down'; ?>"></span>
                        <?php endif; ?>
                    </a>
                </th>
                <th class="column-actions"><?php _e('Actions', 'rxg-smi'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pages)) : ?>
                <tr>
                    <td colspan="7"><?php _e('Aucune page analysée. Lancez une analyse pour voir les résultats.', 'rxg-smi'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($pages as $page) : ?>
                    <tr>
                        <td class="column-title">
                            <strong>
                                <a href="<?php echo esc_url(get_permalink($page->post_id)); ?>" target="_blank">
                                    <?php echo esc_html($page->title); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <span class="rxg-smi-url"><?php echo esc_html($page->url); ?></span>
                        </td>
                        <td class="column-post-type">
                            <?php echo esc_html($page->post_type); ?>
                        </td>
                        <td class="column-inbound-links">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id . '&direction=inbound')); ?>">
                                <?php echo intval($page->inbound_links_count); ?>
                            </a>
                        </td>
                        <td class="column-outbound-links">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id . '&direction=outbound')); ?>">
                                <?php echo intval($page->outbound_links_count); ?>
                            </a>
                        </td>
                        <td class="column-juice-score">
                            <?php echo number_format($page->juice_score, 2); ?>
                        </td>
                        <td class="column-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $page->id)); ?>" class="button button-small">
                                <?php _e('Voir les liens', 'rxg-smi'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url('post.php?post=' . $page->post_id . '&action=edit')); ?>" class="button button-small">
                                <?php _e('Éditer', 'rxg-smi'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php
    // Pagination (à implémenter plus tard)
    ?>
</div>
