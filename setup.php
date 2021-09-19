<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/keys.php';

foreach(glob(__DIR__ . '/commands/*.php') as $filename) {
    require $filename;
}

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$discord = new Discord([
    'token' => $token,
    'loadAllMembers' => true,
    'intents' => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS,
]);

$logger = new Logger('MannBot Logs');
$logger->pushHandler(new StreamHandler(__DIR__ . '/application.log', Logger::DEBUG));

$discord->on('ready', function ($discord) use ($logger, $thoughts_folder, $levels, $currency, $bot_image, $slot_icons, $slot_winnings) {
    $logger->info('MannBot Online');

    // If no user data exists, generate a new users json file
    if (!file_get_contents(__DIR__ . '/users.json')) {
        foreach ($discord->guilds as $guild) {

            $users = [];

            foreach ($guild->members as $member) {
                if ($member->user->bot) {
                    continue;
                }

                $users[] =  [
                    'id' => $member->user->id,
                    'username' => $member->user->username,
                    'nickname' => $member->nick,
                    'alias' => null,
                    'xp' => 0,
                    'level' => 1,
                    'balance' => 1000,
                    'daily_bonus' => strtotime('now'),
                ];
            }

            file_put_contents(__DIR__ . '/users.json', json_encode($users));
        }
    }

    /* @todo test */
    // If the bot is added to a new server, grab all the users in that server and generate data for them
    // $discord->on(Event::GUILD_CREATE, function (Guild $guild, Discord $discord) {
    //     $users = json_decode(file_get_contents(__DIR__ . '/users.json'));

    //     foreach ($guild->members as $member) {
    //         // If this is a bot, skip the member
    //         if ($member->user->bot) {
    //             continue;
    //         }

    //         // If they already exist within the user data the bot holds, skip the member
    //         foreach ($users as $user) {
    //             if ($user->id == $member->user->id) {
    //                 continue 2;
    //             }
    //         }

    //         $users[] =  [
    //             'id' => $member->user->id,
    //             'username' => $member->user->username,
    //             'nickname' => $member->user->nick,
    //             'alias' => null,
    //             'xp' => 0,
    //             'level' => 1,
    //             'balance' => 1000,
    //             'daily_bonus' => strtotime('now'),
    //         ];
    //     }

    //     file_put_contents(__DIR__ . '/users.json', json_encode($users));
    // });
    

    /* @todo test */
    // If a new user joins the server, add them to the users data file
    // $discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
    //     if (!$member->user->bot) {
    //         $users = json_decode(file_get_contents(__DIR__ . '/users.json'));


    //         // If they already exist within the user data the bot holds, skip the member
    //         foreach ($users as $user) {
    //             if ($user->id == $member->user->id) {
    //                 $logger->info('A new user has joined the server');
                    
    //                 break;
    //             }
    //         }

    //         $users[] = [
    //             'id' => $member->user->id,
    //             'username' => $member->user->username,
    //             'nickname' => $member->user->nick,
    //             'alias' => null,
    //             'xp' => 0,
    //             'level' => 1,
    //             'balance' => 1000,
    //             'daily_bonus' => strtotime('now'),
    //         ];

    //         file_put_contents(__DIR__ . '/users.json', json_encode($users));

    //         $logger->info('A new user has joined the server');
    //     }
    // });

    // Listen for messages.
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($logger, $thoughts_folder, $levels, $currency, $bot_image, $slot_icons, $slot_winnings) {
        // Add 1xp to the user every time they message
        addXp($message->channel, $message->author, $levels);

        // See if a command was called
        $command = strtok(strtolower($message->content), ' ');

        if (substr($command, 0, 1) == '!') {
            switch ($command) {
                case '!commands':
                    commandCommands($message->channel, $currency, $bot_image);
                    break;
                case '!hey':
                    commandHey($message->channel, $message->author);
                    break;
                case '!levelsleaderboard':
                    commandLevelsLeaderboard($message->channel);
                    break;

                // Admin Commands
                case '!setalias':
                    commandSetAlias($message->channel, $message->author, $message->content);
                    break;

                // Gambling Commands
                // case '!blackjack':
                //     commandBlackjack($message->channel, $message->author, $currency);
                //     break;
                case '!double':
                    commandDouble($message->channel, $message->author, $message->content, $currency);
                    break;
                case '!slots':
                    commandSlots($message->channel, $message->author, $currency, $slot_icons, $slot_winnings);
                    break;
                case '!steal':
                    commandSteal($message->channel, $message->author, $message->content, $currency);
                    break;
                case '!leaderboard':
                    commandLeaderboard($message->channel, $currency);
                    break;
                case '!checkbalance':
                    commandCheckBalance($message->channel, $message->author, $currency);
                    break;
                case '!claimbonus':
                    commandClaimBonus($message->channel, $message->author, $currency);
                    break;
                case '!slotspayout':
                    commandSlotsPayout($message->channel, $currency, $slot_icons, $slot_winnings);
                    break;   
                case '!givemoney':
                    commandGiveMoney($message->channel, $message->author, $message->content, $currency);
                    break;
                case '!addmoney':
                    commandAddMoney($message->channel, $message->author, $message->content, $currency);
                    break;
                case '!removemoney':
                    commandRemoveMoney($message->channel, $message->author, $message->content, $currency);
                    break;

                // Images Commands    
                // case '!meme':
                //     commandMeme($message->channel, $message);
                //     break;

                // Music Commands

                // Parrot Commands
                // case '!parrot':
                //     commandParrot($message->channel, $message);
                //     break;
                // case '!updateparrot':
                //     commandUpdateParrot($message->channel, $message->author, $logger);
                //     break;    

                // RNG Commands
                case '!8ball':
                    commandEightBall($message->channel);
                    break;
                case '!coinflip':
                    commandCoinFlip($message->channel);
                    break;
                case '!rate':
                    commandRate($message->channel);
                    break;
                case '!roll':
                    commandRoll($message->channel);
                    break;
                case '!roll20':
                    commandRollTwenty($message->channel);
                    break;
                case '!thoughts':
                    commandThoughts($message->channel, $thoughts_folder, $message->content);
                    break;
                case '!whois':
                    commandWhoIs($message->channel, $message->guild->members, $message->content);
                    break;
            }
        }
    });

    // @todo Update the saved users record upon any updates E.G. nickname changes
    // $discord->on(Event::GUILD_MEMBER_UPDATE, function (Member $new_member_record, Discord $discord, $old_member_record) {
        
    // });
});

$discord->run();
