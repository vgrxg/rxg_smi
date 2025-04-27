<?php
/**
 * Template pour la documentation du plugin
 */
?>
<div class="wrap rxg-smi-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rxg-smi-documentation">
        <div class="rxg-smi-doc-navigation">
            <ul>
                <li><a href="#introduction">Introduction</a></li>
                <li><a href="#importance">Pourquoi le maillage interne est important</a></li>
                <li><a href="#fondements">Fondements techniques et scientifiques</a></li>
                <li><a href="#pages">Pages orphelines</a></li>
                <li><a href="#profondeur">Profondeur de page</a></li>
                <li><a href="#juice">Distribution du "jus de lien"</a></li>
                <li><a href="#ancre">Diversité des textes d'ancre</a></li>
                <li><a href="#clusters">Clusters thématiques</a></li>
                <li><a href="#semantique">Analyse sémantique</a></li>
            </ul>
        </div>
        
        <div class="rxg-smi-doc-content">
            <!-- Le contenu complet de la documentation sera inséré ici -->
            <section id="introduction">
                <h2>Introduction</h2>
                <p>RXG Site Maillage Interne est un plugin WordPress dédié à l'analyse et l'optimisation de votre maillage interne - l'ensemble des liens qui connectent les pages de votre site entre elles. Un maillage interne bien structuré est fondamental pour le référencement et l'expérience utilisateur.</p>
            </section>
            
            <!-- Intégrer ici tout le contenu de la documentation que j'ai fournie précédemment -->
            
            <!-- Par exemple, la section sur les fondements techniques -->
            <section id="fondements">
                <h2>Fondements techniques et scientifiques des métriques du plugin</h2>
                <p>Cette section explique les bases scientifiques et techniques sur lesquelles reposent les analyses et recommandations du plugin.</p>
                
                <!-- Insérer ici le contenu détaillé -->
            </section>
            
            <!-- Exemple pour la section des pages orphelines -->
            <section id="pages">
                <h2>Pages orphelines</h2>
                <h3>Affirmation du plugin</h3>
                <p><em>"Ces pages ne reçoivent aucun lien interne, ce qui les rend difficiles à découvrir pour les utilisateurs et les moteurs de recherche."</em></p>
                
                <h3>Base technique et scientifique</h3>
                <ol>
                    <li>
                        <strong>Crawl et découverte par les moteurs de recherche</strong> : Les moteurs de recherche comme Google découvrent les pages d'un site principalement en suivant les liens. Cette approche est documentée dans les brevets de Google (comme le brevet "Reasonable Surfer" US Patent 8,626,752) et dans les déclarations officielles de Google. Sans liens internes, une page peut uniquement être découverte si :
                        <ul>
                            <li>Elle est incluse dans le sitemap XML (qui n'est qu'une indication, pas une garantie d'indexation)</li>
                            <li>Elle est liée depuis un site externe</li>
                            <li>Son URL est soumise directement via Google Search Console</li>
                        </ul>
                    </li>
                    <!-- Continuer avec les autres points -->
                </ol>
            </section>
            
            <!-- Continuer avec les autres sections -->
        </div>
    </div>
</div>

<style>
.rxg-smi-documentation {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.rxg-smi-doc-navigation {
    flex: 0 0 250px;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 32px; /* WP Admin bar height */
    max-height: calc(100vh - 52px);
    overflow-y: auto;
}

.rxg-smi-doc-navigation ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.rxg-smi-doc-navigation li {
    margin-bottom: 10px;
}

.rxg-smi-doc-navigation a {
    text-decoration: none;
    display: block;
    padding: 5px 10px;
    border-left: 2px solid transparent;
}

.rxg-smi-doc-navigation a:hover {
    background: #f8f9fa;
    border-left: 2px solid #2271b1;
}

.rxg-smi-doc-content {
    flex: 1;
    background: #fff;
    padding: 30px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.rxg-smi-doc-content section {
    margin-bottom: 40px;
    border-bottom: 1px solid #eee;
    padding-bottom: 30px;
}

.rxg-smi-doc-content section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.rxg-smi-doc-content h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f1;
}

.rxg-smi-doc-content h3 {
    margin: 25px 0 15px;
    color: #2271b1;
}

.rxg-smi-doc-content p {
    line-height: 1.7;
}

.rxg-smi-doc-content ul, 
.rxg-smi-doc-content ol {
    margin-left: 20px;
    margin-bottom: 20px;
}

.rxg-smi-doc-content li {
    margin-bottom: 10px;
    line-height: 1.6;
}

.rxg-smi-doc-content em {
    background: #f8f9fa;
    padding: 2px 5px;
    font-style: italic;
    border-radius: 3px;
}

.rxg-smi-doc-content strong {
    color: #2271b1;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Smooth scrolling pour les liens d'ancrage
    $('.rxg-smi-doc-navigation a').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 50
        }, 500);
    });
    
    // Surligner l'élément actif dans la navigation
    $(window).on('scroll', function() {
        var scrollPosition = $(window).scrollTop();
        
        $('.rxg-smi-doc-content section').each(function() {
            var currentSection = $(this);
            var sectionTop = currentSection.offset().top - 100;
            var sectionBottom = sectionTop + currentSection.outerHeight();
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                var id = currentSection.attr('id');
                $('.rxg-smi-doc-navigation a').removeClass('active');
                $('.rxg-smi-doc-navigation a[href="#' + id + '"]').addClass('active');
            }
        });
    });
});
</script>