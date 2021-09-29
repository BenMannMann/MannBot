<?php

$token = ''; // Discord Bot Token

$thoughts_folder = __DIR__ . '/storage/thoughts'; // Filepath to the thoughts folder (You can freely change this location)

$currency = ''; // The name to use for the currency for the bot

$bot_image = ''; // Image URL of your Bot's Avatar

$levels = [ // Level XP Thresholds
	1  => 0,
	2  => 25,
	3  => 50,
	4  => 75,
	5  => 125,
	6  => 175,
	7  => 225,
	8  => 275,
	9  => 330,
	10 => 390,
]; 

$slot_icons = [ // Emojis to use as slot icons
	1 => '',
	2 => '',
	3 => '',
	4 => '',
	5 => '',
	6 => '',
	7 => '',
	8 => '',
];

$slot_winnings = [ // How much each slot icon is worth
	1 => 125,
	2 => 150,
	3 => 250,
	4 => 400,
	5 => 550,
	6 => 700,
	7 => 850,
	8 => 1000,
];

$meme_reactions = [ // Emoji's that the bot will use when acknowledging a !meme request. 
	1 => '',
	2 => '',
	3 => '',
	4 => '',
	5 => '',
	6 => '',
	7 => '',
	8 => '',
];
