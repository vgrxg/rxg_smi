/**
 * Scripts d'administration pour RXG Site Maillage Interne (Phase 2)
 */
(function($) {
    'use strict';

    /**
     * Toutes les fonctions JS liées à l'administration
     */
    var RXG_SMI_Admin = {

        /**
         * Initialisation
         */
        init: function() {
            this.bindEvents();
            this.initFilterActions();
            this.initTabs();
            this.initTooltips();
        },

        /**
         * Attacher les gestionnaires d'événements
         */
        bindEvents: function() {
            // Toggle des infobulles
            $('.rxg-smi-info-tooltip').on('click', this.toggleTooltip);
            
            // Confirmation avant analyse complète
            $('#rxg-smi-analyze-button').on('click', this.confirmAnalysis);
            
            // Sélection de page pour filtrer les liens
            $('#rxg-smi-page-filter').on('change', this.filterPageLinks);
            
            // Sélection de taxonomie pour filtrer les termes
            $('#rxg-smi-taxonomy-select').on('change', this.filterTaxonomyTerms);
            
            // Boutons de copie
            $('.rxg-smi-copy-button').on('click', this.copyToClipboard);
            
            // Sélecteurs d'ancre dans les opportunités
            $('.rxg-smi-anchor-selector').on('change', this.toggleCustomAnchor);
        },

        /**
         * Initialiser les actions de filtrage
         */
        initFilterActions: function() {
            // Filtrage des tableaux
            $('#rxg-smi-filter-post-type').on('change', function() {
                var url = new URL(window.location.href);
                url.searchParams.set('post_type', $(this).val());
                window.location.href = url.toString();
            });
            
            // Filtres de profondeur
            $('#rxg-smi-min-depth, #rxg-smi-max-depth').on('change', function() {
                var url = new URL(window.location.href);
                var minDepth = $('#rxg-smi-min-depth').val();
                var maxDepth = $('#rxg-smi-max-depth').val();
                
                if (minDepth !== '') {
                    url.searchParams.set('min_depth', minDepth);
                } else {
                    url.searchParams.delete('min_depth');
                }
                
                if (maxDepth !== '') {
                    url.searchParams.set('max_depth', maxDepth);
                } else {
                    url.searchParams.delete('max_depth');
                }
                
                window.location.href = url.toString();
            });
            
            // Filtres de mots
            $('#rxg-smi-min-words, #rxg-smi-max-words').on('change', function() {
                var url = new URL(window.location.href);
                var minWords = $('#rxg-smi-min-words').val();
                var maxWords = $('#rxg-smi-max-words').val();
                
                if (minWords !== '') {
                    url.searchParams.set('min_word_count', minWords);
                } else {
                    url.searchParams.delete('min_word_count');
                }
                
                if (maxWords !== '') {
                    url.searchParams.set('max_word_count', maxWords);
                } else {
                    url.searchParams.delete('max_word_count');
                }
                
                window.location.href = url.toString();
            });
            
            // Tri des tableaux
            $('.rxg-smi-sort-link').on('click', function(e) {
                e.preventDefault();
                var url = new URL(window.location.href);
                url.searchParams.set('orderby', $(this).data('orderby'));
                url.searchParams.set('order', $(this).data('order'));
                window.location.href = url.toString();
            });
            
            // Filtres de liens
            $('#rxg-smi-link-direction, #rxg-smi-link-section, #rxg-smi-link-position').on('change', function() {
                var url = new URL(window.location.href);
                var direction = $('#rxg-smi-link-direction').val();
                var section = $('#rxg-smi-link-section').val();
                var position = $('#rxg-smi-link-position').val();
                
                url.searchParams.set('direction', direction);
                
                if (section !== '') {
                    url.searchParams.set('section', section);
                } else {
                    url.searchParams.delete('section');
                }
                
                if (position !== '') {
                    url.searchParams.set('position', position);
                } else {
                    url.searchParams.delete('position');
                }
                
                window.location.href = url.toString();
            });
        },
        
        /**
         * Initialiser les onglets jQuery UI
         */
        initTabs: function() {
            $('.rxg-smi-tabs').each(function() {
                $(this).tabs();
            });
            
            // Gestionnaire pour les liens qui ciblent un onglet spécifique
            $('.rxg-smi-tab-link').on('click', function(e) {
                e.preventDefault();
                var tabId = $(this).attr('href');
                var $tabs = $(tabId).closest('.rxg-smi-tabs');
                var index = $tabs.find('.rxg-smi-tabs-nav a[href="' + tabId + '"]').parent().index();
                $tabs.tabs('option', 'active', index);
                
                // Scroll to the tabs
                $('html, body').animate({
                    scrollTop: $tabs.offset().top - 50
                }, 500);
            });
        },
        
        /**
         * Initialiser les infobulles jQuery UI
         */
        initTooltips: function() {
            $('.rxg-smi-tooltip').tooltip({
                position: {
                    my: 'center bottom-20',
                    at: 'center top',
                    using: function(position, feedback) {
                        $(this).css(position);
                        $('<div>')
                            .addClass('arrow')
                            .addClass(feedback.vertical)
                            .addClass(feedback.horizontal)
                            .appendTo(this);
                    }
                }
            });
        },

        /**
         * Afficher/Masquer une infobulle
         */
        toggleTooltip: function(e) {
            e.preventDefault();
            $(this).next('.rxg-smi-tooltip-content').toggleClass('visible');
        },

        /**
         * Confirmation avant l'analyse complète
         */
        confirmAnalysis: function(e) {
            if (!confirm(rxg_smi.i18n.confirm_analysis)) {
                e.preventDefault();
            }
        },

        /**
         * Filtrer les liens d'une page
         */
        filterPageLinks: function() {
            var pageId = $(this).val();
            var direction = $('#rxg-smi-link-direction').val() || 'outbound';
            
            if (pageId) {
                window.location.href = rxg_smi.admin_url + '?page=rxg-smi-links&page_id=' + pageId + '&direction=' + direction;
            }
        },
        
        /**
         * Filtrer les termes d'une taxonomie
         */
        filterTaxonomyTerms: function() {
            var taxonomy = $(this).val();
            
            if (taxonomy) {
                window.location.href = rxg_smi.admin_url + '?page=rxg-smi-taxonomies&taxonomy=' + encodeURIComponent(taxonomy);
            }
        },
        
        /**
         * Copier dans le presse-papier
         */
        copyToClipboard: function() {
            var $this = $(this);
            var textToCopy = $this.data('copy-text');
            
            // Créer un élément temporaire pour la copie
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(textToCopy).select();
            
            // Exécuter la commande de copie
            var success = document.execCommand('copy');
            $temp.remove();
            
            // Feedback visuel
            if (success) {
                var originalText = $this.text();
                $this.text(rxg_smi.i18n.copied);
                
                setTimeout(function() {
                    $this.text(originalText);
                }, 2000);
            }
        },
        
        /**
         * Basculer l'entrée personnalisée pour les ancres
         */
        toggleCustomAnchor: function() {
            var $this = $(this);
            var $customInput = $this.siblings('.rxg-smi-custom-anchor');
            
            if ($this.val() === 'custom') {
                $customInput.show().focus();
            } else {
                $customInput.hide();
            }
        },
        
        /**
         * Charger les termes d'une taxonomie via AJAX
         */
        loadTaxonomyTerms: function(taxonomy, callback) {
            $.ajax({
                url: rxg_smi.ajax_url,
                type: 'POST',
                data: {
                    action: 'rxg_smi_get_terms',
                    taxonomy: taxonomy,
                    nonce: rxg_smi.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data.terms);
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des termes');
                }
            });
        },
        
        /**
         * Charger les suggestions d'ancres pour une page
         */
        loadAnchorSuggestions: function(pageId, callback) {
            $.ajax({
                url: rxg_smi.ajax_url,
                type: 'POST',
                data: {
                    action: 'rxg_smi_get_anchor_suggestions',
                    page_id: pageId,
                    nonce: rxg_smi.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data.suggestions);
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des suggestions d\'ancres');
                }
            });
        },
        
        /**
         * Charger les liens potentiels pour une page
         */
        loadPotentialLinks: function(pageId, callback) {
            $.ajax({
                url: rxg_smi.ajax_url,
                type: 'POST',
                data: {
                    action: 'rxg_smi_get_potential_links',
                    page_id: pageId,
                    nonce: rxg_smi.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data.links);
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des liens potentiels');
                }
            });
        },
        
        /**
         * Charger les statistiques d'ancre pour une page
         */
        loadPageAnchors: function(pageId, callback) {
            $.ajax({
                url: rxg_smi.ajax_url,
                type: 'POST',
                data: {
                    action: 'rxg_smi_get_page_anchors',
                    page_id: pageId,
                    nonce: rxg_smi.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data);
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des statistiques d\'ancre');
                }
            });
        },
        
        /**
         * Vérifier l'utilisation d'une ancre dans le site
         */
        checkAnchorUsage: function(anchor, callback) {
            $.ajax({
                url: rxg_smi.ajax_url,
                type: 'POST',
                data: {
                    action: 'rxg_smi_check_anchor_usage',
                    anchor: anchor,
                    nonce: rxg_smi.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data);
                    }
                },
                error: function() {
                    console.error('Erreur lors de la vérification de l\'utilisation de l\'ancre');
                }
            });
        }
    };

    // Initialiser quand la page est prête
    $(document).ready(function() {
        RXG_SMI_Admin.init();
    });

})(jQuery);
