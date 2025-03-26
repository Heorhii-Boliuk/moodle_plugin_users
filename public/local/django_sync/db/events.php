<?php

$observers = [
    [
        'eventname'   => '\core\event\user_grade',
        'callback'    => 'local_django_sync_observer::grade_updated',
        'includefile' => '/local/django_sync/classes/observer.php',
    ],
];
