/**
 * Script D3.js pour la visualisation du maillage interne
 */
(function($) {
    'use strict';
    
    // Variables principales
    var graph;
    var simulation;
    var svg;
    var width = 800;
    var height = 600;
    var nodes = [];
    var links = [];
    
    // Initialisation
    $(document).ready(function() {
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
        
        // Si on est sur l'onglet de visualisation, initialiser D3
        if ($('#tab-visualization').is(':visible')) {
            initVisualization();
        }
        
        // Si on change d'onglet vers la visualisation, initialiser D3 si pas déjà fait
        $('.rxg-smi-tabs .nav-tab[href="#tab-visualization"]').on('click', function() {
            if (!svg) {
                initVisualization();
            }
        });
        
        // Filtres
        $('#rxg-smi-filter-cluster').on('change', updateVisualization);
        $('#rxg-smi-filter-depth').on('change', updateVisualization);
        $('#rxg-smi-node-size').on('change', updateVisualization);
    });

    // Initialisation D3
    function initVisualization() {
    $('#rxg-smi-loading').hide();
    $('#rxg-smi-graph').html(
        '<div class="rxg-smi-no-data">' +
        '<p>Aucune donnée chargée. Veuillez d\'abord analyser votre site pour générer des données.</p>' +
        '<button id="rxg-smi-generate-viz" class="button button-primary">Générer la visualisation</button>' +
        '</div>'
    );
    
    // Ajouter un gestionnaire d'événement au bouton
    $('#rxg-smi-generate-viz').on('click', function() {
        $(this).prop('disabled', true).text('Chargement...');
        $('#rxg-smi-loading').show();
        
        // Créer l'élément SVG et charger les données
        width = $('#rxg-smi-graph').width();
        height = 600;
        
        svg = d3.select('#rxg-smi-graph')
            .html('') // Nettoyer le contenu actuel
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
        loadData(g);
        
        // Ajouter le comportement de zoom
        var zoom = d3.zoom()
            .scaleExtent([0.1, 8])
            .on('zoom', (event) => {
                g.attr('transform', event.transform);
            });
        
        svg.call(zoom);
        
        // Charger les données
        loadData(g);
    }
    
    // Chargement des données
    function loadData(g) {
        $('#rxg-smi-loading').show();
        
        $.ajax({
            url: rxgSmiData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rxg_smi_get_visualization_data',
                nonce: rxgSmiData.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#rxg-smi-loading').hide();
                    
                    graph = response.data;
                    nodes = graph.pages.map(d => Object.create(d));
                    links = graph.links.map(d => Object.create(d));
                    
                    // Initialiser les filtres
                    initFilters();
                    
                    // Créer la visualisation
                    createVisualization(g);
                } else {
                    $('#rxg-smi-loading').html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('#rxg-smi-loading').html('<p class="error">' + rxgSmiData.i18n.error + '</p>');
            }
        });
    }
    
    // Initialiser les filtres avec les valeurs disponibles
    function initFilters() {
        // Récupérer tous les clusters uniques
        var clusters = [...new Set(nodes.map(node => node.cluster))].sort();
        
        // Remplir le filtre de clusters
        var $clusterFilter = $('#rxg-smi-filter-cluster');
        $clusterFilter.find('option:not(:first)').remove();
        
        clusters.forEach(function(cluster) {
            $clusterFilter.append($('<option></option>').val(cluster).text(cluster));
        });
        
        // Créer la légende des clusters
        var $legend = $('#rxg-smi-cluster-legend');
        $legend.empty();
        
        var colorScale = d3.scaleOrdinal(d3.schemeCategory10);
        
        clusters.forEach(function(cluster, i) {
            var color = colorScale(i);
            
            $legend.append(
                $('<div class="rxg-smi-legend-item"></div>')
                    .append($('<div class="rxg-smi-legend-symbol"></div>').css('background-color', color))
                    .append($('<div class="rxg-smi-legend-label"></div>').text(cluster))
            );
        });
    }
    
    // Créer la visualisation D3
    function createVisualization(g) {
        var colorScale = d3.scaleOrdinal(d3.schemeCategory10);
        var uniqueClusters = [...new Set(nodes.map(node => node.cluster))];
        
        // Créer la simulation de force
        simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(d => d.id))
            .force('charge', d3.forceManyBody().strength(-200))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collide', d3.forceCollide().radius(d => getSizeByAttribute(d) + 10));
        
        // Créer les liens
        var link = g.append('g')
            .selectAll('line')
            .data(links)
            .join('line')
            .attr('stroke', '#999')
            .attr('stroke-opacity', 0.6)
            .attr('stroke-width', d => d.weight * 0.5)
            .attr('marker-end', 'url(#arrowhead)');
        
        // Créer les nœuds
        var node = g.append('g')
            .selectAll('circle')
            .data(nodes)
            .join('circle')
            .attr('r', getSizeByAttribute)
            .attr('fill', (d, i) => colorScale(uniqueClusters.indexOf(d.cluster)))
            .attr('stroke', '#fff')
            .attr('stroke-width', 1.5)
            .on('click', showPageDetails)
            .call(drag(simulation));
        
        // Ajouter des titres (tooltips)
        node.append('title')
            .text(d => d.title);
        
        // Ajouter les étiquettes de texte
        var label = g.append('g')
            .selectAll('text')
            .data(nodes)
            .join('text')
            .attr('dx', 12)
            .attr('dy', '.35em')
            .text(d => d.title)
            .style('font-size', '8px')
            .style('opacity', 0.7);
        
        // Mise à jour de la simulation
        simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);
            
            node
                .attr('cx', d => d.x)
                .attr('cy', d => d.y);
            
            label
                .attr('x', d => d.x)
                .attr('y', d => d.y);
        });
    }
    
    // Obtenir la taille d'un nœud en fonction de l'attribut sélectionné
    function getSizeByAttribute(d) {
        var attribute = $('#rxg-smi-node-size').val();
        var value = 0;
        
        switch (attribute) {
            case 'inbound_links_count':
                value = d.inbound_links_count;
                break;
            case 'juice_score':
                value = d.juice_score;
                break;
            case 'word_count':
                value = d.word_count / 100; // Diviser par 100 pour rendre les tailles plus raisonnables
                break;
            default:
                value = d.inbound_links_count;
        }
        
        // Taille minimum 3, maximum 20
        return Math.max(3, Math.min(20, Math.sqrt(value) + 3));
    }
    
    // Mette à jour la visualisation en fonction des filtres
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
        var filteredNodeIds = filteredNodes.map(node => node.id);
        var filteredLinks = graph.links.filter(function(link) {
            return filteredNodeIds.includes(link.source) && 
                   filteredNodeIds.includes(link.target);
        });
        
        // Mise à jour des données
        nodes = filteredNodes.map(d => Object.create(d));
        links = filteredLinks.map(d => Object.create(d));
        
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
        html += '<p><strong>Score de jus:</strong> ' + d.juice_score.toFixed(2) + '</p>';
        
        if (d.taxonomies && d.taxonomies.length > 0) {
            html += '<p><strong>Taxonomies:</strong> ' + d.taxonomies.join(', ') + '</p>';
        }
        
        html += '<p><a href="' + rxgSmiData.adminUrl + '?page=rxg-smi-links&page_id=' + d.id + '" class="button button-small">Voir les liens</a> ';
        html += '<a href="' + rxgSmiData.adminUrl + '?page=rxg-smi-opportunities&page_id=' + d.id + '" class="button button-small">Voir opportunités</a></p>';
        
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
    
})(jQuery);
