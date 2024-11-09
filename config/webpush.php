<?php

return [

    /*
     * VAPID keys for authentication.
     */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:gkalfon@hotmail.com'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    /*
     * Model for storing push subscriptions.
     */
    'model' => \NotificationChannels\WebPush\PushSubscription::class,

    /*
     * Table name for storing push subscriptions.
     */
    'table_name' => env('WEBPUSH_DB_TABLE', 'push_subscriptions'),

    /*
     * Database connection for the push subscriptions table.
     */
    'database_connection' => env('WEBPUSH_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),

    /*
     * Options for the Guzzle client.
     */
    'client_options' => [],

];
