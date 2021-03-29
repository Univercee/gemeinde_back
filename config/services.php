<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailjet' => [
      'key' => env('MAILJET_APIKEY'),
      'secret' => env('MAILJET_APISECRET'),
      'transactional' => [
          'call' => true,
          'options' => [
              'url' => 'api.mailjet.com',
              'version' => 'v3.1',
              'call' => true,
              'secured' => true
          ]
      ],
      'common' => [
          'call' => true,
          'options' => [
              'url' => 'api.mailjet.com',
              'version' => 'v3',
              'call' => true,
              'secured' => true
          ]
      ]
    ]
];
