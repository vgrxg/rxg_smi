<?php
/**
 * Template pour la liste des liens
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="rxg-smi-links">
            <div class="rxg-smi-filter">
                <label for="rxg-smi-page-filter"><?php _e('Sélectionner une page:', 'rxg-smi'); ?></label>
                <select id="rxg-smi-page-filter" name="page_id" onchange="this.form.submit()">
                    <option value=""><?php _e('-- Choisir une page --', 'rxg-smi'); ?></option>
                    <?php foreach ($pages as $p) : ?>
                        <option value="<?php echo intval($p->id); ?>" <?php selected($page_id, $p->id); ?>>
                            <?php echo esc_html($p->title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($page_id) : ?>
                <div class="rxg-smi-filter">
                    <label for="rxg-smi-link-direction"><?php _e('Direction:', 'rxg-smi'); ?></label>
                    <select id="rxg-smi-link-direction" name="direction" onchange="this.form.submit()">
                        <option value="outbound" <?php selected($direction, 'outbound'); ?>><?php _e('Liens sortants', 'rxg-smi'); ?></option>
                        <option value="inbound" <?php selected($direction, 'inbound'); ?>><?php _e('Liens entrants', 'rxg-smi'); ?></option>
                    </select>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if ($page_id) : ?>
        <?php
        // Récupérer les informations de la page sélectionnée
        global $wpdb;
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $selected_page = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_pages WHERE id = %d", $page_id));
        ?>
        
        <div class="rxg-smi-page-info">
            <h2>
                <?php if ($direction === 'outbound') : ?>
                    <?php printf(__('Liens sortants de : %s', 'rxg-smi'), esc_html($selected_page->title)); ?>
                <?php else : ?>
                    <?php printf(__('Liens entrants vers : %s', 'rxg-smi'), esc_html($selected_page->title)); ?>
                <?php endif; ?>
            </h2>
            <p>
                <strong><?php _e('URL:', 'rxg-smi'); ?></strong> 
                <a href="<?php echo esc_url($selected_page->url); ?>" target="_blank"><?php echo esc_html($selected_page->url); ?></a>
                | 
                <strong><?php _e('Type:', 'rxg-smi'); ?></strong> 
                <?php echo esc_html($selected_page->post_type); ?>
                | 
                <strong><?php _e('Score:', 'rxg-smi'); ?></strong> 
                <?php echo number_format($selected_page->juice_score, 2); ?>
            </p>
        </div>
        
        <table class="widefat striped rxg-smi-table">
            <thead>
                <tr>
                    <?php if ($direction === 'outbound') : ?>
                        <th><?php _e('Page de destination', 'rxg-smi'); ?></th>
                        <th><?php _e('URL', 'rxg-smi'); ?></th>
                        <th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th>
                        <th><?php _e('Attributs', 'rxg-smi'); ?></th>
                        <th><?php _e('Position', 'rxg-smi'); ?></th>
                        <th><?php _e('Statut', 'rxg-smi'); ?></th>
                    <?php else : ?>
                        <th><?php _e('Page source', 'rxg-smi'); ?></th>
                        <th><?php _e('Texte d\'ancre', 'rxg-smi'); ?></th>
                        <th><?php _e('Attributs', 'rxg-smi'); ?></th>
                        <th><?php _e('Position', 'rxg-smi'); ?></th>
                        <th><?php _e('Contexte', 'rxg-smi'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($links)) : ?>
                    <tr>
                        <td colspan="<?php echo ($direction === 'outbound') ? '6' : '5'; ?>">
                            <?php if ($direction === 'outbound') : ?>
                                <?php _e('Cette page ne contient pas de liens sortants.', 'rxg-smi'); ?>
                            <?php else : ?>
                                <?php _e('Cette page ne reçoit pas de liens entrants.', 'rxg-smi'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($links as $link) : ?>
                        <tr>
                            <?php if ($direction === 'outbound') : ?>
                                <td>
                                    <?php
                                    if ($link->target_id) {
                                        // Trouver le titre de la page cible
                                        $target_page = $wpdb->get_var($wpdb->prepare("SELECT title FROM $table_pages WHERE id = %d", $link->target_id));
                                        echo esc_html($target_page);
                                    } else {
                                        if ($link->external) {
                                            _e('Lien externe', 'rxg-smi');
                                        } else {
                                            _e('Page non analysée', 'rxg-smi');
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url($link->target_url); ?>" target="_blank">
                                        <?php echo esc_html($link->target_url); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($link->anchor_text); ?></td>
                                <td>
                                    <?php
                                    $attrs = array();
                                    if ($link->nofollow) $attrs[] = 'nofollow';
                                    if ($link->sponsored) $attrs[] = 'sponsored';
                                    if ($link->ugc) $attrs[] = 'ugc';
                                    echo esc_html(implode(', ', $attrs) ?: '—');
                                    ?>
                                </td>
                                <td><?php echo esc_html($link->position ?: '—'); ?></td>
                                <td>
                                    <?php
                                    if ($link->external) {
                                        if ($link->http_status >= 200 && $link->http_status < 300) {
                                            echo '<span class="rxg-smi-status-ok">' . $link->http_status . ' OK</span>';
                                        } elseif ($link->http_status >= 300 && $link->http_status < 400) {
                                            echo '<span class="rxg-smi-status-redirect">' . $link->http_status . ' Redirection</span>';
                                        } elseif ($link->http_status >= 400) {
                                            echo '<span class="rxg-smi-status-error">' . $link->http_status . ' Erreur</span>';
                                        } else {
                                            echo '<span class="rxg-smi-status-unknown">' . __('Non vérifié', 'rxg-smi') . '</span>';
                                        }
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </td>
                            <?php else : ?>
                                <td>
                                    <?php
                                    // Trouver le titre de la page source
                                    $source_page = $wpdb->get_row($wpdb->prepare("SELECT title, id FROM $table_pages WHERE id = %d", $link->source_id));
                                    if ($source_page) {
                                        ?>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=rxg-smi-links&page_id=' . $source_page->id)); ?>">
                                            <?php echo esc_html($source_page->title); ?>
                                        </a>
                                        <?php
                                    } else {
                                        _e('Page inconnue', 'rxg-smi');
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html($link->anchor_text); ?></td>
                                <td>
                                    <?php
                                    $attrs = array();
                                    if ($link->nofollow) $attrs[] = 'nofollow';
                                    if ($link->sponsored) $attrs[] = 'sponsored';
                                    if ($link->ugc) $attrs[] = 'ugc';
                                    echo esc_html(implode(', ', $attrs) ?: '—');
                                    ?>
                                </td>
                                <td><?php echo esc_html($link->position ?: '—'); ?></td>
                                <td>
                                    <div class="rxg-smi-context-preview">
                                        <?php echo esc_html($link->context ?: '—'); ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="rxg-smi-notice">
            <p><?php _e('Veuillez sélectionner une page pour afficher ses liens.', 'rxg-smi'); ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
    .rxg-smi-status-ok {
        color: #46b450;
        font-weight: bold;
    }
    
    .rxg-smi-status-redirect {
        color: #ffb900;
        font-weight: bold;
    }
    
    .rxg-smi-status-error {
        color: #dc3232;
        font-weight: bold;
    }
    
    .rxg-smi-status-unknown {
        color: #999;
        font-style: italic;
    }
    
    .rxg-smi-page-info {
        background: #fff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .rxg-smi-context-preview {
        max-width: 300px;
        max-height: 50px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>