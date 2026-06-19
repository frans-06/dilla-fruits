<?php
$admin_credentials = [
    [
        'username' => 'admin',
        'password' => 'admin123', 
        'level'    => 'admin'    
    ],
    [
        'username' => 'kasir1',
        'password' => 'kasir123',
        'level'    => 'admin'    
    ],
    [
        'username' => 'owner',
        'password' => 'owner123',
        'level'    => 'owner'    
    ]
];

function validateAdminLogin($input_username, $input_password) {
    global $admin_credentials;
    
    foreach ($admin_credentials as $account) {
        if ($account['username'] === $input_username && $account['password'] === $input_password) {
            return [
                'username' => $account['username'],
                'level'    => $account['level']
            ];
        }
    }
    
    return false;
}