<?php 

use Discord\Builders\MessageBuilder;

/**
 * Sets a user's alias
 *
 * @param string  $message_channel	Channel to send the message to
 * @param string  $message_author	Author of the message
 * @param string  $message_content	Message that was passed through with the command
 */
function commandSetAlias($message_channel, $message_author, $message_content) {
	// If the user isn't an Admin, don't do anything
	foreach ($message_author->roles as $role) {
		if ($role->permissions->administrator) {
		    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));
			
			$message = explode(' ', $message_content);

			$user = $message[1];

			$user_id = str_replace(['<', '>', '@', '!'], '', $user);

		   	$alias = $message[2];

			foreach ($users as $user_record) {
				if ($user_record->id == $user_id) {
					$user_record->alias = $alias;
				}
			}

			file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

			$message_builder = MessageBuilder::new()->setContent("{$user} has been given the alias {$alias}.");

			$message_channel->sendMessage($message_builder);
		}
	}
}
