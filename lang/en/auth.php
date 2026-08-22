<?php

return [
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'verify' => [
        'subject' => 'Verify your email address',
        'greeting' => 'Hello :name,',
        'line1' => 'Please click the button below to verify your email address.',
        'action' => 'Verify email address',
        'thanks' => 'If you did not create an account, no further action is required.',
    ],

    'reset' => [
        'subject' => 'Reset your password',
        'line1' => 'You are receiving this email because we received a password reset request for your account.',
        'action' => 'Reset password',
        'expire' => 'This link expires in :count minutes.',
        'line2' => 'If you did not request a password reset, no further action is required.',
    ],
];
