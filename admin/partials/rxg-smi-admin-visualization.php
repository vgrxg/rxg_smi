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
        
        <!-- Onglet d'export -->
        <div id="tab-export" class="tab-content" style="display:none;">
            <h2><?php _e('Exporter les données du maillage', 'rxg-smi'); ?></h2>
            <p><?php _e('Téléchargez les données de maillage dans différents formats pour les analyser avec des outils externes.', 'rxg-smi'); ?></p>
            
            <div class="rxg-smi-export-options">
                <div class="rxg-smi-export-option">
                    <h3><?php _e('Format JSON', 'rxg-smi'); ?></h3>
                    <p><?php _e('Export complet des données au format JSON pour les outils de visualisation avancés.', 'rxg-smi'); ?></p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_export_json'), 'rxg_smi_export', 'rxg_smi_nonce'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Télécharger (JSON)', 'rxg-smi'); ?>
                    </a>
                </div>
                
                <div class="rxg-smi-export-option">
                    <h3><?php _e('Format Gephi (CSV)', 'rxg-smi'); ?></h3>
                    <p><?php _e('Fichiers CSV formatés pour importation dans Gephi.', 'rxg-smi'); ?></p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rxg_smi_export_csv'), 'rxg_smi_export_csv', 'rxg_smi_nonce'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Télécharger pour Gephi', 'rxg-smi'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Onglet de guide -->
        <div id="tab-guide" class="tab-content" style="display:none;">
            <h2><?php _e('Guide d\'interprétation et d\'utilisation', 'rxg-smi'); ?></h2>
            
            <div class="rxg-smi-guide-section">
                <h3><?php _e('Comprendre la visualisation', 'rxg-smi'); ?></h3>
                <p><?php _e('Le graphe représente votre site avec:', 'rxg-smi'); ?></p>
                <ul>
                    <li><?php _e('<strong>Nœuds</strong> : Pages de votre site', 'rxg-smi'); ?></li>
                    <li><?php _e('<strong>Taille des nœuds</strong> : Nombre de liens entrants (ou autre métrique sélectionnée)', 'rxg-smi'); ?></li>
                    <li><?php _e('<strong>Couleur des nœuds</strong> : Cluster thématique', 'rxg-smi'); ?></li>
                    <li><?php _e('<strong>Liens</strong> : Connexions entre les pages', 'rxg-smi'); ?></li>
                </ul>
            </div>
            
            <div class="rxg-smi-guide-section">
                <h3><?php _e('Utilisation avec Gephi', 'rxg-smi'); ?></h3>
                <ol>
                    <li><?php _e('Téléchargez l\'export au format Gephi (CSV)', 'rxg-smi'); ?></li>
                    <li><?php _e('Ouvrez Gephi et créez un nouveau projet', 'rxg-smi'); ?></li>
                    <li><?php _e('Dans le "Laboratoire de données", importez d\'abord nodes.csv puis edges.csv', 'rxg-smi'); ?></li>
                    <li><?php _e('Utilisez les algorithmes de disposition comme ForceAtlas2', 'rxg-smi'); ?></li>
                    <li><?php _e('Ajustez la taille des nœuds en fonction des attributs importés', 'rxg-smi'); ?></li>
                    <li><?php _e('Colorez les nœuds selon le cluster ou le type de contenu', 'rxg-smi'); ?></li>
                </ol>
            </div>
            
            <div class="rxg-smi-guide-section">
                <h3><?php _e('Interpréter les résultats', 'rxg-smi'); ?></h3>
                <div class="rxg-smi-interpretation-tips">
                    <div class="rxg-smi-tip">
                        <h4><?php _e('Pages orphelines', 'rxg-smi'); ?></h4>
                        <p><?php _e('Pages sans liens entrants, difficiles à découvrir pour utilisateurs et moteurs de recherche.', 'rxg-smi'); ?></p>
                        <div class="rxg-smi-tip-action"><?php _e('Action: Créer des liens vers ces pages depuis des contenus pertinents.', 'rxg-smi'); ?></div>
                    </div>
                    
                    <div class="rxg-smi-tip">
                        <h4><?php _e('Clusters isolés', 'rxg-smi'); ?></h4>
                        <p><?php _e('Groupes de pages liées entre elles mais déconnectées du reste du site.', 'rxg-smi'); ?></p>
                        <div class="rxg-smi-tip-action"><?php _e('Action: Créer des ponts entre clusters avec des liens stratégiques.', 'rxg-smi'); ?></div>
                    </div>
                    
                    <div class="rxg-smi-tip">
                        <h4><?php _e('Pages hub', 'rxg-smi'); ?></h4>
                        <p><?php _e('Pages avec beaucoup de liens entrants et sortants, centrales pour la navigation.', 'rxg-smi'); ?></p>
                        <div class="rxg-smi-tip-action"><?php _e('Action: Optimisez ces pages importantes, vérifiez qu\'elles ciblent des mots-clés pertinents.', 'rxg-smi'); ?></div>
                    </div>
                    
                    <div class="rxg-smi-tip">
                        <h4><?php _e('Pages autorité', 'rxg-smi'); ?></h4>
                        <p><?php _e('Pages avec beaucoup de liens entrants mais peu de liens sortants.', 'rxg-smi'); ?></p>
                        <div class="rxg-smi-tip-action"><?php _e('Action: Ajoutez des liens stratégiques sortants pour redistribuer le jus de lien.', 'rxg-smi'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
