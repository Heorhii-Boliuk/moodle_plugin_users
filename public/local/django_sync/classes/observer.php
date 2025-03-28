<?php
defined('MOODLE_INTERNAL') || die();

class local_django_sync_observer {
    public static function grade_updated(\core\event\grade_item_updated $event) {
        global $DB;

        $grade = $DB->get_record('grade_grades', ['id' => $event->objectid]);
        $user = $DB->get_record('user', ['id' => $grade->userid]);
        $course = $DB->get_record('course', ['id' => $event->courseid]);

        if (!$grade || !$user || !$course) {
            return;
        }

        $gradebook_id = self::get_gradebook_id($course->id);
        if (!$gradebook_id) {
            return;
        }

        $gradebook_cell = self::get_gradebook_cell($gradebook_id);
        if (!$gradebook_cell) {
            return;
        }

        if ($gradebook_cell['student_gradebook']['student']['id'] !== (string)$user->id) {
            return;
        }

        self::update_gradebook_cell($gradebook_cell['id'], $grade->finalgrade);
    }

    private static function get_gradebook_id($course_id) {
        // @todo find the way how to get gradebook ID in KSU24 using Moodle course id
        return "3fa85f64-5717-4562-b3fc-2c963f66afa6";  // Example of returning the course id
    }

    private static function get_gradebook_cell($gradebook_id) {
        $url = "https://ksu24.kspu.edu/api/gradebook/cell/{$gradebook_id}/";
        $response = self::make_request($url, 'GET');

        return $response ? json_decode($response, true) : null;
    }

    private static function update_gradebook_cell($cell_id, $new_grade) {
        $url = "https://django-site/api/gradebook/cell/{$cell_id}/";
        $data = json_encode(['total_mark' => $new_grade]);

        self::make_request($url, 'PATCH', $data);
    }

    private static function make_request($url, $method, $data = null) {
        /**
         * @todo need to add token or get it from server.
         */
        $jwt_token = 'your_django_api_token';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $jwt_token",
            "Accept: application/json",
            "Content-Type: application/json"
        ]);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
