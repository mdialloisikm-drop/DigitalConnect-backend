<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsAndTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Définition des projets pour les 3 clients
        $projects = [

            [
                'id' => 13,
                'client_id' => 1,
                'title' => 'Refonte complète du site e-commerce avec système de recommandation',
                'description' => 'Notre boutique en ligne de vêtements nécessite une refonte complète. Nous vendons des vêtements haut de gamme et avons besoin d\'un site moderne et performant. Le projet comprend : Catalogue produits avec filtres avancés (prix, taille, couleur, marque), système intelligent de recommandation de taille basé sur les mensurations, panier d\'achat avec codes promo, intégration Stripe pour les paiements, gestion multi-adresses de livraison, espace client avec historique des commandes, système de wishlist, suivi de livraison en temps réel. Design responsive obligatoire.',
                'category_id' => 9, // Développement de site e-commerce
                'budget' => 5000.00,
                'duration' => 45,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [8, 25, 109, 84], // Laravel, PHP, MySQL, Responsive Design
                'tasks' => [
                    [
                        'title' => 'Configurer l\'environnement de développement',
                        'description' => 'Installer Laravel, configurer la base de données MySQL, mettre en place Git, créer la structure MVC de base, installer les dépendances (Tailwind CSS, Livewire si nécessaire).',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Développer le système de gestion des produits',
                        'description' => 'Créer le CRUD complet pour les produits (nom, description, prix, images multiples, tailles, couleurs, stock), système de variations produits, gestion des catégories et sous-catégories.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Implémenter le système de recommandation de taille',
                        'description' => 'Développer un questionnaire interactif (mensurations utilisateur), algorithme de recommandation de taille basé sur les mesures, guide des tailles par produit, historique des tailles achetées.',
                        'order' => 3,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Intégrer le système de paiement Stripe',
                        'description' => 'Configurer Stripe, implémenter le tunnel de paiement sécurisé, gérer les webhooks, afficher les confirmations de paiement, envoyer les emails de confirmation automatiques.',
                        'order' => 4,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Créer le système de panier et checkout',
                        'description' => 'Développer le panier d\'achat avec ajout/suppression/modification, calcul du total et frais de livraison, codes promo, sauvegarde du panier pour utilisateurs connectés.',
                        'order' => 5,
                        'priority' => 'haute',
                    ],
                ],
            ],
            [
                'id' => 14,
                'client_id' => 1,
                'title' => 'Campagne publicitaire Facebook et Instagram pour lancement produit',
                'description' => 'Nous lançons une nouvelle gamme de produits cosmétiques bio et avons besoin d\'un expert en publicité Facebook/Instagram. Budget publicitaire : 3000$ sur 30 jours. Mission : Créer et gérer une campagne complète de A à Z. Définir la stratégie et les audiences cibles, créer 5 visuels publicitaires professionnels conformes à notre charte graphique, rédiger les textes accrocheurs avec appels à l\'action, mettre en place des tests A/B sur visuels et audiences, optimiser quotidiennement les performances, fournir des rapports hebdomadaires détaillés avec recommandations. Objectif : Maximiser les conversions et le ROAS.',
                'category_id' => 50, // Gestion de campagne Facebook Ads
                'budget' => 1000.00,
                'duration' => 30,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [143, 144, 156, 157, 151, 153], // Facebook Ads, Instagram Ads, Facebook Marketing, Instagram Marketing, Copywriting, A/B Testing
                'tasks' => [
                    [
                        'title' => 'Définir la stratégie publicitaire',
                        'description' => 'Analyser le produit et la cible, définir les objectifs de campagne (notoriété, trafic, conversions), créer les personas détaillés, planifier le budget par audience, établir les KPI à suivre.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Créer les audiences ciblées',
                        'description' => 'Configurer les audiences Facebook (démographie, intérêts, comportements), créer des audiences personnalisées (visiteurs site, clients existants), développer des audiences similaires (lookalike), tester 3-5 audiences différentes.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Designer les visuels publicitaires',
                        'description' => 'Créer 5 visuels attractifs conformes aux guidelines Facebook/Instagram, décliner en formats (carré, vertical, story), rédiger les textes accrocheurs avec CTA clairs, préparer 2-3 variations par visuel pour A/B testing.',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Lancer et optimiser les campagnes',
                        'description' => 'Configurer les campagnes dans Ads Manager, implémenter le pixel Facebook, lancer les tests A/B sur visuels et audiences, analyser les performances quotidiennes, réallouer le budget vers les meilleures campagnes.',
                        'order' => 4,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Effectuer le suivi et reporting hebdomadaire',
                        'description' => 'Créer un dashboard de suivi (impressions, clics, CTR, conversions, CPA, ROAS), analyser les métriques chaque semaine, ajuster les enchères et audiences, produire un rapport PDF avec recommandations.',
                        'order' => 5,
                        'priority' => 'moyenne',
                    ],
                ],
            ],

            // PROJETS DU CLIENT 2
            [
                'id' => 15,
                'client_id' => 2,
                'title' => 'Design d\'identité visuelle complète pour startup tech',
                'description' => 'Notre startup technologique en phase de lancement recherche un designer talentueux pour créer notre identité visuelle de A à Z. Nous développons une solution SaaS B2B et avons besoin d\'une image professionnelle, moderne et innovante. Livrables attendus : 3 propositions de logo différentes (typographique, pictogramme, combiné), charte graphique complète (palette de couleurs primaire et secondaire, typographies pour titres et corps de texte, règles d\'utilisation du logo avec espacements minimaux), cartes de visite recto-verso prêtes pour l\'impression, 10 templates pour réseaux sociaux (Instagram, Facebook, LinkedIn) personnalisables. Nous recherchons un style épuré, tech, avec une touche d\'originalité.',
                'category_id' => 28, // Design de logo et identité visuelle
                'budget' => 1500.00,
                'duration' => 15,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [6, 7, 74], // Adobe Illustrator, Figma, Canva
                'tasks' => [
                    [
                        'title' => 'Recherche et brainstorming créatif',
                        'description' => 'Analyser la concurrence, définir les valeurs de la marque, créer un mood board avec inspirations visuelles, proposer 3 directions artistiques différentes basées sur le brief client.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Créer 3 propositions de logo',
                        'description' => 'Concevoir 3 concepts de logo différents (typographique, pictogramme, combiné), chacun décliné en couleur et noir & blanc, avec variations (horizontal, vertical, icône seule).',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Développer la charte graphique complète',
                        'description' => 'Créer le document de charte graphique incluant : palette de couleurs (primaire, secondaire, nuances), typographies (titres, corps de texte, web), règles d\'utilisation du logo, espacements minimaux.',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Designer les cartes de visite',
                        'description' => 'Concevoir le design des cartes de visite recto-verso en appliquant l\'identité visuelle, préparer les fichiers pour l\'impression (format PDF avec traits de coupe, CMJN, 300 DPI).',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Créer les templates réseaux sociaux',
                        'description' => 'Designer 10 templates pour Instagram/Facebook/LinkedIn (posts, stories, couvertures) facilement personnalisables, livrés en formats Photoshop et Canva.',
                        'order' => 5,
                        'priority' => 'moyenne',
                    ],
                ],
            ],
            [
                'id' => 16,
                'client_id' => 2,
                'title' => 'Audit SEO et optimisation complète pour site d\'architecture',
                'description' => 'Cabinet d\'architecture cherche expert SEO pour améliorer drastiquement notre visibilité sur Google. Notre site web manque de trafic organique malgré la qualité de nos projets. Mission complète : Réaliser un audit SEO technique approfondi (vitesse, mobile, erreurs 404, structure, Schema.org), recherche et analyse de 50-100 mots-clés pertinents dans le secteur de l\'architecture, optimisation on-page de toutes les pages (balises title, meta descriptions, H1-H6, URLs, images), rédaction de 10 articles de blog optimisés SEO (1500-2000 mots chacun) sur des thématiques architecture, développement d\'une stratégie de backlinks avec identification de 20-30 sites partenaires, configuration Google Analytics 4 et Search Console avec rapport mensuel. Délai : 30 jours.',
                'category_id' => 51, // Optimisation du référencement SEO
                'budget' => 2500.00,
                'duration' => 30,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [1, 137, 138, 139, 140, 141], // SEO, SEO On-Page, SEO Off-Page, SEO Technique, Google Analytics, Google Search Console
                'tasks' => [
                    [
                        'title' => 'Effectuer l\'audit SEO technique complet',
                        'description' => 'Analyser la structure du site, vitesse de chargement, mobile-friendliness, erreurs 404, redirections, sitemap XML, robots.txt, données structurées Schema.org, HTTPS, Core Web Vitals.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Recherche et analyse des mots-clés',
                        'description' => 'Identifier 50-100 mots-clés pertinents pour le secteur de l\'architecture, analyser le volume de recherche, la concurrence, l\'intention de recherche, créer une stratégie de ciblage par page.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Optimiser le SEO on-page',
                        'description' => 'Réécrire les balises title et meta descriptions, optimiser les H1-H6, améliorer le maillage interne, optimiser les URLs, ajouter du texte alternatif aux images, améliorer le contenu existant.',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Rédiger 10 articles de blog optimisés SEO',
                        'description' => 'Créer 10 articles de 1500-2000 mots sur des sujets liés à l\'architecture (tendances, conseils, projets), optimisés avec mots-clés, images optimisées, liens internes/externes.',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Développer la stratégie de backlinks',
                        'description' => 'Identifier 20-30 sites pertinents pour obtenir des backlinks, créer une stratégie de guest posting, soumettre le site aux annuaires de qualité, établir des partenariats.',
                        'order' => 5,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Configurer Google Analytics et Search Console',
                        'description' => 'Installer GA4, configurer les objectifs de conversion, créer un dashboard personnalisé, vérifier Search Console, soumettre le sitemap, créer un rapport mensuel automatisé.',
                        'order' => 6,
                        'priority' => 'haute',
                    ],
                ],
            ],
            [
                'id' => 17,
                'client_id' => 2,
                'title' => 'Montage de 20 vidéos promotionnelles immobilières pour réseaux sociaux',
                'description' => 'Agence immobilière en pleine expansion cherche monteur vidéo professionnel pour créer 20 vidéos courtes et percutantes. Nous fournissons tous les rushes (visites virtuelles, photos, plans). Spécifications techniques : Vidéos de 30 à 60 secondes, animations texte dynamiques avec informations clés (prix, surface, localisation), transitions fluides et professionnelles, musique libre de droits adaptée, sous-titres en français, logo animé en intro et outro. Formats requis : Vertical 1080x1920 pour Instagram/TikTok et Carré 1080x1080 pour Facebook. Style moderne, élégant, donnant envie. Les vidéos doivent capter l\'attention dans les 3 premières secondes.',
                'category_id' => 68, // Montage vidéo professionnel
                'budget' => 1200.00,
                'duration' => 20,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [5, 7], // Adobe Premiere Pro, Video Editing (ID fictif car non dans la liste)
                'tasks' => [
                    [
                        'title' => 'Organiser et trier les rushes vidéo',
                        'description' => 'Visionner tous les rushes fournis, créer une bibliothèque organisée par propriété, sélectionner les meilleurs plans, noter les timecodes importants, créer un document de planning des vidéos.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Créer le template graphique des vidéos',
                        'description' => 'Designer les éléments graphiques réutilisables (intro animée, lower thirds, transitions, animations texte, outro avec logo), définir la palette de couleurs et les polices conformes à la charte.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Monter les 10 premières vidéos',
                        'description' => 'Assembler les clips, ajouter les transitions, intégrer les animations texte, synchroniser la musique, ajouter les sous-titres, exporter en format vertical (1080x1920) et carré (1080x1080).',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Monter les 10 dernières vidéos',
                        'description' => 'Continuer le montage des 10 vidéos restantes en respectant le même style graphique, ajuster le rythme selon chaque propriété, optimiser pour l\'engagement sur réseaux sociaux.',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Ajouter sous-titres et finalisation',
                        'description' => 'Créer les sous-titres en français pour toutes les vidéos (format SRT + incrustés), effectuer la correction colorimétrique, équilibrer l\'audio, ajouter les logos animés, livrer les fichiers finaux.',
                        'order' => 5,
                        'priority' => 'moyenne',
                    ],
                ],
            ],

            // PROJETS DU CLIENT 7
            [
                'id' => 18,
                'client_id' => 7,
                'title' => 'Landing page haute conversion pour formation en marketing digital',
                'description' => 'Nous lançons une formation en ligne sur le marketing digital et avons besoin d\'une landing page qui convertit vraiment ! La page doit être optimisée pour la conversion avec un design moderne et professionnel. Éléments requis : Hero section impactante avec vidéo de présentation (que nous fournirons), section bénéfices avec icônes, témoignages clients avec photos et notes, programme détaillé de la formation, FAQ complète, formulaire d\'inscription optimisé avec intégration Mailchimp, boutons CTA stratégiquement placés, page de remerciement après inscription, design 100% responsive (mobile-first). Technologies : HTML5, CSS3, JavaScript vanilla. Optimisation vitesse de chargement obligatoire.',
                'category_id' => 19, // Développement de landing page
                'budget' => 800.00,
                'duration' => 10,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [88, 89, 26, 84, 154], // HTML, CSS, JavaScript, Responsive Design, Conversion Optimization
                'tasks' => [
                    [
                        'title' => 'Créer la maquette de la landing page',
                        'description' => 'Designer une maquette complète sur Figma incluant : hero section avec vidéo, section bénéfices, témoignages, programme de formation, FAQ, formulaire d\'inscription, footer. Versions desktop et mobile.',
                        'order' => 1,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Intégrer le HTML/CSS responsive',
                        'description' => 'Coder la landing page en HTML5/CSS3, utiliser Flexbox/Grid, assurer la compatibilité cross-browser, optimiser pour mobile-first, animations CSS au scroll.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Développer le formulaire d\'inscription',
                        'description' => 'Créer un formulaire avec validation JavaScript (nom, email, téléphone), messages d\'erreur personnalisés, intégration avec Mailchimp API, page de remerciement avec redirection automatique.',
                        'order' => 3,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Intégrer la vidéo de présentation',
                        'description' => 'Optimiser et intégrer la vidéo de présentation (YouTube ou hébergement direct), créer un player personnalisé avec overlay, tracking des vues, bouton CTA dans la vidéo.',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Optimiser pour la conversion',
                        'description' => 'Placer stratégiquement les CTA, optimiser les titres et textes persuasifs, ajouter des éléments de preuve sociale (compteur d\'inscrits, badges de confiance), implémenter Google Analytics avec tracking des conversions.',
                        'order' => 5,
                        'priority' => 'haute',
                    ],
                ],
            ],
            [
                'id' => 19,
                'client_id' => 7,
                'title' => 'Dashboard Power BI interactif pour suivi des KPI de ventes',
                'description' => 'Entreprise de distribution cherche expert Power BI pour créer un tableau de bord interactif et visuel de nos performances commerciales. Nous avons une base de données SQL Server avec toutes nos données de ventes, clients et produits. Le dashboard doit présenter : Chiffre d\'affaires global et par période (jour, semaine, mois, année), analyse par région géographique avec carte interactive, performance par produit et catégorie, analyse par commercial avec classement, graphiques d\'évolution des ventes, prévisions basées sur l\'historique, indicateurs KPI principaux (taux de croissance, panier moyen, nombre de clients), filtres dynamiques (période, région, produit, vendeur). Connexion automatique à SQL Server avec rafraîchissement quotidien. Formation de 2h pour notre équipe prévue.',
                'category_id' => 97, // Création de dashboard Power BI
                'budget' => 1800.00,
                'duration' => 20,
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [166, 170, 37, 164], // Power BI, Data Visualization, SQL, Data Analysis
                'tasks' => [
                    [
                        'title' => 'Analyser les besoins et sources de données',
                        'description' => 'Rencontrer l\'équipe pour définir les KPI prioritaires, identifier les sources de données (SQL Server, Excel, etc.), documenter la structure des tables, définir les relations entre les données.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Préparer et nettoyer les données',
                        'description' => 'Se connecter à SQL Server, importer les tables nécessaires, nettoyer les données (doublons, valeurs manquantes), créer les relations entre tables, développer les mesures DAX pour les calculs.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Créer les visualisations principales',
                        'description' => 'Développer les graphiques clés (CA par région, évolution mensuelle, top produits, performance par vendeur), utiliser des visuels interactifs (cartes, graphiques en cascade, jauges), appliquer un thème cohérent.',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Développer les filtres et interactivité',
                        'description' => 'Ajouter des slicers (période, région, produit, vendeur), configurer les interactions entre visuels, créer des drill-through pour détails, implémenter des tooltips personnalisés.',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Automatiser les mises à jour',
                        'description' => 'Configurer l\'actualisation automatique des données (quotidienne/hebdomadaire), paramétrer Power BI Service pour le partage, tester la mise à jour automatique, documenter le processus.',
                        'order' => 5,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Former l\'équipe et livrer la documentation',
                        'description' => 'Organiser une session de formation de 2h avec l\'équipe, créer un guide utilisateur PDF, démontrer comment utiliser les filtres et interpréter les données, recueillir les feedbacks.',
                        'order' => 6,
                        'priority' => 'moyenne',
                    ],
                ],
            ],
            [
                'id' => 20,
                'client_id' => 7,
                'title' => 'Automatisation des processus RH avec Power Automate',
                'description' => 'Notre département RH gère manuellement de nombreux processus répétitifs et chronophages. Nous cherchons un expert Power Automate pour automatiser nos workflows principaux. Processus à automatiser : Onboarding des nouveaux employés (envoi automatique des documents, création des comptes, ajout aux groupes Teams), gestion des demandes de congés (notification managers, mise à jour calendrier partagé, email de confirmation), processus de validation des notes de frais (workflow d\'approbation multi-niveaux), rappels automatiques pour les évaluations annuelles, synchronisation des données RH entre systèmes (SharePoint, Excel, Outlook). Intégrations requises : SharePoint Online, Outlook, Teams, Excel Online. Le freelance devra également créer une documentation complète et former 3 personnes de notre équipe RH.',
                'category_id' => 98, // Automatisation avec Power Automate
                'budget' => 2200.00,
                'duration' => 25,
                'start_date' => $now->copy()->addDays(15),
                'deadline' => $now->copy()->addDays(40),
                'status' => 'open',
                'progress' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'skills' => [167, 168, 169, 171], // Power Automate, Microsoft 365, SharePoint, Power Platform
                'tasks' => [
                    [
                        'title' => 'Analyser les processus RH actuels',
                        'description' => 'Rencontrer l\'équipe RH pour comprendre les workflows manuels, identifier les points de friction, documenter les processus actuels avec flowcharts, définir les priorités d\'automatisation.',
                        'order' => 1,
                        'priority' => 'urgente',
                    ],
                    [
                        'title' => 'Automatiser le processus d\'onboarding',
                        'description' => 'Créer un flow déclenché lors de l\'ajout d\'un nouvel employé dans SharePoint, envoi automatique des documents de bienvenue, création des comptes utilisateur, ajout aux groupes Teams appropriés, notification au manager.',
                        'order' => 2,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Développer le workflow de gestion des congés',
                        'description' => 'Créer un formulaire de demande de congé dans SharePoint/Forms, workflow d\'approbation automatique vers le manager, mise à jour du calendrier partagé, envoi d\'emails de confirmation, gestion des rejets avec commentaires.',
                        'order' => 3,
                        'priority' => 'haute',
                    ],
                    [
                        'title' => 'Automatiser la validation des notes de frais',
                        'description' => 'Développer un workflow multi-niveaux (manager > finance > direction si > 1000€), notifications par email et Teams, suivi du statut en temps réel, archivage automatique des documents validés.',
                        'order' => 4,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Créer les rappels d\'évaluation annuelle',
                        'description' => 'Flow automatique envoyant des rappels 30, 15 et 7 jours avant les échéances d\'évaluation, notifications aux managers et employés, mise à jour du statut dans SharePoint.',
                        'order' => 5,
                        'priority' => 'moyenne',
                    ],
                    [
                        'title' => 'Former l\'équipe et livrer la documentation',
                        'description' => 'Créer une documentation complète pour chaque workflow (avec captures d\'écran), organiser 2 sessions de formation de 2h pour l\'équipe RH, créer des guides de dépannage.',
                        'order' => 6,
                        'priority' => 'haute',
                    ],
                ],
            ]
        ];
        // Insertion des projets et leurs tâches
        foreach ($projects as $projectData) {
            // Extraire les skills et tasks avant l'insertion
            $skills = $projectData['skills'];
            $tasks = $projectData['tasks'];
            unset($projectData['skills'], $projectData['tasks']);

            // Insérer le projet
            DB::table('projects')->insert($projectData);

            // Associer les compétences au projet
            foreach ($skills as $skillId) {
                DB::table('project_skills')->insert([
                    'project_id' => $projectData['id'],
                    'skill_id' => $skillId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Insérer les tâches du projet
            foreach ($tasks as $taskData) {
                DB::table('project_tasks')->insert([
                    'project_id' => $projectData['id'],
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'order' => $taskData['order'],
                    'priority' => $taskData['priority'],
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('✅ ' . count($projects) . ' projets et leurs tâches ont été créés avec succès !');
    }
}
