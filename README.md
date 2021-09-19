# MannBot
A Discord Bot

## Setup
### Create your `keys.php` file

- `$token` is your Discord bot's token. You can find it within your Discord bot application settings, under Bot -> Build-A-Bot -> Token.
- `$thoughts_folder` Here is the location to store MannBots and your users thoughts.
- `$currency` The name you'd like to call the currency within the bot.
- `$bot_image` Image URL of the bot.
- `$slot_icons` Icons to use for the slot machine. You can use normal or custom emojis for this by passing in '<:emojiname:emojiid>'.
- `$slot_winnings` How much the individual slot icons are worth.

### Add the bot to your Discord server
This should generate the `users.json` file, which grabs all users within your server and assigns them defaults such as name, id, balance, xp and levels

## Commands
`!commands` - Generates an embed message showing all the commands
