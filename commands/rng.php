<?php

use Discord\Builders\MessageBuilder;

/**
 * Flips a coin
 *
 * @param string $message_channel   Channel to send the message to
 */
function commandCoinFlip($message_channel) {
   $flip = rand(1, 500000);

   if ($flip % 2) {
      $outcome = 'Heads';
   } else {
      $outcome = 'Tails';
   }

   $message_builder = MessageBuilder::new()->setContent($outcome);

   $message_channel->sendMessage($message_builder);
}

/**
 * Ask the Magical 8 Ball for a prediction
 *
 * @param string $message_channel   Channel to send the message to
 */
function commandEightBall($message_channel) {
   $predictions = [
      1 => 'As I see it, yes',
      2 => 'Ask again later',
      3 => 'Better not tell you now',
      4 => 'Cannot predict now',
      5 => 'Concentrate and ask again',
      6 => 'Don’t count on it',
      7 => 'It is certain',
      8 => 'It is decidedly so',
      9 => 'Most likely',
      10 => 'My reply is no',
      11 => 'My sources say no',
      12 => 'Outlook not so good',
      13 => 'Outlook good',
      14 => 'Reply hazy, try again',
      15 => 'Signs point to yes',
      16 => 'Very doubtful',
      17 => 'Without a doubt',
      18 => 'Yes',
      19 => 'Yes, definitely',
      20 => 'You may rely on it',
   ];

   $random = rand(1, 20);

   $message_builder = MessageBuilder::new()->setContent($predictions[$random]);

   $message_channel->sendMessage($message_builder);
}

/**
 * Rate something
 *
 * @param string $message_channel   Channel to send the message to
 */
function commandRate($message_channel) {
   $rate = rand(-1, 101);

   $message_builder = MessageBuilder::new()->setContent("I'd give that a {$rate}/100");

   $message_channel->sendMessage($message_builder);
}

/**
 * Roll a 6 sided dice
 *
 * @param string $message_channel   Channel to send the message to
 */
function commandRoll($message_channel) {
   $roll = rand(1, 6);

   $message_builder = MessageBuilder::new()->setContent($roll);

   $message_channel->sendMessage($message_builder);   
}

/**
 * Roll a 20 sided dice
 *
 * @param string $message_channel   Channel to send the message to
 */
function commandRollTwenty($message_channel) {
   $roll = rand(1, 20);

   $message_builder = MessageBuilder::new()->setContent($roll);

   $message_channel->sendMessage($message_builder);
}

/**
 * Grab the thoughts of a user (or MannBot!)
 *
 * @param string $message_channel   Channel to send the message to
 * @param string $thoughts_folder   Location of the thoughts file
 * @param string $message_content   Message that was passed through with the command
 */
function commandThoughts($message_channel, $thoughts_folder, $message_content) {
   $person = strtok(str_replace('!thoughts', '', strtolower($message_content)), ' ');

   if ($person == 'mannbot' || empty($person)) {
      $thoughts_file = $thoughts_folder . '/' . 'thoughts.txt';
   } elseif (!empty($person)) {
      $thoughts_file = $thoughts_folder . '/' . $person . '.txt';
   }

   $thoughts = file($thoughts_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

   if (!empty($thoughts)) {
      $selected_thought = array_rand($thoughts);

      if (!empty($person)) {
         $message_builder = MessageBuilder::new()->setContent(ucfirst($person) . "'s thoughts: {$thoughts[$selected_thought]}");
      } else {
         $message_builder = MessageBuilder::new()->setContent("MannBot's thoughts: {$thoughts[$selected_thought]}");
      }

      $message_channel->sendMessage($message_builder);
   } else { // default to MannBot's Thoughts
      $thoughts = file($thoughts_folder . '/' . 'thoughts.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

      $selected_thought = array_rand($thoughts);

      $message_builder = MessageBuilder::new()->setContent("MannBot's thoughts: {$thoughts[$selected_thought]}");

      $message_channel->sendMessage($message_builder);
   }
}

/**
 * Find out who is what
 *
 * @param string $message_channel   Channel to send the message to
 * @param string $users             List of users within the server
 * @param string $message_content   Message that was passed through with the command
 */
function commandWhoIs($message_channel, $users, $message_content) {
   foreach ($users as $user) {
      if ($user->nick !== null) {
         $people[] = $user->nick;
      } else {
         $people[] = $user->username;
      }
   }

   $selected_person = array_rand($people);

   $message_builder = MessageBuilder::new()->setContent("{$people[$selected_person]} is " . str_replace('!whois ', '', $message_content));

   $message_channel->sendMessage($message_builder);
}
