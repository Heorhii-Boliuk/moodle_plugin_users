<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_externalpage(
        'local_django_sync',
        'Sync with Django',
        new moodle_url('/local/django_sync/sync.php')
    );
    $ADMIN->add('localplugins', $settings);
    $settings = new admin_externalpage(
        'local_django_managers',
        'Managers per course count',
        new moodle_url('/local/django_sync/course_managers.php')
    );
    $ADMIN->add('localplugins', $settings);
    $settings = new admin_externalpage(
        'local_django_courses',
        'Count courses per category',
        new moodle_url('/local/django_sync/count_courses.php')
    );
    $ADMIN->add('localplugins', $settings);
}
