<?php

$observers = [
    [
        'eventname'   => '\core\event\grade_updated',
        'callback'    => 'local_myplugin_observer::grade_updated',
    ],
];
