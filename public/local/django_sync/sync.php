<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/local/django_sync/lib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url(new moodle_url('/local/django_sync/sync.php'));
$PAGE->set_context($context);
$PAGE->set_heading('Sync Django ↔ Moodle');

echo $OUTPUT->header();
echo $OUTPUT->heading('Sync Users');

$updated_users = [];
$only_in_moodle = [];
$only_in_django = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sync_users'])) {
        list($only_in_moodle, $only_in_django, $common_users) = sync_users();
        $updated_users = batch_update_moodle_users();
        echo $OUTPUT->notification("✅ Sync complete! Updated: $updated_users users.", 'notifysuccess');
    } elseif (isset($_POST['create_moodle_user'])) {
        $django_user = json_decode($_POST['user_data'], true);
        create_moodle_user($django_user);
    } elseif (isset($_POST['create_django_user'])) {
        $moodle_user = json_decode($_POST['user_data'], true);
        create_django_user($moodle_user);
    }
}

echo '<form method="post">';
echo '<button type="submit" name="sync_users" class="btn btn-primary">🔄 Sync Users</button>';
echo '</form>';

if (!empty($only_in_moodle)) {
    echo "<h3>❌ Пользователи только в Moodle</h3><ul>";
    foreach ($only_in_moodle as $user) {
        echo "<li>
                {$user['email']} 
                <form method='post' style='display:inline'>
                    <input type='hidden' name='user_data' value='" . json_encode($user) . "'>
                    <button type='submit' name='create_django_user' class='btn btn-success btn-sm'>Create in Django</button>
                </form>
              </li>";
    }
    echo "</ul>";
}

if (!empty($only_in_django)) {
    echo "<h3>❌ Users only in Django</h3><ul>";
    foreach ($only_in_django as $user) {
        echo "<li>
                {$user['email']} 
                <form method='post' style='display:inline'>
                    <input type='hidden' name='user_data' value='" . json_encode($user) . "'>
                    <button type='submit' name='create_moodle_user' class='btn btn-info btn-sm'>Create in Moodle</button>
                </form>
              </li>";
    }
    echo "</ul>";
}

echo $OUTPUT->footer();
