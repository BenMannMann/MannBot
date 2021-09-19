<?php 

use Discord\Builders\MessageBuilder;

/**
 * Adds xp to a user
 * 
 * @param string  $message_channel  Channel to send the message to
 * @param string  $message_author   Author of the message
 * @param array   $levels           Array of levels and their corresponding XP
 */
function addXp($message_channel, $message_author, $levels) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

    foreach ($users as $user_record) {
        if ($user_record->id == $message_author->user->id) {
            $user_record->xp++;

            // See if the user has achieved the xp threshold for the next level            
            foreach ($levels as $level => $xp) {
                if ($user_record->xp === $xp) {
                    $user_record->level++;

                    $message_builder = MessageBuilder::new()->setContent("Congratulations {$message_author}, You have leveled up to level {$user_record->level}!");

                    $message_channel->sendMessage($message_builder);

                    break;
                }
            }

        }
    }

    file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));
}

/**
 * Displays the current level leaderboard
 * 
 * @param string  $message_channel  Channel to send the message to
 */
function commandLevelsLeaderboard($message_channel) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

    $sorted_users = array_sort($users, 'xp', SORT_DESC);

    $leaderboards = 'Levels Leaderboard:' . PHP_EOL;
    
    $count = 1;

    foreach ($sorted_users as $user) {
        $leaderboards .= "{$count}) {$user->username} - Level {$user->level} - {$user->xp} xp" . PHP_EOL;

        $count++;
    }

    $message_builder = MessageBuilder::new()->setContent($leaderboards);

    $message_channel->sendMessage($message_builder);
}
