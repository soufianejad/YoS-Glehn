<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Informations générales de la plateforme
    |--------------------------------------------------------------------------
    */
    'name' => env('PLATFORM_NAME', 'Plateforme de Lecture'),
    'description' => env('PLATFORM_DESCRIPTION', 'Votre bibliothèque numérique africaine'),
    'logo' => env('PLATFORM_LOGO', '/assets/images/logo.png'),
    'favicon' => env('PLATFORM_FAVICON', '/assets/images/favicon.ico'),
    'contact_email' => env('PLATFORM_CONTACT_EMAIL', 'contact@plateforme.com'),
    'contact_phone' => env('PLATFORM_CONTACT_PHONE', '+225 XX XX XX XX XX'),
    'address' => env('PLATFORM_ADDRESS', 'Abidjan, Côte d\'Ivoire'),

    /*
    |--------------------------------------------------------------------------
    | Configuration des prix (en FCFA)
    |--------------------------------------------------------------------------
    */
    'pricing' => [
        // Abonnements individuels
        'individual' => [
            'monthly' => [
                'price' => 7000,
                'duration_days' => 30,
                'name' => 'Mensuel',
            ],
            'annual' => [
                'price' => 50000,
                'duration_days' => 365,
                'name' => 'Annuel',
            ],
        ],

        // Achats ponctuels
        'single_purchase' => [
            'pdf_default' => 3000,
            'audio_default' => 3500,
        ],

        // Abonnements scolaires
        'school' => [
            'tier_1' => [
                'price' => 200000,
                'max_students' => 200,
                'duration_days' => 30,
                'name' => 'École - 200 élèves',
            ],
            'tier_2' => [
                'price' => 350000,
                'max_students' => 450,
                'duration_days' => 30,
                'name' => 'École - 450 élèves',
            ],
            'tier_3' => [
                'price' => 650000,
                'max_students' => null, // Illimité
                'duration_days' => 30,
                'name' => 'École - Illimité',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Téléchargements
    |--------------------------------------------------------------------------
    */
    'downloads' => [
        'subscription_discount_percentage' => 20, // 20% de réduction pour les abonnés
    ],

    /*
    |--------------------------------------------------------------------------
    | Répartition des revenus
    |--------------------------------------------------------------------------
    */
    'revenue_split' => [
        // Contenu original de l'auteur
        'original_content' => [
            'author_percentage' => 80,
            'platform_percentage' => 20,
        ],

        // Contenu produit/traduit par la plateforme
        'platform_produced' => [
            'author_percentage' => 60,
            'platform_percentage' => 40,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des fichiers
    |--------------------------------------------------------------------------
    */
    'files' => [
        'max_sizes' => [
            'cover_image' => 2048, // KB (2 MB)
            'pdf' => 51200, // KB (50 MB)
            'audio' => 102400, // KB (100 MB)
        ],

        'allowed_types' => [
            'cover' => ['jpg', 'jpeg', 'png', 'webp'],
            'pdf' => ['pdf'],
            'audio' => ['mp3', 'wav', 'm4a', 'aac'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des quiz
    |--------------------------------------------------------------------------
    */
    'quiz' => [
        'default_questions_count' => 10,
        'default_pass_score' => 60, // Pourcentage
        'default_time_limit' => 30, // Minutes
        'max_attempts' => 3, // Nombre de tentatives autorisées (null = illimité)
        'randomize_questions' => true,
        'show_correct_answers' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification - Badges et récompenses
    |--------------------------------------------------------------------------
    */
    'gamification' => [
        'enabled' => true,

        'badges' => [
            'first_book' => [
                'name' => 'Premier Livre',
                'description' => 'Terminer votre premier livre',
                'points' => 10,
                'books_required' => 1,
            ],
            'book_lover' => [
                'name' => 'Amoureux des Livres',
                'description' => 'Lire 10 livres',
                'points' => 50,
                'books_required' => 10,
            ],
            'quiz_master' => [
                'name' => 'Maître des Quiz',
                'description' => 'Réussir 10 quiz',
                'points' => 50,
                'quizzes_required' => 10,
            ],
            'curious_reader' => [
                'name' => 'Lecteur Curieux',
                'description' => '30 heures de lecture',
                'points' => 30,
                'minutes_required' => 1800, // 30 heures
            ],
        ],

        'points' => [
            'per_book_completed' => 10,
            'per_quiz_passed' => 5,
            'per_review' => 2,
            'per_hour_reading' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Langues disponibles
    |--------------------------------------------------------------------------
    */
    'languages' => [
        'fr' => 'Français',
        'en' => 'English',
        'baoule' => 'Baoulé',
        'malinke' => 'Malinké',
        'bete' => 'Bété',
        'wolof' => 'Wolof',
        'swahili' => 'Swahili',
        'bambara' => 'Bambara',
    ],

    /*
    |--------------------------------------------------------------------------
    | Espaces de contenu
    |--------------------------------------------------------------------------
    */
    'spaces' => [
        'public' => [
            'name' => 'Espace Public',
            'description' => 'Livres accessibles à tous les lecteurs',
            'icon' => 'book-open',
            'color' => 'blue',
        ],
        'educational' => [
            'name' => 'Espace Éducatif',
            'description' => 'Contenu réservé aux établissements scolaires',
            'icon' => 'academic-cap',
            'color' => 'green',
        ],
        'adult' => [
            'name' => 'Espace Adulte',
            'description' => 'Contenu restreint sur invitation',
            'icon' => 'lock-closed',
            'color' => 'red',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration PWA (Progressive Web App)
    |--------------------------------------------------------------------------
    */
    'pwa' => [
        'enabled' => true,
        'name' => env('PWA_NAME', 'Lecture App'),
        'short_name' => env('PWA_SHORT_NAME', 'Lecture'),
        'description' => env('PWA_DESCRIPTION', 'Votre bibliothèque numérique'),
        'theme_color' => env('PWA_THEME_COLOR', '#1e40af'),
        'background_color' => env('PWA_BG_COLOR', '#ffffff'),
        'display' => 'standalone',
        'orientation' => 'portrait',
        'start_url' => '/',
        'scope' => '/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration mode hors ligne
    |--------------------------------------------------------------------------
    */
    'offline' => [
        'enabled' => true,
        'max_downloads_per_user' => 10, // Nombre max de livres téléchargeables
        'download_expiry_days' => 30, // Durée de validité des téléchargements
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'books_per_page' => 20,
        'users_per_page' => 50,
        'payments_per_page' => 50,
        'quiz_attempts_per_page' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => true,

        // Types de notifications
        'types' => [
            'new_book' => 'Nouveau livre disponible',
            'subscription_expiry' => 'Abonnement bientôt expiré',
            'quiz_available' => 'Nouveau quiz disponible',
            'book_approved' => 'Votre livre a été approuvé',
            'revenue_available' => 'Nouveaux revenus disponibles',
            'payout_processed' => 'Paiement effectué',
        ],

        // Délai d'avertissement avant expiration (jours)
        'subscription_expiry_alert_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Méthodes de paiement
    |--------------------------------------------------------------------------
    */
    'payment_methods' => [
        'mobile_money' => [
            'enabled' => true,
            'providers' => [
                'orange_money' => 'Orange Money',
                'mtn_money' => 'MTN Money',
                'moov_money' => 'Moov Money',
                'wave' => 'Wave',
            ],
        ],
        'card' => [
            'enabled' => true,
            'providers' => ['visa', 'mastercard'],
        ],
        'bank_transfer' => [
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Réseaux sociaux
    |--------------------------------------------------------------------------
    */
    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK', ''),
        'twitter' => env('SOCIAL_TWITTER', ''),
        'instagram' => env('SOCIAL_INSTAGRAM', ''),
        'youtube' => env('SOCIAL_YOUTUBE', ''),
        'linkedin' => env('SOCIAL_LINKEDIN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO et Meta
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_meta_description' => 'Découvrez notre bibliothèque numérique de littérature africaine',
        'default_meta_keywords' => 'lecture, livres, audio, éducation, Afrique',
        'og_image' => '/assets/images/og-image.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'enabled' => env('MAINTENANCE_MODE', false),
        'message' => env('MAINTENANCE_MESSAGE', 'Nous effectuons une maintenance. Nous serons de retour bientôt.'),
        'allowed_ips' => explode(',', env('MAINTENANCE_ALLOWED_IPS', '')),
    ],
];
