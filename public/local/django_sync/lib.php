<?php
global $CFG;
require_once($CFG->dirroot . '/user/externallib.php');

function sync_users() {
    $moodle_users = get_moodle_users();
    $django_users = get_django_users();

    $moodle_emails = array_column($moodle_users, 'email');
    $django_emails = array_column($django_users, 'email');

    $common_users = [];
    $only_in_moodle = [];
    $only_in_django = [];

    foreach ($django_users as $django_user) {
        if (in_array($django_user['email'], $moodle_emails)) {
            $common_users[] = $django_user;
        } else {
            $only_in_django[] = $django_user;
        }
    }

    foreach ($moodle_users as $moodle_user) {
        if (!in_array($moodle_user['email'], $django_emails)) {
            $only_in_moodle[] = $moodle_user;
        }
    }

    return [$only_in_moodle, $only_in_django, $common_users];
}

function create_moodle_user($django_user) {
    $new_user = [
        'username' => $django_user['username'],
        'password' => 'TempPassword123!',
        'firstname' => $django_user['first_name'],
        'lastname' => $django_user['last_name'],
        'email' => $django_user['email'],
        'auth' => 'manual',
        'preferences' => [
            [
                'type' => 'auth_forcepasswordchange',
                'value' => '1',
            ],
        ],
        'customfields' => [
            [
                'type' => 'edeboid',
                'value' => $django_user['edeboid'],
            ],
            [
                'type' => 'iasid',
                'value' => $django_user['iasid'],
            ],
            [
                'type' => 'djangoid',
                'value' => $django_user['id'],
            ],
        ],
    ];

    try {
        $users = [$new_user];

        $result = core_user_external::create_users($users);

        if (isset($result['exception'])) {
            throw new Exception('Error creating user: ' . $result['message']);
        } else {
            echo "User created successfully in Moodle with email: " . $django_user['email'];
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function create_django_user($moodle_user) {
    $url = "https://ksu24.kspu.edu/api/user/";
    /**
     * @todo need to add token.
     */
    $api_token = 'your_django_api_token';

    $data = [
        "username" => $moodle_user['username'],
        "password" => 'TempPassword123!',
        "email" => $moodle_user['email'],
        "first_name" => $moodle_user['firstname'],
        "last_name" => $moodle_user['lastname']
    ];

    $json_data = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Token $api_token",
        "Accept: application/json",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 201) {
        echo "User Successfully created in Django!";
    } else {
        echo "Error creating user! Response Django: $response";
    }
}

function get_moodle_users() {
    return core_user_external::get_users()['users'];
}

function get_django_users() {
    $django_api_url = 'https://ksu24.kspu.edu/api/user/';
    /**
     * @todo need to add token.
     */
    $api_token = 'your_django_api_token';

    $all_users = [];
    $next_page = $django_api_url;

    do {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $next_page);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Token $api_token",
            "Accept: application/json",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['results']) && is_array($data['results'])) {
            $all_users = array_merge($all_users, $data['results']);
        }

        $next_page = $data['next'] ?? null;

    } while ($next_page);

    return $all_users;
}

function batch_update_moodle_users() {
    global $DB;

    $moodle_users = $DB->get_records_sql("SELECT id, email, edeboid, iasid, djangoid FROM {user}");
    $django_users = get_django_users();

    $updated = 0;

    foreach ($moodle_users as $m_user) {
        if (!empty($m_user->edeboid) && !empty($m_user->iasid) && !empty($m_user->djangoid)) {
            continue;
        }
        foreach ($django_users as $d_user) {
            if ($m_user->email === $d_user['email']) {
                $DB->execute(
                    "UPDATE {user} SET edeboid = ?, iasid = ?, djangoid = ? WHERE id = ?",
                    [$d_user['edeboid'], $d_user['iasid'], $d_user['id'], $m_user->id]
                );
                $updated++;
            }
        }
    }

    return $updated;
}

