<?php
defined('MOODLE_INTERNAL') || die();

class local_myplugin_observer {
    public static function grade_updated(\core\event\grade_item_created $event) {
        global $DB;

        $grade = $DB->get_record('grade_grades', ['id' => $event->objectid]);
        $user = $DB->get_record('user', ['id' => $grade->userid]);
        $course = $DB->get_record('course', ['id' => $event->courseid]);

        $data = [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'grade' => $grade->finalgrade,
        ];

        $ch = curl_init('https://django-site/api/update_grade/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}
