<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'envoi de notifications push via Firebase
    |
    */

    'credentials' => [
        /*
         | Chemin vers le fichier JSON des credentials Firebase
         | Tu devras télécharger ce fichier depuis Firebase Console:
         | Paramètres du projet → Comptes de service → Générer une nouvelle clé privée
         */
        'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/firebase-credentials.json')),
    ],

    /*
     | URL de l'API Firebase (ne pas modifier)
     */
    'api_url' => 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send',

    /*
     | Configuration des notifications par défaut
     */
    'notification' => [
        'icon' => env('FCM_NOTIFICATION_ICON', '/logo.png'),
        'sound' => env('FCM_NOTIFICATION_SOUND', 'default'),
        'badge' => env('FCM_NOTIFICATION_BADGE', '/logo.png'),
    ],

    /*
     | Time to live pour les notifications (en secondes)
     | Par défaut: 4 semaines
     */
    'ttl' => env('FCM_TTL', 2419200),
];
