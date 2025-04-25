<?php
/**
 * Classe pour l'analyse des textes d'ancre
 */
class RXG_SMI_Anchor_Analyzer {
    
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
     * Analyse les textes d'ancre pour toutes les pages indexées
     */
    public function analyze_anchors() {
        global $wpdb;
        
        // Récupérer toutes les pages depuis la table
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        $pages = $wpdb->get_results("SELECT id FROM $table_pages");
        
        foreach ($pages as $page) {
            // Récupérer tous les liens entrants vers cette page
            $links = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT source_id, anchor_text 
                    FROM $table_links 
                    WHERE target_id = %d AND anchor_text != ''
                ", $page->id)
            );
            
            // Analyser les ancres
            foreach ($links as $link) {
                $this->db->save_anchor_stats($page->id, $link->anchor_text);
            }
            
            // Calculer et mettre à jour le score de diversité des ancres
            $this->db->update_anchor_diversity_score($page->id);
        }
    }
    
    /**
     * Analyse un texte d'ancre spécifique
     */
    public function analyze_anchor_text($anchor_text) {
        // Ignorer les ancres vides
        if (empty($anchor_text)) {
            return array(
                'length' => 0,
                'word_count' => 0,
                'keywords' => array(),
                'is_keyword_rich' => false,
                'is_branded' => false,
                'has_stopwords' => false,
                'suggestions' => array()
            );
        }
        
        // Statistiques de base
        $length = mb_strlen($anchor_text);
        $words = preg_split('/\s+/', trim($anchor_text));
        $word_count = count($words);
        
        // Liste de mots vides fréquents en français
        $stopwords = array(
            'le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'de', 'du', 'au', 'aux',
            'ce', 'cette', 'ces', 'mon', 'ton', 'son', 'notre', 'votre', 'leur',
            'en', 'pour', 'par', 'sur', 'dans', 'avec', 'sans', 'chez'
        );
        
        // Vérifier la présence de mots vides
        $has_stopwords = false;
        foreach ($words as $word) {
            if (in_array(strtolower($word), $stopwords)) {
                $has_stopwords = true;
                break;
            }
        }
        
        // Extraire les mots clés potentiels (mots de 4 lettres ou plus)
        $keywords = array();
        foreach ($words as $word) {
            if (mb_strlen($word) >= 4 && !in_array(strtolower($word), $stopwords)) {
                $keywords[] = $word;
            }
        }
        
        // Vérifier si l'ancre est riche en mots-clés (au moins 50% de mots-clés)
        $is_keyword_rich = !empty($keywords) && (count($keywords) / $word_count) >= 0.5;
        
        // TODO: Vérifier si l'ancre contient une marque (nécessiterait une liste de marques)
        $is_branded = false;
        
        // Générer des suggestions d'amélioration
        $suggestions = array();
        
        if ($word_count == 1 && $length < 4) {
            $suggestions[] = 'L\'ancre est trop courte et peu descriptive.';
        }
        
        if ($has_stopwords && $word_count <= 2) {
            $suggestions[] = 'L\'ancre contient principalement des mots vides.';
        }
        
        if (!$is_keyword_rich && $word_count > 2) {
            $suggestions[] = 'L\'ancre pourrait inclure plus de mots-clés pertinents.';
        }
        
        if ($length > 60) {
            $suggestions[] = 'L\'ancre est peut-être trop longue, envisagez de la raccourcir.';
        }
        
        return array(
            'length' => $length,
            'word_count' => $word_count,
            'keywords' => $keywords,
            'is_keyword_rich' => $is_keyword_rich,
            'is_branded' => $is_branded,
            'has_stopwords' => $has_stopwords,
            'suggestions' => $suggestions
        );
    }
    
    /**
     * Génère des variantes d'ancres suggérées pour une page
     */
    public function generate_anchor_suggestions($page_id, $max_suggestions = 5) {
        global $wpdb;
        
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        
        // Récupérer les informations sur la page
        $page = $wpdb->get_row($wpdb->prepare("
            SELECT title, meta_description, h1 
            FROM $table_pages 
            WHERE id = %d
        ", $page_id));
        
        if (!$page) {
            return array();
        }
        
        $suggestions = array();
        
        // 1. Utiliser le titre comme suggestion d'ancre
        if (!empty($page->title)) {
            $suggestions[] = array(
                'text' => $page->title,
                'source' => 'Titre de la page',
                'score' => 90
            );
            
            // Variante : première partie du titre (si contient un séparateur)
            $title_parts = preg_split('/[-–|:]/u', $page->title, 2);
            if (count($title_parts) > 1 && mb_strlen(trim($title_parts[0])) > 5) {
                $suggestions[] = array(
                    'text' => trim($title_parts[0]),
                    'source' => 'Première partie du titre',
                    'score' => 85
                );
            }
        }
        
        // 2. Utiliser le H1 comme suggestion si différent du titre
        if (!empty($page->h1) && $page->h1 != $page->title) {
            $suggestions[] = array(
                'text' => $page->h1,
                'source' => 'Titre H1',
                'score' => 80
            );
        }
        
        // 3. Utiliser des termes de taxonomie comme suggestions
        $terms = $wpdb->get_results($wpdb->prepare("
            SELECT taxonomy, name 
            FROM $table_terms 
            WHERE page_id = %d 
            ORDER BY taxonomy
        ", $page_id));
        
        if (!empty($terms)) {
            $used_terms = array();
            foreach ($terms as $term) {
                // Éviter les doublons
                if (in_array($term->name, $used_terms)) {
                    continue;
                }
                
                $used_terms[] = $term->name;
                
                // N'ajouter que si le nom du terme est assez long
                if (mb_strlen($term->name) >= 4) {
                    $suggestions[] = array(
                        'text' => $term->name,
                        'source' => 'Terme de taxonomie: ' . $term->taxonomy,
                        'score' => 75
                    );
                }
                
                // Si le titre contient ce terme, suggérer une variante autour
                if (!empty($page->title) && stripos($page->title, $term->name) !== false) {
                    $context = $this->extract_keyword_context($page->title, $term->name);
                    if ($context != $term->name && $context != $page->title && mb_strlen($context) > 5) {
                        $suggestions[] = array(
                            'text' => $context,
                            'source' => 'Terme en contexte dans le titre',
                            'score' => 70
                        );
                    }
                }
            }
        }
        
        // 4. Extraire des phrases de la méta-description
        if (!empty($page->meta_description) && mb_strlen($page->meta_description) > 20) {
            $sentences = preg_split('/[.!?]+/', $page->meta_description);
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                
                // Ne prendre que des phrases assez courtes
                if (mb_strlen($sentence) >= 10 && mb_strlen($sentence) <= 60) {
                    $suggestions[] = array(
                        'text' => $sentence,
                        'source' => 'Phrase de la méta-description',
                        'score' => 65
                    );
                }
                
                // Limiter le nombre de suggestions
                if (count($suggestions) >= $max_suggestions) {
                    break;
                }
            }
        }
        
        // Trier par score et limiter le nombre
        usort($suggestions, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return array_slice($suggestions, 0, $max_suggestions);
    }
    
    /**
     * Extrait le contexte autour d'un mot-clé dans un texte
     */
    private function extract_keyword_context($text, $keyword, $context_length = 5) {
        $pattern = '/(\w+\s+){0,' . $context_length . '}' . preg_quote($keyword, '/') . '(\s+\w+){0,' . $context_length . '}/ui';
        
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[0]);
        }
        
        return $keyword;
    }
    
    /**
     * Vérifie si une page a des ancres trop similaires
     */
    public function has_similar_anchors($page_id, $similarity_threshold = 80) {
        $anchors = $this->db->get_anchor_stats($page_id);
        
        if (count($anchors) <= 1) {
            return false;
        }
        
        // Classer les ancres par fréquence
        $anchor_texts = array();
        foreach ($anchors as $anchor) {
            $anchor_texts[] = $anchor->anchor_text;
        }
        
        // Vérifier la similarité entre les ancres
        $similar_pairs = array();
        
        for ($i = 0; $i < count($anchor_texts); $i++) {
            for ($j = $i + 1; $j < count($anchor_texts); $j++) {
                $similarity = $this->calculate_text_similarity($anchor_texts[$i], $anchor_texts[$j]);
                
                if ($similarity >= $similarity_threshold) {
                    $similar_pairs[] = array(
                        'anchor1' => $anchor_texts[$i],
                        'anchor2' => $anchor_texts[$j],
                        'similarity' => $similarity
                    );
                }
            }
        }
        
        return !empty($similar_pairs) ? $similar_pairs : false;
    }
    
/*
 * Calcule la similarité entre deux textes (algorithme de Levenshtein simple)
 */
public function calculate_text_similarity($text1, $text2) {
    $text1 = strtolower(trim($text1));
    $text2 = strtolower(trim($text2));
    
    // Si l'un des textes est vide, la similarité est 0
    if (empty($text1) || empty($text2)) {
        return 0;
    }
    
    // Si les textes sont identiques, la similarité est 100%
    if ($text1 === $text2) {
        return 100;
    }
    
    // Calculer la distance de Levenshtein
    $lev_distance = levenshtein($text1, $text2);
    
    // Calculer la longueur maximale des deux textes
    $max_length = max(mb_strlen($text1), mb_strlen($text2));
    
    // Calculer la similarité en pourcentage
    $similarity = (1 - ($lev_distance / $max_length)) * 100;
    
    return round($similarity);
}
    
    /**
     * Récupère les statistiques détaillées d'ancre pour une page
     */
    public function get_anchor_stats_details($page_id) {
        $stats = $this->db->get_anchor_stats($page_id);
        $total_links = 0;
        $anchor_details = array();
        
        foreach ($stats as $stat) {
            $total_links += $stat->count;
            
            $analysis = $this->analyze_anchor_text($stat->anchor_text);
            
            $anchor_details[] = array(
                'text' => $stat->anchor_text,
                'count' => $stat->count,
                'analysis' => $analysis
            );
        }
        
        // Calculer les pourcentages d'utilisation
        foreach ($anchor_details as &$detail) {
            $detail['percentage'] = ($total_links > 0) ? round(($detail['count'] / $total_links) * 100, 1) : 0;
            $detail['overused'] = $detail['percentage'] > 30;
        }
        
        return array(
            'total_links' => $total_links,
            'unique_anchors' => count($stats),
            'diversity_score' => $this->calculate_diversity_score($stats, $total_links),
            'details' => $anchor_details
        );
    }
    
    /**
     * Calcule un score de diversité basé sur les statistiques d'ancre
     */
    private function calculate_diversity_score($stats, $total_links) {
        if ($total_links === 0 || empty($stats)) {
            return 0;
        }
        
        $unique_count = count($stats);
        
        // Calculer la distribution des ancres
        $distributions = array();
        foreach ($stats as $stat) {
            $distributions[] = $stat->count / $total_links;
        }
        
        // Calculer l'indice de diversité (Inverse de l'indice de concentration)
        $concentration = 0;
        foreach ($distributions as $distribution) {
            $concentration += pow($distribution, 2);
        }
        
        // La diversité est l'inverse de la concentration (normalisée entre 0 et 100)
        $diversity = (1 - $concentration) * 100;
        
        // Ajuster en fonction du nombre d'ancres uniques
        $diversity_adjusted = $diversity * min(1, $unique_count / 5);
        
        return round($diversity_adjusted);
    }
}
