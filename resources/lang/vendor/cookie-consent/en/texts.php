<?php

$cookie_string = getOptionWithJsonDecode('gdpr_cookies', [
    'is_enabled' => false,
    'title' => 'Allow cookies',
    'description' => 'Your experience on this site will be improved by allowing cookies.',
]);

return [
    'message' => $cookie_string['description'],
    'agree' => 'Allow',
    'disagree' => 'Decline',
];
