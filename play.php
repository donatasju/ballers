<?php
require_once 'bootstrap.php';

session_start();

function get_players_names($team_idx, $player_idx) {
    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);
        $team_players = &$teams_array[$team_idx]['players'];
        $players_nick_list = array_column($team_players, 'nick_name');
        return $players_nick_list;
    }

    return [];
}

function form_success($safe_input, $form) {
    $team_idx = $_SESSION['team'] ?? false;
    $nick = $_SESSION['nick'] ?? false;
    $player_ind = $_SESSION['player_idx'] ?? false;

    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);

        $teams_array[$team_idx]['ball_handler'] = $safe_input['ball_handler'];
        $teams_array[$team_idx]['players'][$player_ind]['score'] ++;

        return array_to_file($teams_array, STORAGE_FILE);
    }
}

function validate_kick($field_input, &$field, $input) {
    $team_idx = $_SESSION['team'] ?? false;
    $nick = $_SESSION['nick'] ?? false;
    $player_ind = $_SESSION['player_idx'] ?? false;

    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);
        $team = &$teams_array[$team_idx];
        $ball_handler = $team['ball_handler'];
        if ($ball_handler == null || $ball_handler == $player_ind) {
            return true;
        } else {
            $field['error_msg'] = strtr('Negali pasuoti, nes @ball_handler turi kamuoli', [
                '@ball_handler' => $team['players'][$ball_handler]['nick_name']
            ]);
        }
    }

    return false;
}

function check_player($team_idx, $player_idx) {
    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);
        return $teams_array[$team_idx]['players'][$player_idx] ?? false;
    }

    return false;
}

$team_idx = $_SESSION['team'] ?? false;
$player_idx = $_SESSION['player_idx'] ?? false;

$form = [
    'fields' => [
        'ball_handler' => [
            'label' => 'Kam pasuoji?',
            'type' => 'select',
            'options' => get_players_names($team_idx, $player_idx),
            'validate' => [
                'validate_not_empty',
                'validate_kick'
            ]
        ]
    ],
    'buttons' => [
        'submit' => [
            'text' => 'Kick the ball!'
        ]
    ],
    'callbacks' => [
        'success' => [
            'form_success'
        ],
        'fail' => []
    ]
];

$show_form = false;
$valid_player = false;
$message = '';

if (!empty($_SESSION)) {
    if ($player_idx !== false && $team_idx !== false) {
        $valid_player = check_player($team_idx, $player_idx);
    }
}

if ($valid_player) {
    $show_form = true;

    if (!empty($_POST)) {
        $safe_input = get_safe_input($form);
        $form_success = validate_form($safe_input, $form);
    }
} else {
    header("Location: join.php");
    exit();
}
?>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>PZ2ABALL | Play</title>
    </head>
    <body>
        <!-- Navigation -->    
<?php require 'objects/navigation.php'; ?>        

        <!-- Content -->       
        <h1>Išbėk į aikštelę</h1>
<?php if ($show_form): ?>
            <!-- Form -->        
            <?php require 'objects/form.php'; ?>
        <?php else: ?>
            <h2>Zašibys!</h2>
            <h3><?php print $message; ?></h3>
        <?php endif; ?>
    </body>
</html>