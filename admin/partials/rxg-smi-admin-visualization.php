<?php
/**
 * Template pour la page de visualisation et export
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-tabs">
        <div class="nav-tab-wrapper">
            <a href="#tab-visualization" class="nav-tab nav-tab-active"><?php _e('Visualisation', 'rxg-smi'); ?></a>
            <a href="#tab-export" class="nav-tab"><?php _e('Exporter', 'rxg-smi'); ?></a>
            <a href="#tab-guide" class="nav-tab"><?php _e('Guide d\'utilisation', 'rxg-smi'); ?></a>
        </div>
        
        <!-- Onglet de visualisation D3.js -->
        <div id="tab-visualization" class="tab-content">
            <div class="rxg-smi-visualization-header">
                <h2><?php _e('Visualisation du maillage interne', 'rxg-smi'); ?></h2>
                <div class="rxg-smi-controls">
                    <label for="rxg-smi-filter-cluster"><?php _e('Filtrer par cluster:', 'rxg-smi'); ?></label>
                    <select id="rxg-smi-filter-cluster">
                        <option value="all"><?php _e('Tous les clusters', 'rxg-smi'); ?></option>
                    </select>
                    
                    <label for="rxg-smi-filter-depth"><?php _e('Profondeur max:', 'rxg-smi'); ?></label>
                    <select id="rxg-smi-filter-depth">
                        <option value="all"><?php _e('Toutes', 'rxg-smi'); ?></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5+</option>
                    </select>
                    
                    <label for="rxg-smi-node-size"><?php _e('Taille des nœuds:', 'rxg-smi'); ?></label>
                    <select id="rxg-smi-node-size">
                        <option value="inbound_links_count"><?php _e('Liens entrants', 'rxg-smi'); ?></option>
                        <option value="juice_score"><?php _e('Score de jus', 'rxg-smi'); ?></option>
                        <option value="word_count"><?php _e('Nombre de mots', 'rxg-smi'); ?></option>
                    </select>
                </div>
            </div>

            <div class="rxg-smi-visualization-container">
                <div id="rxg-smi-loading" class="rxg-smi-loading">
                    <span class="spinner is-active"></span>
                    <p><?php _e('Chargement des données...', 'rxg-smi'); ?></p>
                </div>
                <div id="rxg-smi-graph"></div>
                <button id="rxg-smi-generate-viz" class="button button-primary" style="margin-top:15px; display:block;">
                    <?php _e('Générer la visualisation', 'rxg-smi'); ?>
                </button>
            </div>
            
            <div class="rxg-smi-legend">
                <h3><?php _e('Légende', 'rxg-smi'); ?></h3>
                <div id="rxg-smi-cluster-legend"></div>
                <div class="rxg-smi-legend-item">
                    <div class="rxg-smi-legend-symbol rxg-smi-node-large"></div>
                    <div class="rxg-smi-legend-label"><?php _e('Pages avec beaucoup de liens entrants', 'rxg-smi'); ?></div>
                </div>
                <div class="rxg-smi-legend-item">
                    <div class="rxg-smi-legend-symbol rxg-smi-node-small"></div>
                    <div class="rxg-smi-legend-label"><?php _e('Pages avec peu de liens entrants', 'rxg-smi'); ?></div>
                </div>
            </div>
            
            <div class="rxg-smi-selected-page">
                <h3><?php _e('Détails de la page sélectionnée', 'rxg-smi'); ?></h3>
                <div id="rxg-smi-page-details">
                    <p><?php _e('Cliquez sur une page dans le graphe pour voir ses détails.', 'rxg-smi'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Reste du contenu des onglets... -->
        
        <!-- Onglet d'export -->
        <div id="tab-export" class="tab-content" style="display:none;">
            <!-- Contenu inchangé... -->
        </div>
        
        <!-- Onglet de guide -->
        <div id="tab-guide" class="tab-content" style="display:none;">
            <!-- Contenu inchangé... -->
        </div>
    </div>

<!-- Élément de débogage -->
<div id="rxg-smi-debug" style="margin-top:20px; border:1px solid #ccc; padding:10px; display:none;">
    <h3>Informations de débogage</h3>
    <button id="rxg-smi-test-ajax" class="button">Tester AJAX</button>
    <div id="rxg-smi-debug-output"></div>
</div>

<script>
jQuery(document).ready(function($) {
    // Afficher la section de débogage si mode debug actif
    if (typeof rxgSmiData !== 'undefined' && rxgSmiData.debugMode) {
        $('#rxg-smi-debug').show();
        $('#rxg-smi-debug-output').append('<p>JavaScript fonctionne! ' + new Date().toLocaleTimeString() + '</p>');
    
        $('#rxg-smi-test-ajax').on('click', function() {
            $('#rxg-smi-debug-output').append('<p>Test AJAX en cours...</p>');
            
            $.ajax({
                url: rxgSmiData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rxg_smi_get_visualization_data',
                    nonce: rxgSmiData.nonce
                },
                success: function(response) {
                    $('#rxg-smi-debug-output').append('<p>Réponse AJAX reçue ✓</p>');
                    console.log('Réponse AJAX:', response);
                },
                error: function(xhr, status, error) {
                    $('#rxg-smi-debug-output').append('<p>Erreur AJAX: ' + error + '</p>');
                    console.error('Erreur AJAX:', status, error, xhr.responseText);
                }
            });
        });
    }
});
</script>

</div>

