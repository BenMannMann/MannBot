<?php 

use Discord\Builders\MessageBuilder;

/**
 * Sends a message to the channel with a list of available commands
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $currency			Name of the currency
 * @param string  $bot_image		URL of the bot's avatar
 */
function commandCommands($message_channel, $currency, $bot_image) {
	$message_channel->sendMessage("", false, [
		'title' => 'Commands',
		'author' => [
			'name' => 'MannBot',
			'icon_url' => $bot_image,
		],
		'color' => '3447003',
		'description' => "
			!hey -> Say Hi to MannBot!
			!commands -> Outputs a list of commands available
			!levelsleaderboard -> Check out the levels leaderboard

			-- Gambling Commands --
			!blackjack -> Play Blackjack against the Dealer MannBot
			!slots -> Play the completely-totally-not-rigged slots, 100 {$currency} a go!
			!double {amount} -> Try your luck at doubling your money!
			!steal {user} {amount} -> Try to steal some money from another user!
			!leaderboard -> Check out the gambling leaderboard
			!checkbalance -> Check your balance
			!claimbonus -> Receive some {$currency} every 6 hours
			!givemoney {user} {amount} -> Give another user some of your {$currency}
			!slotspayout -> Shows how much each you could win from the slots
			!jailtime -> Check how long you're in jail for

			-- Image Commands --
			!meme -> Generates a meme

			-- Music Commands --

			-- RNG Commands --
			!8ball {question} -> Ask the magical eight ball for a prediction
			!coinflip -> Flip a coin
			!parrot {user} -> Generates a message based on the users message history
			!rate {thing} -> Rate a given thing
			!roll -> Roll a 6 sided die
			!roll20 -> Roll a 20 sided die
			!thoughts {user|mannbot} -> Find out a given users (or MannBots!) thoughts
			!whois {what} -> Find out who is what
		",
		'thumbnail' => [
			'url' => $bot_image,
		],
		'footer' => [
			'text' => 'Last Updated: 19/09/2021',
			'icon_url' => $bot_image,
		],
	]);
}