<?php
require_once 'bootstrap.php';

session_start();

function team_scores() {
    $teams = file_to_array(STORAGE_FILE);

    foreach ($teams as &$team) {
        $team['total_score'] = 0;

        foreach ($team['players'] as $player) {
            $team['total_score'] += $player['score'];
        }
    }

    return $teams;
}

function get_player($team_idx, $nick) {
    $teams = file_to_array(STORAGE_FILE);
    $team = $teams[$team_idx] ?? false;

    if ($team) {
        foreach ($team['players'] as $player) {
            if ($nick == $player['nick_name']) {
                return $player;
            }
        }
    }

    return null;
}

$scoreboard = team_scores();

$nick = $_SESSION['nick'] ?? false;
$team_idx = $_SESSION['team'] ?? false;

if ($nick && $team_idx !== false) {
    $player_stats = get_player($team_idx, $nick);
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
        <h1>BALLERS Scoreboard!</h1>
        <table>
            <tr>
                <th>Team</th>
                <th>Score</th>
            </tr>
            <?php foreach ($scoreboard as $team): ?>
                <tr>
                    <td><?php print $team['team_name']; ?></td>
                    <td><?php print $team['total_score']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (isset($player_stats)): ?>
            <table>
                <tr>
                    <th>Nickname</th>
                    <th>Score</th>
                </tr>
                <tr>
                    <td><?php print $player_stats['nick_name']; ?></td>
                    <td><?php print $player_stats['score']; ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </body>
</html>