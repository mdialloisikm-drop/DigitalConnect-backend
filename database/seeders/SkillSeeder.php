<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Langages de programmation
            'PHP',
            'JavaScript',
            'Python',
            'Java',
            'C#',
            'Ruby',
            'Go',
            'Swift',
            'Kotlin',
            'TypeScript',
            'Rust',
            'Dart',
            'SQL',
            'C++',
            'Scala',

            // Frameworks & Bibliothèques Web
            'Symfony',
            'CodeIgniter',
            'Angular',
            'Next.js',
            'Nuxt.js',
            'Svelte',
            'jQuery',
            'Express.js',
            'Django',
            'Flask',
            'FastAPI',
            'Spring Boot',
            'ASP.NET',
            'Ruby on Rails',

            // Mobile
            'React Native',
            'Flutter',
            'Ionic',
            'SwiftUI',
            'Xamarin',
            'Android SDK',
            'iOS Development',

            // CMS & E-commerce
            'WordPress',
            'WooCommerce',
            'Shopify',
            'Magento',
            'PrestaShop',
            'Drupal',
            'Joomla',
            'Webflow',
            'Wix',
            'Squarespace',

            // Design
            'Adobe XD',
            'Sketch',
            'InVision',
            'Canva',
            'CorelDRAW',
            'Affinity Designer',
            'Adobe InDesign',

            // UI/UX
            'UI Design',
            'UX Design',
            'Wireframing',
            'Prototyping',
            'User Research',
            'Design Systems',
            'Responsive Design',
            'Mobile Design',
            'Web Design',
            'Interaction Design',

            // Frontend
            'HTML',
            'CSS',
            'Sass',
            'Less',
            'Bootstrap',
            'Tailwind CSS',
            'Material UI',
            'Ant Design',
            'Styled Components',
            'Webpack',
            'Vite',
            'Babel',

            // Backend & API
            'REST API',
            'GraphQL',
            'Microservices',
            'API Development',
            'WebSockets',
            'JSON',
            'XML',
            'OAuth',
            'JWT',

            // Bases de données
            'MySQL',
            'PostgreSQL',
            'MongoDB',
            'Redis',
            'SQLite',
            'MariaDB',
            'Firebase',
            'Oracle',
            'Microsoft SQL Server',
            'Elasticsearch',
            'DynamoDB',

            // DevOps & Cloud
            'AWS',
            'Google Cloud',
            'Azure',
            'Docker',
            'Kubernetes',
            'CI/CD',
            'Jenkins',
            'Git',
            'GitHub',
            'GitLab',
            'Bitbucket',
            'Linux',
            'Ubuntu',
            'Nginx',
            'Apache',
            'Terraform',
            'Ansible',

            // SEO & Marketing Digital
            'SEO On-Page',
            'SEO Off-Page',
            'SEO Technique',
            'Google Analytics',
            'Google Search Console',
            'Google Ads',
            'Facebook Ads',
            'Instagram Ads',
            'LinkedIn Ads',
            'TikTok Ads',
            'Email Marketing',
            'Marketing Automation',
            'Content Marketing',
            'Social Media Marketing',
            'Copywriting',
            'Growth Hacking',
            'A/B Testing',
            'Conversion Optimization',
            'SEM',

            // Réseaux sociaux
            'Facebook Marketing',
            'Instagram Marketing',
            'LinkedIn Marketing',
            'Twitter Marketing',
            'YouTube Marketing',
            'TikTok Marketing',
            'Pinterest Marketing',
            'Community Management',

            // Analyse & Data
            'Data Analysis',
            'Google Analytics 4',
            'Power BI',
            'Tableau',
            'Excel Avancé',
            'Google Sheets',
            'Data Visualization',
            'Web Scraping',
            'Python Data Science',
            'Pandas',
            'NumPy',
            'R Programming',
            'Statistiques',

            // Vidéo & Animation
            'Adobe Premiere Pro',
            'Final Cut Pro',
            'DaVinci Resolve',
            'After Effects',
            'Motion Graphics',
            'Video Editing',
            'Animation 2D',
            'Animation 3D',
            'Blender',
            'Cinema 4D',
            'Maya',
            '3ds Max',

            // Audio
            'Audio Editing',
            'Adobe Audition',
            'Audacity',
            'Sound Design',
            'Voice Over',
            'Podcast Editing',

            // Rédaction & Contenu
            'Content Writing',
            'Blog Writing',
            'Technical Writing',
            'Creative Writing',
            'Copywriting',
            'Ghostwriting',
            'Proofreading',
            'Translation',
            'Transcription',
            'Article Writing',
            'Product Descriptions',

            // E-commerce & Business
            'E-commerce',
            'Dropshipping',
            'Amazon FBA',
            'Product Listing',
            'Inventory Management',
            'Payment Integration',
            'Stripe',
            'PayPal',

            // Sécurité
            'Web Security',
            'Cybersecurity',
            'Penetration Testing',
            'SSL/TLS',
            'HTTPS',
            'Security Audit',

            // Gestion de projet
            'Project Management',
            'Agile',
            'Scrum',
            'Jira',
            'Trello',
            'Asana',
            'Monday.com',
            'Notion',

            // CRM & Automation
            'Salesforce',
            'HubSpot',
            'Zoho CRM',
            'Mailchimp',
            'SendGrid',
            'Zapier',
            'Make (Integromat)',

            // Testing & QA
            'Quality Assurance',
            'Manual Testing',
            'Automated Testing',
            'Selenium',
            'Jest',
            'PHPUnit',
            'Cypress',

            // Autres compétences digitales
            'Blockchain',
            'NFT',
            'Smart Contracts',
            'Machine Learning',
            'Artificial Intelligence',
            'ChatGPT Integration',
            'API Integration',
            'Third-party Integration',
            'Chrome Extension Development',
            'Web Scraping',
            'Data Entry',
            'Virtual Assistant',
            'Customer Support',
            'Technical Support',
            'Consulting',
            'Strategy',
            'Branding',
            'Logo Design',
            'Business Card Design',
            'Flyer Design',
            'Infographic Design',
            'Presentation Design',
            'Email Template Design',
            'Landing Page Design',
            'Banner Design',
            'Social Media Design',
            'Icon Design',
            'Illustration',
            'Character Design',
            'Vector Graphics',
            'Typography',
            'Color Theory',
            'Print Design',
            'Packaging Design',
            'UX Writing',
            'Microcopy',
            'Localization',
        ];
        $now = Carbon::now();
        $data = array_map(function($skill) use ($now) {
            return [
                'name' => $skill,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $skills);

        // Insertion par lots pour éviter les problèmes de mémoire
        $chunks = array_chunk($data, 50);
        foreach ($chunks as $chunk) {
            DB::table('skills')->insert($chunk);
        }
    }
}
