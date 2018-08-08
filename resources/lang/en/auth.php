<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'login' => [
        'title' => 'Sign In',
        'form' => [
            'label' => [
                'email'    => 'Email',
                'password' => 'Password',
            ],
            'placeholder' => [
                'email'    => 'Email',
                'password' => 'Password',
            ],
            'button' => [
                'login'  => 'Login',
                'forgot' => 'Forgot Password'
            ]
        ],        
    ],
    'messages' => [
        'successfully logged in' => 'Successfully logged in.',
        'failed logged in' => 'Email or password incorrect.',
        'not permission' => 'You are not permission login to Admin Site.',
    ]
];
