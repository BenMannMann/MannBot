# MannBot
A Discord Bot

### Setup
1. Create your `keys.php` file

`$token` is your Discord Bot's token. You can find it within your bot appkication settings, under Bot -> Build-A-Bot -> Token
`$thoughts_folder` Here is the location to store MannBot's and users thoughts. Default is in the folder `MannBot/storage/thoughts`, which you will have to create
`$currency` The name you'd like to call the currency within the bot
`$bot_image` Image URL of the Bot
`$slot_icons` Icons to use for the slot machine. You can use normal or custom emojis for this by passing in '<:emojiname:emojiid>',
`$slot_winnings` How much the slot icons are worth 

2. Add the bot to your Discord server
This should generate the `users.json` file, which grabs all users within your server and assigns them defaults such as name, id, balance, xp and levels

### Commands
`!commands` - Generates an embed message showing all the commands 

### License
MIT License, © David Cole and other contributers 2016-present.
