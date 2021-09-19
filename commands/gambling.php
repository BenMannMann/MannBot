<?php 

use Discord\Builders\MessageBuilder;
use Carbon\Carbon;

/**
 * Play a game of blackjack
 */
function commandBlackjack() {

}

/**
 * Play a game of slots
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $currency			Name of the currency
 * @param array   $slot_icons		Icons to use in the slot machine
 * @param array   $slot_icons		How much each slot icon is worth
 */
function commandSlots($message_channel, $message_author, $currency, $slot_icons, $slot_winnings) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

	$slot_column_one = rand(1, 8);
	$slot_column_two = rand(1, 8);
	$slot_column_three = rand(1, 8);

	$slot_column_one_item = $slot_icons[$slot_column_one];
	$slot_column_two_item = $slot_icons[$slot_column_two];
	$slot_column_three_item = $slot_icons[$slot_column_three];

	// Let's see if they've won anything...
	if ( // All three icons match
		($slot_column_one == $slot_column_two) &&
		($slot_column_one == $slot_column_three) &&
		($slot_column_two == $slot_column_three)
	) {
		$winnings = $slot_winnings[$slot_column_one] * 2;

		$status = "won {$winnings} {$currency}!";
	} elseif ( // Two icons match
		($slot_column_one == $slot_column_two) ||
		($slot_column_one == $slot_column_three)
	) {
		$winnings = $slot_winnings[$slot_column_one];

		$status = "won {$winnings} {$currency}!";
	} elseif ($slot_column_two == $slot_column_three) {  // Two icons match
		$winnings = $slot_winnings[$slot_column_two];

		$status = "won {$winnings} {$currency}!";
	} else {
		// Nothing matched
		$winnings = 0;

		$status = 'lost';
	}

	foreach ($users as $user_record) {
		if ($user_record->id == $message_author->user->id) {
			// If they don't have enough balance, turn them away
			if ($user_record->balance < 100) {
				$message_builder = MessageBuilder::new()->setContent("You don't have enough {$currency} to play the slot machines, scram!");

				$message_channel->sendMessage($message_builder);
			} else {
				// Remove the initial slots cost
				$user_record->balance = $user_record->balance - 100; 

				$new_balance = $user_record->balance + $winnings; 

				$user_record->balance = $new_balance;

				file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

				$message_builder = MessageBuilder::new()->setContent("{$message_author} has placed 100 {$currency} into the slot machine and rolled... |{$slot_column_one_item} {$slot_column_two_item} {$slot_column_three_item}| and {$status}. Their new balance is {$new_balance}.");

				$message_channel->sendMessage($message_builder);
			}
		}
	}
}

/**
 * Play a game of doubling money
 * 
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string $message_content   Message that was passed through with the command
 * @param string  $currency			Name of the currency
 */
function commandDouble($message_channel, $message_author, $message_content, $currency) {
	$message = explode(' ', $message_content);

	$amount = $message[1];

	if (empty($amount)) {
		$message_builder = MessageBuilder::new()->setContent('Please enter how much you\'d like to attempt to double.');

		$message_channel->sendMessage($message_builder);

		return;
   	}

   	$roll = rand(1, 6);

   	$doubled = false;

   	if ($roll === 5 || $roll === 6) {
   		$doubled = true;
   	}

	$users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

	foreach ($users as $user_record) {
    	if ($user_record->id == $message_author->user->id) {
    		if ($amount === 'all') {
				$amount = $user_record->balance;
    		} else {
    			$amount = (integer) $amount;
    		}

    		if ($doubled) {
				$user_record->balance = $user_record->balance + $amount;

				$message_builder = MessageBuilder::new()->setContent("{$message_author} tried to double their money and succeeded! They gained {$amount} {$currency}!");
    		} else {
    			$user_record->balance = $user_record->balance - $amount;

				$message_builder = MessageBuilder::new()->setContent("{$message_author} tried to double their money and failed! They lost {$amount} {$currency}!");
    		}
    	}
	}

	file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

	$message_channel->sendMessage($message_builder);
}

/**
 * Try and steal another users balance
 */
function commandSteal() {

}

/**
 * Displays the current balance for a user
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $currency			Name of the currency
 */
function commandCheckBalance($message_channel, $message_author, $currency) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

	foreach ($users as $user) {
    	if ($user->id == $message_author->user->id) {
    		$balance = $user->balance;

    		break;
    	}
	}

	$message_builder = MessageBuilder::new()->setContent("{$message_author}, your balance is a whopping {$balance} {$currency}.");

	$message_channel->sendMessage($message_builder);
}


/**
 * Adds currency to a user. Admin Command
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $message_content  Message that was passed through with the command
 * @param string  $currency			Name of the currency
 */
function commandAddMoney($message_channel, $message_author, $message_content, $currency) {
	// If the user isn't an Admin, don't do anything
	foreach ($message_author->roles as $role) {
		if ($role->permissions->administrator) {
			$message = explode(' ', $message_content);

			$user = $message[1];

			$user_id = str_replace(['<', '>', '@', '!'], '', $user);

		   	$amount = (integer) $message[2];

		   	$added = false;

		   	if (empty($user)) {
				$message_builder = MessageBuilder::new()->setContent('Please enter who to give the currency to.');

				$message_channel->sendMessage($message_builder);

				return;
		   	}

		   	if (empty($amount)) {
				$message_builder = MessageBuilder::new()->setContent('Please enter how much to give to the user.');

				$message_channel->sendMessage($message_builder);

				return;
		   	} 

	    	$users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

			foreach ($users as $user_record) {
		    	if ($user_record->id == $user_id) {
	    			$user_record->balance = $user_record->balance + $amount;
	    			$added = true;
		    	}
			}

			if ($added) {
				file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

				$message_builder = MessageBuilder::new()->setContent("{$user} has been given {$amount} {$currency}.");
			} else {
				$message_builder = MessageBuilder::new()->setContent('Please enter a valid input.');
			}

			$message_channel->sendMessage($message_builder);
		}
	}
}

/**
 * Removes currency from a user. Admin Command
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $message_content  Message that was passed through with the command
 * @param string  $currency			Name of the currency
 */
function commandRemoveMoney($message_channel, $message_author, $message_content, $currency) {
	// If the user isn't an Admin, don't do anything
	foreach ($message_author->roles as $role) {
		if ($role->permissions->administrator) {
			$message = explode(' ', $message_content);

			$user = $message[1];

			$user_id = str_replace(['<', '>', '@', '!'], '', $user);

		   	$amount = (integer) $message[2];

		   	$added = false;

		   	if (empty($user)) {
				$message_builder = MessageBuilder::new()->setContent('Please enter who to remove the currency from.');

				$message_channel->sendMessage($message_builder);

				return;
		   	} 

		   	if (empty($amount)) {
				$message_builder = MessageBuilder::new()->setContent('Please enter how much to remove from the user.');

				$message_channel->sendMessage($message_builder);

				return;
		   	} 

	    	$users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

			foreach ($users as $user_record) {
		    	if ($user_record->id == $user_id) {
	    			$user_record->balance = $user_record->balance - $amount;
	    			$added = true;
		    	}
			}

			if ($added) {
				file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

				$message_builder = MessageBuilder::new()->setContent("{$user} has had {$amount} {$currency} removed.");
			} else {
				$message_builder = MessageBuilder::new()->setContent('Please enter a valid input.');
			}

			$message_channel->sendMessage($message_builder);
		}
	}
}

/**
 * Allows a user to send some balance to another user
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $message_content  Message that was passed through with the command
 * @param string  $currency			Name of the currency
 */
function commandGiveMoney($message_channel, $message_author, $message_content, $currency) {
	$message = explode(' ', $message_content);

	$user = $message[1];

	$user_id = str_replace(['<', '>', '@', '!'], '', $user);

   	$amount = (integer) $message[2];

   	$added = false;
   	$removed = false;

   	if (empty($user)) {
		$message_builder = MessageBuilder::new()->setContent('Please enter who to give the currency to.');

		$message_channel->sendMessage($message_builder);

		return;
   	} 

   	if (empty($amount)) {
		$message_builder = MessageBuilder::new()->setContent('Please enter how much to give to the user.');

		$message_channel->sendMessage($message_builder);

		return;
   	}

	$users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

	foreach ($users as $user_record) {
    	if ($user_record->id == $message_author->user->id) {
    		if ($user_record->balance < $amount) {
				$message_builder = MessageBuilder::new()->setContent("You do not have enough {$currency} to give away.");

				$message_channel->sendMessage($message_builder);
				
				return;
    		}
    	}
	}

	foreach ($users as $user_record) {
    	if ($user_record->id == $message_author->user->id) {
			$user_record->balance = $user_record->balance - $amount;

			$removed = true;
    	}

    	if ($user_record->id == $user_id) {
			$user_record->balance = $user_record->balance + $amount;
			
			$added = true;
    	}
	}

	if ($added & $removed) {
		file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

		$message_builder = MessageBuilder::new()->setContent("{$message_author} has given {$user} {$amount} {$currency}! How sweet");
	} else {
		$message_builder = MessageBuilder::new()->setContent('Please enter a valid input.');
	}
	
	$message_channel->sendMessage($message_builder);
}


/**
 * Allows a user to claim a bonus amount of currency evey 6 hours
 * 
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $message_author 	Author of the message
 * @param string  $currency			Name of the currency
 */
function commandClaimBonus($message_channel, $message_author, $currency) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

    foreach ($users as $user_record) {
    	if ($user_record->id == $message_author->user->id) {
			$last_claimed = Carbon::parse($user_record->daily_bonus);
			$now = Carbon::now();

			if ($last_claimed->addHours(6) > $now) {
				$message_builder = MessageBuilder::new()->setContent("You have already claimed your bonus {$currency}!");

				$message_channel->sendMessage($message_builder);	
			} else {
				$bonus = rand(100, 1000);

				$user_record->balance = $user_record->balance + $bonus;

				$user_record->daily_bonus = $now->timestamp;

				file_put_contents(dirname(__DIR__, 1) . '/users.json', json_encode($users));

				$message_builder = MessageBuilder::new()->setContent("{$message_author} has claimed {$bonus} bonus {$currency} !");

				$message_channel->sendMessage($message_builder);
			}
    	}
    }
}

/**
 * Displays the current leaderboard
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $currency			Name of the currency
 */
function commandLeaderboard($message_channel, $currency) {
    $users = json_decode(file_get_contents(dirname(__DIR__, 1) . '/users.json'));

    $sorted_users = array_sort($users, 'balance', SORT_DESC);

    $leaderboards = 'Balance Leaderboard:' . PHP_EOL;

    $count = 1;

    foreach ($sorted_users as $user) {
		$leaderboards .= "{$count}) {$user->username} - {$user->balance} {$currency}" . PHP_EOL;

		$count++;
    }

	$message_builder = MessageBuilder::new()->setContent($leaderboards);

	$message_channel->sendMessage($message_builder);
}

/**
 * Sort an array by a specific key and keeps indexes
 *
 * @param array  $array 	array of users
 * @param string $sort_by 	key to sort by
 * @param string $order 	sort the array in ascending or descending order
 */
function array_sort($array, $sort_by, $order = SORT_ASC) {
	$sortable_array = [];
	$sorted_array = [];

	if (count($array) === 0) {
		return [];
	}

	foreach ($array as $key => $value) {
		if (is_array((array) $value)) {
			foreach ((array) $value as $k => $v) {
				if ($k == $sort_by) {
					$sortable_array[$key] = $v;
				}
			}
		} else {
			$sortable_array[$key] = $value;
		}
	}

	if ($order === SORT_ASC) {
		asort($sortable_array);
	} elseif ($order === SORT_DESC) {
		arsort($sortable_array);
	}

	foreach ($sortable_array as $key => $value) {
		$sorted_array[$key] = $array[$key];
	}

	return $sorted_array;
}

/**
 * Outputs the prize money for certain slots combinations 
 *
 * @param string  $message_channel 	Channel to send the message to
 * @param string  $currency			Name of the currency
 * @param array   $slot_icons		Icons to use in the slot machine
 * @param array   $slot_icons		How much each slot icon is worth
 */
function commandSlotsPayout($message_channel, $currency, $slot_icons, $slot_winnings) {
	$slots_payout = 'Slots Payout:' . PHP_EOL;

	foreach (array_combine($slot_icons, $slot_winnings) as $key => $value) {
		$slots_payout .= "{$key} {$key} = {$value} {$currency}" . PHP_EOL;
		$double_value = $value * 2;
		$slots_payout .= "{$key} {$key} {$key} = {$double_value} {$currency}" . PHP_EOL;
	}

	$message_builder = MessageBuilder::new()->setContent($slots_payout);

	$message_channel->sendMessage($message_builder);
}
