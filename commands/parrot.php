<?php 

use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;

/**
 * Generates a message based on a past users messages
 */
function commandParrot($message_channel, $message_content) {

}

/**
 * Adds messages to the Parrot
 * 
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 */
function commandUpdateParrot($message_channel, $message_author, $logger) {
	// If the user isn't an Admin, don't do anything
	foreach ($message_author->roles as $role) {
		if ($role->permissions->administrator) {
	   		$message_builder = MessageBuilder::new()->setContent("Updating messages, this could take a while...");

	   		$message_channel->sendMessage($message_builder);
		 
			$message_channel->getMessageHistory(['limit' => 100])->done(function (Collection $collected_messages) use ($logger) {
				foreach ($collected_messages as $message) {
					$logger->debug('test', ['message' => $message]);
				}
			});
		}
	}
}

function addMessages() {

}

function acknowledgeParrotRequest() {

}

function formatMessage() {

}