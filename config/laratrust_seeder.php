<?php

return [
    'role_structure' => [
        'superadmin' => [
            'settings' => 'r,u',
            'users' => 'c,r,u,d',
            'posts' => 'c,r,u,d,p,a',
            'general' => 'c,r,u,d',
            'media' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'testsuperadmin' => [
            'settings' => 'r',
            'users' => 'r',
            'posts' => 'c,r,p,a',
            'general' => 'c,r',
            'media' => 'c,r',
            'profile' => 'r,u'
        ],
        'admin' => [
            'posts' => 'c,r,u,d,p,a',
            'general' => 'c,r,u,d',
            'media' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'testadmin' => [
            'posts' => 'c,r,p,a',
            'general' => 'c,r',
            'media' => 'c,r',
            'profile' => 'r,u'
        ],
        'editor' => [
            'posts' => 'c,r,u,d,p,a',
            // 'general' => 'r',
            'media' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'author' => [
            'posts' => 'c,r,u,d,p',
            'media' => 'c,r',
            'profile' => 'r,u'
        ],
        'contributor' => [
            'posts' => 'c,r,u,d',
            'media' => 'c,r',
            'profile' => 'r,u'
        ],
        'subscriber' => [
            'profile' => 'r,u'
        ],
    ],
    'permission_structure' => [],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
        'p' => 'publish',
        'a' => 'all'
    ]
];
