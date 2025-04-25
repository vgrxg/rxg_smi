<?php
/**
 * Classe pour l'analyse détaillée du contenu
 */
class RXG_SMI_Content_Analyzer {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Analyse le contenu de toutes les pages
     */
    public function analyze_content() {
        global $wpdb;
        
        // Récupérer toutes les pages depuis la table
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $pages = $wpdb->get_results("SELECT id, post_id FROM $table_pages");
        
        foreach ($pages as $page) {
            // Récupérer le contenu complet
            $post = get_post($page->post_id);
            
            if (!$post) {
                continue;
            }
            
            // Analyser le contenu
            $word_count = $this->count_words($post->post_content);
            
            // Calculer le ratio mots/liens
            $outbound_links_count = $wpdb->get_var(
                $wpdb->prepare("
                    SELECT outbound_links_count 
                    FROM $table_pages 
                    WHERE id = %d
                ", $page->id)
            );
            
            // Mettre à jour les données dans la base
            $wpdb->update(
                $table_pages,
                array('word_count' => $word_count),
                array('id' => $page->id)
            );
            
            // Mettre à jour le ratio mots/liens
            $this->db->update_word_link_ratio($page->id, $word_count, $outbound_links_count);
        }
    }
    
    /**
     * Compte le nombre de mots dans un contenu
     */
    public function count_words($content) {
        // Supprimer les shortcodes
        $content = strip_shortcodes($content);
        
        // Supprimer les balises HTML
        $content = wp_strip_all_tags($content);
        
        // Supprimer les caractères spéciaux et espaces multiples
        $content = preg_replace('/[\r\n\t]+/', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Compter les mots
        $words = preg_split('/\s+/', $content);
        return count($words);
    }
    
    /**
     * Analyse la position des liens dans le contenu
     */
    public function analyze_link_positions($post_content, $page_id) {
        if (empty($post_content)) {
            return array();
        }
        
        // Utiliser DOMDocument pour analyser le contenu
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Désactiver les erreurs libxml
        @$dom->loadHTML(mb_convert_encoding($post_content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        
        $links = $dom->getElementsByTagName('a');
        $link_positions = array();
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            
            // Ignorer les liens vides ou les ancres
            if (empty($href) || substr($href, 0, 1) === '#') {
                continue;
            }
            
            // Déterminer la section et la position du lien
            $position = $this->determine_link_position($link, $dom);
            $section = $this->determine_link_section($link, $dom);
            $weight = $this->calculate_link_weight($position, $section);
            
            $link_positions[] = array(
                'href' => $href,
                'anchor_text' => $link->textContent,
                'position' => $position,
                'section' => $section,
                'weight' => $weight
            );
        }
        
        return $link_positions;
    }
    
    /**
     * Détermine la position d'un lien dans la page
     */
    private function determine_link_position($link, $dom) {
        $parent = $link->parentNode;
        
        // Vérifier si le lien est dans un titre
        $headings = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($headings as $heading) {
            if ($this->is_node_or_ancestor_tag($parent, $heading)) {
                return 'heading';
            }
        }
        
        // Vérifier si le lien est dans une liste
        if ($this->is_node_or_ancestor_tag($parent, 'li')) {
            return 'list';
        }
        
        // Vérifier si le lien est dans un tableau
        if ($this->is_node_or_ancestor_tag($parent, 'table')) {
            return 'table';
        }
        
        // Vérifier si le lien est dans un paragraphe
        if ($this->is_node_or_ancestor_tag($parent, 'p')) {
            return 'paragraph';
        }
        
        // Vérifier si le lien est une image
        if ($link->getElementsByTagName('img')->length > 0) {
            return 'image';
        }
        
        // Par défaut, considérer comme texte
        return 'text';
    }
    
    /**
     * Détermine la section de la page où se trouve le lien
     */
    private function determine_link_section($link, $dom) {
        $node = $link;
        
        // Remonter l'arbre DOM pour trouver les sections
        while ($node) {
            // Vérifier les attributs de classe et ID pour identifier les sections
            if ($node->hasAttributes()) {
                $class = $node->getAttribute('class');
                $id = $node->getAttribute('id');
                
                // Détecter le header
                if (
                    $node->nodeName === 'header' || 
                    stripos($class, 'header') !== false || 
                    stripos($id, 'header') !== false ||
                    stripos($class, 'menu') !== false || 
                    stripos($id, 'menu') !== false ||
                    stripos($class, 'nav') !== false || 
                    stripos($id, 'nav') !== false
                ) {
                    return 'header';
                }
                
                // Détecter le footer
                if (
                    $node->nodeName === 'footer' || 
                    stripos($class, 'footer') !== false || 
                    stripos($id, 'footer') !== false
                ) {
                    return 'footer';
                }
                
                // Détecter la sidebar
                if (
                    $node->nodeName === 'aside' || 
                    stripos($class, 'sidebar') !== false || 
                    stripos($id, 'sidebar') !== false ||
                    stripos($class, 'widget') !== false || 
                    stripos($id, 'widget') !== false
                ) {
                    return 'sidebar';
                }
                
                // Détecter le contenu principal
                if (
                    stripos($class, 'content') !== false || 
                    stripos($id, 'content') !== false ||
                    stripos($class, 'main') !== false || 
                    stripos($id, 'main') !== false ||
                    $node->nodeName === 'article' ||
                    $node->nodeName === 'main'
                ) {
                    return 'content';
                }
            }
            
            $node = $node->parentNode;
        }
        
        // Par défaut, considérer comme contenu
        return 'content';
    }
    
    /**
     * Vérifie si un nœud ou l'un de ses ancêtres est d'un tag spécifique
     */
    private function is_node_or_ancestor_tag($node, $tag) {
        while ($node) {
            if (strtolower($node->nodeName) === $tag) {
                return true;
            }
            $node = $node->parentNode;
        }
        return false;
    }
    
    /**
     * Calcule le poids d'un lien en fonction de sa position et section
     */
    private function calculate_link_weight($position, $section) {
        // Poids de base par section
        $section_weights = array(
            'content' => 1.0,
            'header' => 0.8,
            'sidebar' => 0.6,
            'footer' => 0.4
        );
        
        // Poids de base par position
        $position_weights = array(
            'heading' => 1.5,
            'paragraph' => 1.0,
            'list' => 0.9,
            'image' => 1.2,
            'table' => 0.8,
            'text' => 1.0
        );
        
        // Récupérer les poids
        $section_weight = isset($section_weights[$section]) ? $section_weights[$section] : 1.0;
        $position_weight = isset($position_weights[$position]) ? $position_weights[$position] : 1.0;
        
        // Calculer le poids final
        $weight = $section_weight * $position_weight;
        
        return round($weight, 2);
    }
    
    /**
     * Met à jour la position et le poids des liens pour une page
     */
    public function update_link_positions($page_id, $post_id) {
        global $wpdb;
        
        // Récupérer le contenu du post
        $post = get_post($post_id);
        
        if (!$post) {
            return;
        }
        
        // Analyser les positions des liens
        $link_positions = $this->analyze_link_positions($post->post_content, $page_id);
        
        if (empty($link_positions)) {
            return;
        }
        
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Mettre à jour chaque lien
        foreach ($link_positions as $link_data) {
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_links 
                 SET position = %s, section = %s, weight = %f 
                 WHERE source_id = %d AND target_url = %s",
                $link_data['position'],
                $link_data['section'],
                $link_data['weight'],
                $page_id,
                $link_data['href']
            ));
        }
    }
    
    /**
     * Évalue la répartition du contenu et des liens
     */
    public function evaluate_content_distribution($page_id) {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Récupérer les informations de base sur la page
        $page = $wpdb->get_row($wpdb->prepare("
            SELECT word_count, outbound_links_count, word_link_ratio 
            FROM $table_pages 
            WHERE id = %d
        ", $page_id));
        
        if (!$page) {
            return array();
        }
        
        // Récupérer la distribution des liens par section
        $sections = $wpdb->get_results($wpdb->prepare("
            SELECT section, COUNT(*) as count
            FROM $table_links
            WHERE source_id = %d
            GROUP BY section
        ", $page_id));
        
        $section_distribution = array();
        foreach ($sections as $section) {
            $section_distribution[$section->section] = $section->count;
        }
        
        // Récupérer la distribution des liens par position
        $positions = $wpdb->get_results($wpdb->prepare("
            SELECT position, COUNT(*) as count
            FROM $table_links
            WHERE source_id = %d
            GROUP BY position
        ", $page_id));
        
        $position_distribution = array();
        foreach ($positions as $position) {
            $position_distribution[$position->position] = $position->count;
        }
        
        // Évaluer le ratio mots/liens
        $ratio_evaluation = $this->evaluate_word_link_ratio($page->word_link_ratio);
        
        // Évaluer la distribution des liens
        $distribution_evaluation = $this->evaluate_link_distribution($section_distribution);
        
        return array(
            'word_count' => $page->word_count,
            'outbound_links_count' => $page->outbound_links_count,
            'word_link_ratio' => $page->word_link_ratio,
            'section_distribution' => $section_distribution,
            'position_distribution' => $position_distribution,
            'ratio_evaluation' => $ratio_evaluation,
            'distribution_evaluation' => $distribution_evaluation
        );
    }
    
    /**
     * Évalue le ratio mots/liens
     */
    private function evaluate_word_link_ratio($ratio) {
        if ($ratio === 0) {
            return array(
                'status' => 'warning',
                'message' => 'Aucun lien sortant détecté.'
            );
        }
        
        if ($ratio < 40) {
            return array(
                'status' => 'error',
                'message' => 'Trop de liens par rapport au contenu. Risque de sur-optimisation.'
            );
        }
        
        if ($ratio < 80) {
            return array(
                'status' => 'warning',
                'message' => 'Ratio mots/liens assez faible. Envisager d\'ajouter plus de contenu ou de réduire le nombre de liens.'
            );
        }
        
        if ($ratio > 300) {
            return array(
                'status' => 'warning',
                'message' => 'Peu de liens par rapport au contenu. Des opportunités de maillage interne sont probablement manquées.'
            );
        }
        
        return array(
            'status' => 'success',
            'message' => 'Bon équilibre entre contenu et liens.'
        );
    }
    
    /**
     * Évalue la distribution des liens dans les différentes sections
     */
    private function evaluate_link_distribution($section_distribution) {
        // Vérifier si des liens existent
        if (empty($section_distribution)) {
            return array(
                'status' => 'warning',
                'message' => 'Aucun lien détecté.'
            );
        }
        
        // Nombre total de liens
        $total_links = array_sum($section_distribution);
        
        // Vérifier si tous les liens sont dans le header/footer
        $navigation_links = isset($section_distribution['header']) ? $section_distribution['header'] : 0;
        $navigation_links += isset($section_distribution['footer']) ? $section_distribution['footer'] : 0;
        
        $content_links = isset($section_distribution['content']) ? $section_distribution['content'] : 0;
        
        if ($navigation_links > 0 && $content_links == 0) {
            return array(
                'status' => 'error',
                'message' => 'Tous les liens sont dans la navigation. Aucun lien contextuel dans le contenu.'
            );
        }
        
        // Calculer le pourcentage de liens dans le contenu
        $content_percentage = ($content_links / $total_links) * 100;
        
        if ($content_percentage < 30) {
            return array(
                'status' => 'warning',
                'message' => 'Faible proportion de liens dans le contenu principal. Envisagez d\'ajouter plus de liens contextuels.'
            );
        }
        
        if ($content_percentage > 80) {
            return array(
                'status' => 'success',
                'message' => 'Bonne proportion de liens dans le contenu principal.'
            );
        }
        
        return array(
            'status' => 'info',
            'message' => 'Distribution équilibrée des liens entre navigation et contenu.'
        );
    }
}
