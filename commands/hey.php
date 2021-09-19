<?php

use Discord\Builders\MessageBuilder;

/**
 * Say Hello to MannBot!
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 */
function commandHey($message_channel, $message_author) {
	$message_builder = MessageBuilder::new()->setContent("Hi {$message_author}");

	$message_channel->sendMessage($message_builder);
}
