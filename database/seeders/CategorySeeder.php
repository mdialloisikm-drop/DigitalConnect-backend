<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Développement Web & Mobile
            'Développement de site web vitrine',
            'Développement de site e-commerce',
            'Développement d\'application mobile iOS',
            'Développement d\'application mobile Android',
            'Développement d\'application web progressive (PWA)',
            'Développement de plugin WordPress',
            'Développement de thème WordPress',
            'Développement de site Shopify',
            'Développement de boutique WooCommerce',
            'Intégration HTML/CSS responsive',
            'Développement d\'API REST',
            'Développement de landing page',
            'Développement de tableau de bord admin',
            'Développement de plateforme SaaS',
            'Développement de marketplace',
            'Migration de site web',
            'Optimisation des performances web',
            'Développement de chatbot',
            'Développement d\'extension Chrome',
            'Développement React/Vue/Angular',

            // Design & Création
            'Design de logo et identité visuelle',
            'Design d\'interface utilisateur (UI)',
            'Design d\'expérience utilisateur (UX)',
            'Design de maquettes web (mockups)',
            'Design d\'application mobile',
            'Design de bannières publicitaires',
            'Design de posts réseaux sociaux',
            'Design d\'infographies',
            'Design de présentation PowerPoint',
            'Design de newsletter email',
            'Design de packaging produit',
            'Illustration digitale',
            'Création d\'icônes personnalisées',
            'Design de brochure digitale',
            'Design de catalogue produit',
            'Retouche photo professionnelle',
            'Création de GIF animés',
            'Design de signature email',
            'Design de miniature YouTube',
            'Design de couverture e-book',

            // Marketing Digital
            'Stratégie de marketing digital',
            'Gestion de campagne Google Ads',
            'Gestion de campagne Facebook Ads',
            'Optimisation du référencement SEO',
            'Audit SEO complet',
            'Rédaction de contenu SEO',
            'Création de stratégie de contenu',
            'Gestion de réseaux sociaux',
            'Création de calendrier éditorial',
            'Analyse de données Google Analytics',
            'Email marketing et automation',
            'Marketing d\'affiliation',
            'Growth hacking',
            'Stratégie d\'influence marketing',
            'Community management',
            'Publicité LinkedIn Ads',
            'Remarketing et retargeting',
            'Optimisation du taux de conversion (CRO)',
            'Marketing automation',
            'Analyse de la concurrence',

            // Vidéo & Animation
            'Montage vidéo professionnel',
            'Création de vidéo explicative',
            'Animation motion design',
            'Création de vidéo publicitaire',
            'Sous-titrage de vidéo',
            'Création d\'intro et outro vidéo',
            'Animation de logo',
            'Création de vidéo de formation',
            'Post-production vidéo',
            'Création de story Instagram/TikTok',
            'Animation 2D',
            'Animation 3D',
            'Création de reel Instagram',
            'Montage vidéo YouTube',
            'Création de vidéo de présentation',

            // Rédaction & Contenu
            'Rédaction d\'articles de blog',
            'Rédaction de pages web',
            'Rédaction de fiches produits',
            'Rédaction de scripts vidéo',
            'Rédaction de communiqués de presse',
            'Rédaction de livre blanc (white paper)',
            'Rédaction de case study',
            'Traduction de contenu web',
            'Relecture et correction',
            'Rédaction de contenu LinkedIn',
            'Ghostwriting',
            'Transcription audio/vidéo',
            'Rédaction de CV et lettre de motivation',
            'Rédaction technique et documentation',

            // Data & Analytics
            'Création de dashboard Power BI',
            'Création de dashboard Tableau',
            'Analyse de données avec Excel',
            'Web scraping et extraction de données',
            'Création de rapports analytics',
            'Nettoyage et préparation de données',
            'Visualisation de données',
            'Automatisation avec Google Sheets',
            'Configuration Google Tag Manager',
            'Audit de tracking analytics',
        ];

        $now = Carbon::now();
        $data = array_map(function($category) use ($now) {
            return [
                'name' => $category,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $categories);

        DB::table('categories')->insert($data);
    }
}
