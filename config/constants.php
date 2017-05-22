<?php
return [
    'CHALLENGE_TYPES' => [
        'DUO'   => 0,
        'SPOT'  => 1,
        'PLAY'  => 2
    ],

    'CHALLENGE_TYPES_STR' => [
        'DUO'   => 'duo',
        'SPOT'  => 'spot',
        'PLAY'  => 'play'
    ],

    'CHALLENGE_STATUS' => [
        'UNSET'         => -1,
        'INVITED'       => 0,
        'COMPLETED'     => 1,
        'ACCEPTED'      => 2,
        'DECLINED'      => 3
    ],

    'INVITATION_STATUS' => [
        'UNSET'         => -1,
        'INVITED'       => 0,
        'ACCEPTED'      => 1,
        'DECLINED'      => 2
    ],

    'SOCIAL_STATUS' => [
        'UNSET'          => 0,
        'PENDING'        => 1,
        'COMPLETED'      => 2,
        'CONFIRMED'      => 3,
    ],

    'KEY_TYPE' => [
        'FACEBOOK_INTEGRATION_CONFIRM' => 0,
    ],

    'KEY_STATUS' => [
        'VALID' => 0,
        'EXPIRED' => 1,
    ]
];