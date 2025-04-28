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
                <button id="rxg-smi-generate-viz" class="button button-primary" style="margin-top:15px;">
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
</div>

<!-- Script de visualisation intégré directement -->
<script src="https://d3js.org/d3.v7.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Masquer le chargement au départ
    $('#rxg-smi-loading').hide();
    
    // Navigation par onglets
    $('.rxg-smi-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Activer l'onglet
        $('.rxg-smi-tabs .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Afficher le contenu
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });
    
    // Variables principales
    var graph;
    var simulation;
    var svg;
    var width = 800;
    var height = 600;
    var nodes = [];
    var links = [];
    
    // Gestionnaire du bouton "Générer la visualisation"
    $('#rxg-smi-generate-viz').on('click', function() {
        $(this).prop('disabled', true);
        $(this).text('Chargement en cours...');
        $('#rxg-smi-loading').show();
        
        // Initialiser la visualisation
        initVisualization();
    });
    
    function initVisualization() {
        // Créer l'élément SVG
        width = $('#rxg-smi-graph').width();
        height = 600;
        
        $('#rxg-smi-graph').html(''); // Nettoyer l'espace
        
        svg = d3.select('#rxg-smi-graph')
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .attr('viewBox', [0, 0, width, height]);
        
        // Ajouter les définitions pour les marqueurs de flèche
        svg.append('defs').append('marker')
            .attr('id', 'arrowhead')
            .attr('viewBox', '0 -5 10 10')
            .attr('refX', 20)
            .attr('refY', 0)
            .attr('orient', 'auto')
            .attr('markerWidth', 6)
            .attr('markerHeight', 6)
            .append('path')
            .attr('d', 'M0,-5L10,0L0,5')
            .attr('fill', '#999');
        
        // Créer un groupe pour le zoom
        var g = svg.append('g');
        
        // Ajouter le comportement de zoom
        var zoom = d3.zoom()
            .scaleExtent([0.1, 8])
            .on('zoom', function(event) {
                g.attr('transform', event.transform);
            });
        
        svg.call(zoom);
        
        // Charger les données
        loadData(g);
    }
    
    // Chargement des données
    function loadData(g) {
        $('#rxg-smi-loading').show();
        console.log('Chargement des données via AJAX...');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'rxg_smi_get_visualization_data',
                nonce: '<?php echo wp_create_nonce('rxg-smi-visualization'); ?>'
            },
            success: function(response) {
                console.log('Réponse reçue:', response);
                
                if (response.success) {
                    $('#rxg-smi-loading').hide();
                    
                    graph = response.data;
                    console.log('Structure de graph:', graph);
                    
                    if (!graph || !graph.pages) {
                        $('#rxg-smi-graph').html('<p class="error">Données incomplètes ou malformées: pages manquantes</p>');
                        $('#rxg-smi-generate-viz').prop('disabled', false).text('Réessayer');
                        return;
                    }
                    
                    // Vérifier la structure de graph.links
                    if (!graph.links || typeof graph.links !== 'object') {
                        $('#rxg-smi-graph').html('<p class="error">Données incomplètes ou malformées: liens manquants</p>');
                        $('#rxg-smi-generate-viz').prop('disabled', false).text('Réessayer');
                        return;
                    }
                    
                    // Vérifier si graph.links est un tableau
                    if (!Array.isArray(graph.links)) {
                        console.log('Links n\'est pas un tableau, conversion nécessaire');
                        // Convertir l'objet en tableau si nécessaire
                        var linksArray = [];
                        for (var key in graph.links) {
                            if (graph.links.hasOwnProperty(key)) {
                                linksArray.push(graph.links[key]);
                            }
                        }
                        graph.links = linksArray;
                    }
                    
                    nodes = graph.pages.map(function(d) {
                        return Object.create(d);
                    });
                    
                    links = graph.links.map(function(d) {
                        return Object.create(d);
                    });
                    
                    console.log('Nodes: ', nodes.length);
                    console.log('Links: ', links.length);
                    
                    // Initialiser les filtres
                    initFilters();
                    
                    // Créer la visualisation
                    createVisualization(g);
                } else {
                    console.error('Erreur dans la réponse:', response);
                    $('#rxg-smi-loading').hide();
                    $('#rxg-smi-graph').html('<p class="error">' + (response.data ? response.data.message : 'Erreur de chargement') + '</p>');
                    $('#rxg-smi-generate-viz').prop('disabled', false).text('Réessayer');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', status, error);
                console.log('Détails de l\'erreur:', xhr.responseText);
                $('#rxg-smi-loading').hide();
                $('#rxg-smi-graph').html('<p class="error">Erreur lors du chargement des données</p>');
                $('#rxg-smi-generate-viz').prop('disabled', false).text('Réessayer');
            }
        });
    }
    
    // Initialiser les filtres avec les valeurs disponibles
    function initFilters() {
        if (!nodes || nodes.length === 0) return;
        
        // Récupérer tous les clusters uniques
        var clusters = [];
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].cluster && clusters.indexOf(nodes[i].cluster) === -1) {
                clusters.push(nodes[i].cluster);
            }
        }
        clusters.sort();
        
        // Remplir le filtre de clusters
        var $clusterFilter = $('#rxg-smi-filter-cluster');
        $clusterFilter.find('option:not(:first)').remove();
        
        for (var j = 0; j < clusters.length; j++) {
            $clusterFilter.append($('<option></option>').val(clusters[j]).text(clusters[j]));
        }
        
        // Créer la légende des clusters
        var $legend = $('#rxg-smi-cluster-legend');
        $legend.empty();
        
        var colorScale = d3.scaleOrdinal(d3.schemeCategory10);
        
        for (var k = 0; k < clusters.length; k++) {
            var color = colorScale(k);
            
            $legend.append(
                $('<div class="rxg-smi-legend-item"></div>')
                    .append($('<div class="rxg-smi-legend-symbol"></div>').css('background-color', color))
                    .append($('<div class="rxg-smi-legend-label"></div>').text(clusters[k]))
            );
        }
    }
    
    // Créer la visualisation D3
    function createVisualization(g) {
        var colorScale = d3.scaleOrdinal(d3.schemeCategory10);
        var uniqueClusters = [];
        
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].cluster && uniqueClusters.indexOf(nodes[i].cluster) === -1) {
                uniqueClusters.push(nodes[i].cluster);
            }
        }
        
        // Créer la simulation de force
        simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(function(d) { return d.id; }))
            .force('charge', d3.forceManyBody().strength(-200))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collide', d3.forceCollide().radius(function(d) { 
                return getSizeByAttribute(d) + 10; 
            }));
        
        // Créer les liens
        var link = g.append('g')
            .selectAll('line')
            .data(links)
            .join('line')
            .attr('stroke', '#999')
            .attr('stroke-opacity', 0.6)
            .attr('stroke-width', function(d) { return d.weight * 0.5; })
            .attr('marker-end', 'url(#arrowhead)');
        
        // Créer les nœuds
        var node = g.append('g')
            .selectAll('circle')
            .data(nodes)
            .join('circle')
            .attr('r', getSizeByAttribute)
            .attr('fill', function(d, i) { 
                return colorScale(uniqueClusters.indexOf(d.cluster)); 
            })
            .attr('stroke', '#fff')
            .attr('stroke-width', 1.5)
            .on('click', function(event, d) {
                showPageDetails(event, d);
            })
            .call(drag(simulation));
        
        // Ajouter des titres (tooltips)
        node.append('title')
            .text(function(d) { return d.title; });
        
        // Ajouter les étiquettes de texte
        var label = g.append('g')
            .selectAll('text')
            .data(nodes)
            .join('text')
            .attr('dx', 12)
            .attr('dy', '.35em')
            .text(function(d) { return d.title; })
            .style('font-size', '8px')
            .style('opacity', 0.7);
        
        // Mise à jour de la simulation
        simulation.on('tick', function() {
            link
                .attr('x1', function(d) { return d.source.x; })
                .attr('y1', function(d) { return d.source.y; })
                .attr('x2', function(d) { return d.target.x; })
                .attr('y2', function(d) { return d.target.y; });
            
            node
                .attr('cx', function(d) { return d.x; })
                .attr('cy', function(d) { return d.y; });
            
            label
                .attr('x', function(d) { return d.x; })
                .attr('y', function(d) { return d.y; });
        });
    }
    
    // Obtenir la taille d'un nœud en fonction de l'attribut sélectionné
    function getSizeByAttribute(d) {
        var attribute = $('#rxg-smi-node-size').val();
        var value = 0;
        
        switch (attribute) {
            case 'inbound_links_count':
                value = d.inbound_links_count || 0;
                break;
            case 'juice_score':
                value = d.juice_score || 0;
                break;
            case 'word_count':
                value = (d.word_count || 0) / 100; // Diviser par 100 pour rendre les tailles plus raisonnables
                break;
            default:
                value = d.inbound_links_count || 0;
        }
        
        // Taille minimum 3, maximum 20
        return Math.max(3, Math.min(20, Math.sqrt(value) + 3));
    }
    
    // Mettre à jour la visualisation en fonction des filtres
    function updateVisualization() {
        if (!graph || !simulation) return;
        
        var clusterFilter = $('#rxg-smi-filter-cluster').val();
        var depthFilter = $('#rxg-smi-filter-depth').val();
        
        // Filtrer les nœuds
        var filteredNodes = graph.pages.filter(function(node) {
            // Filtre de cluster
            var matchesCluster = clusterFilter === 'all' || node.cluster === clusterFilter;
            
            // Filtre de profondeur
            var matchesDepth = depthFilter === 'all' || 
                               (depthFilter === '5' && node.depth >= 5) || 
                               node.depth.toString() === depthFilter;
            
            return matchesCluster && matchesDepth;
        });
        
        // Filtrer les liens
        var filteredNodeIds = filteredNodes.map(function(node) {
            return node.id;
        });
        
        var filteredLinks = graph.links.filter(function(link) {
            return filteredNodeIds.includes(link.source) && 
                   filteredNodeIds.includes(link.target);
        });
        
        // Mise à jour des données
        nodes = filteredNodes.map(function(d) {
            return Object.create(d);
        });
        
        links = filteredLinks.map(function(d) {
            return Object.create(d);
        });
        
        // Recréer la visualisation
        d3.select('#rxg-smi-graph svg g').remove();
        var g = d3.select('#rxg-smi-graph svg').append('g');
        
        createVisualization(g);
    }
    
    // Afficher les détails d'une page
    function showPageDetails(event, d) {
        var $details = $('#rxg-smi-page-details');
        
        var html = '<h4>' + d.title + '</h4>';
        html += '<p><strong>URL:</strong> <a href="' + d.url + '" target="_blank">' + d.url + '</a></p>';
        html += '<p><strong>Type:</strong> ' + d.type + '</p>';
        html += '<p><strong>Cluster:</strong> ' + d.cluster + '</p>';
        html += '<p><strong>Profondeur:</strong> ' + d.depth + '</p>';
        html += '<p><strong>Mots:</strong> ' + d.word_count + '</p>';
        html += '<p><strong>Liens entrants:</strong> ' + d.inbound_links_count + '</p>';
        html += '<p><strong>Liens sortants:</strong> ' + d.outbound_links_count + '</p>';
        html += '<p><strong>Score de jus:</strong> ' + (parseFloat(d.juice_score) || 0).toFixed(2) + '</p>';
        
        if (d.taxonomies && d.taxonomies.length > 0) {
            html += '<p><strong>Taxonomies:</strong> ' + d.taxonomies.join(', ') + '</p>';
        }
        
        html += '<p><a href="<?php echo admin_url('admin.php'); ?>?page=rxg-smi-links&page_id=' + d.id + '" class="button button-small">Voir les liens</a> ';
        html += '<a href="<?php echo admin_url('admin.php'); ?>?page=rxg-smi-opportunities&page_id=' + d.id + '" class="button button-small">Voir opportunités</a></p>';
        
        $details.html(html);
    }
    
    // Fonction pour le comportement de glisser-déposer
    function drag(simulation) {
        function dragstarted(event) {
            if (!event.active) simulation.alphaTarget(0.3).restart();
            event.subject.fx = event.subject.x;
            event.subject.fy = event.subject.y;
        }
        
        function dragged(event) {
            event.subject.fx = event.x;
            event.subject.fy = event.y;
        }
        
        function dragended(event) {
            if (!event.active) simulation.alphaTarget(0);
            event.subject.fx = null;
            event.subject.fy = null;
        }
        
        return d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended);
    }
    
    // Attacher les événements aux filtres
    $('#rxg-smi-filter-cluster, #rxg-smi-filter-depth, #rxg-smi-node-size').on('change', updateVisualization);
});
</script>
<style>
.error {
    color: #dc3232;
    background: #fbeaea;
    padding: 10px;
    border-radius: 3px;
    margin: 10px 0;
}
</style>