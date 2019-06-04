<?php

require_once 'bootstrap.php';

function form_success($safe_input, $form) {
    $team = [
        'team_name' => $safe_input['team_name'],
        'players' => [],
        'ball_handler' => null
    ];

    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);
        $teams_array[] = $team;
    } else {
        $teams_array = [$team];
    }

    return array_to_file($teams_array, STORAGE_FILE);
}

function validate_team_name($field_input, &$field, $form_input) {
    if (file_exists(STORAGE_FILE)) {
        $teams_array = file_to_array(STORAGE_FILE);

        foreach ($teams_array as $team) {
            if ($team['team_name'] == $field_input) {
                $field['error_msg'] = strtr('Ballers team pavadinimu '
                        . '"@team" jau egzistuoja!', [
                    '@team' => $field_input
                ]);
                return false;
            }
        }
    }

    return true;
}

$form = [
    'fields' => [
        'team_name' => [
            'label' => 'Create team',
            'type' => 'text',
            'placeholder' => 'Team name',
            'validate' => [
                'validate_not_empty',
                'validate_team_name'
            ]
        ],
    ],
    'buttons' => [
        'submit' => [
            'text' => 'Create!'
        ]
    ],
    'callbacks' => [
        'success' => [
            'form_success'
        ],
        'fail' => []
    ]
];

$show_form = true;

if (!empty($_POST)) {
    $safe_input = get_safe_input($form);
    $form_success = validate_form($safe_input, $form);

    if ($form_success) {
        $success_msg = strtr('Team`as "@team_name" sėkmingai sukurtas!', [
            '@team_name' => $safe_input['team_name']
        ]);
        $show_form = false;
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>PZ2ABALL | Create Team</title>
    </head>
    <body>
        <!-- Navigation -->    
        <?php require 'objects/navigation.php'; ?>        

        <!-- Content -->    
        <h1>Create a BALLERS team!</h1>

        <?php if ($show_form): ?>
            <!-- Form -->        
            <?php require 'objects/form.php'; ?>
        <?php else: ?>
            <h2>Zasibys!</h2>
            <h3><?php print $success_msg; ?></h3>
        <?php endif; ?>
    </body>
</html>