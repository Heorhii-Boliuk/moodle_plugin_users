<?php

function xmldb_local_django_sync_install() {
    global $DB;

    $dbman = $DB->get_manager();
    $table = new xmldb_table('user'); // mdl_user

    // Добавляем поле edeboid
    $field1 = new xmldb_field('edeboid', XMLDB_TYPE_CHAR, '255', null, false, false, null, 'email');
    if (!$dbman->field_exists($table, $field1)) {
        $dbman->add_field($table, $field1);
    }

    // Добавляем поле djangoid
    $field2 = new xmldb_field('djangoid', XMLDB_TYPE_CHAR, '255', null, false, false, null, 'edeboid');
    if (!$dbman->field_exists($table, $field2)) {
        $dbman->add_field($table, $field2);
    }

    // Добавляем поле iasid
    $field3 = new xmldb_field('iasid', XMLDB_TYPE_CHAR, '255', null, false, false, null, 'djangoid');
    if (!$dbman->field_exists($table, $field3)) {
        $dbman->add_field($table, $field3);
    }
}
