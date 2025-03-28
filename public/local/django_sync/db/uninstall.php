<?php

function xmldb_local_django_sync_uninstall() {
    global $DB;

    $dbman = $DB->get_manager();
    $table = new xmldb_table('user'); // mdl_user

    $field1 = new xmldb_field('edeboid');
    if ($dbman->field_exists($table, $field1)) {
        $dbman->drop_field($table, $field1);
    }

    $field2 = new xmldb_field('djangoid');
    if ($dbman->field_exists($table, $field2)) {
        $dbman->drop_field($table, $field2);
    }

    $field3 = new xmldb_field('iasid');
    if ($dbman->field_exists($table, $field3)) {
        $dbman->drop_field($table, $field3);
    }
}