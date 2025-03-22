<?php
require_once('../../config.php');
require_login();
$PAGE->set_url('/count_courses.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Кількість курсів');
$PAGE->set_heading('Кількість курсів у Moodle');

echo $OUTPUT->header();

global $DB, $CFG;

// SQL-запит для підрахунку кількості курсів у кожній категорії
$sql = "SELECT cat.id, cat.name AS category, COUNT(c.id) AS course_count
        FROM {course_categories} cat
        LEFT JOIN {course} c ON c.category = cat.id
        GROUP BY cat.id, cat.name
        ORDER BY cat.name";

$categories = $DB->get_records_sql($sql);

echo "<h2>Кількість курсів у категоріях</h2>";
echo "<table border='1'>
        <tr>
            <th>Назва категорії</th>
            <th>Кількість курсів</th>
        </tr>";

foreach ($categories as $category) {
    echo "<tr>
            <td><a href='{$CFG->wwwroot}/course/index.php?categoryid={$category->id}'>{$category->category}</a></td>
            <td>{$category->course_count}</td>
          </tr>";
}
echo "</table>";

echo $OUTPUT->footer();