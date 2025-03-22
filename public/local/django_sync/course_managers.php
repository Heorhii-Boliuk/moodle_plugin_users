<?php
require_once('../../config.php');
require_login();
$PAGE->set_url('/course_managers.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Менеджери курсів');
$PAGE->set_heading('Список менеджерів курсів');

echo $OUTPUT->header();

global $DB, $CFG;

// SQL-запит для отримання менеджерів та кількості курсів, якими вони керують
$sql = "SELECT u.id, u.firstname, u.lastname, u.email, COUNT(c.id) AS course_count
        FROM {user} u
        JOIN {role_assignments} ra ON u.id = ra.userid
        JOIN {role} r ON ra.roleid = r.id
        JOIN {context} ctx ON ra.contextid = ctx.id
        JOIN {course} c ON ctx.instanceid = c.id
        WHERE r.shortname = 'manager'
        GROUP BY u.id, u.firstname, u.lastname, u.email
        ORDER BY u.lastname, u.firstname";

$managers = $DB->get_records_sql($sql);

echo "<h2>Менеджери курсів</h2>";
echo "<table border='1'>
        <tr>
            <th>Ім'я</th>
            <th>Email</th>
            <th>Кількість курсів</th>
        </tr>";

foreach ($managers as $manager) {
    echo "<tr>
            <td><a href='{$CFG->wwwroot}/user/profile.php?id={$manager->id}'>{$manager->firstname} {$manager->lastname}</a></td>
            <td>{$manager->email}</td>
            <td>{$manager->course_count}</td>
          </tr>";
}
echo "</table>";

echo $OUTPUT->footer();