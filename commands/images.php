<?php

use Discord\Builders\MessageBuilder;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Generates a meme with a random template and image
 * 
 * @param Message $message              Message object that was sent by the user
 * @param string  $meme_reactions       Reactions the bot can use to acknowledge a !meme command
 */
function commandMeme($message, $meme_reactions) {
    // Add a reaction to the message to acknowledge a meme generation request
    $reaction = rand(1, 11);

    $message->react($meme_reactions[$reaction]);

    // Grab our templates and choose one at random
    $template_counter = count(glob(dirname(__DIR__, 1) . '/storage/meme_generator/templates/*')) - 1; // -1 as the file names start from 0
    $source_images_counter = count(glob(dirname(__DIR__, 1) . '/storage/meme_generator/source_images/*')) - 1; // -1 as the file names start from 0

    $template_rand = rand(0, $template_counter);

    $template = dirname(__DIR__, 1) . '/storage/meme_generator/templates/' . $template_rand . '.png';

    // Load the template
    Image::configure(['driver' => 'imagick']);

    $image = Image::make($template);

    // Grab info of the template from sizes.json
    $sizes = json_decode(file_get_contents(dirname(__DIR__, 1) . '/storage/meme_generator/sizes.json'));

    $template_details = $sizes[$template_rand];

    // Grab the amount of memes needed to fill the template
    $memes = [];
    $meme_name = 'meme';
    $memes_chosen = [];

    foreach ($template_details->boxes as $box) {
        // We can safely assume that $meme_chosen has a random number in it as repeat_previous doesn't get set on the first array record of a template
        if (!empty($box[0]->repeat_previous)) {
            $filepath = glob(dirname(__DIR__, 1) . '/storage/meme_generator/source_images/' . $meme_chosen . '.*');

            $memes[] = [
                'meme_id' => $meme_chosen,
                'path' => $filepath[0],
                'width' => $box[0]->width,
                'height' => $box[0]->height,
                'top' => $box[0]->top,
                'left' => $box[0]->left,
            ];

            continue;
        }

        $meme_chosen = rand(0, $source_images_counter);

        // If the meme has already been used, reroll
        if (in_array($meme_chosen, $memes_chosen)) {
            while (in_array($meme_chosen, $memes_chosen)) {
                $meme_chosen = rand(0, $source_images_counter);
            }
        }

        $filepath = glob(dirname(__DIR__, 1) . '/storage/meme_generator/source_images/' . $meme_chosen . '.*');

        $memes[] = [
            'meme_id' => $meme_chosen,
            'path' => $filepath[0],
            'width' => $box[0]->width,
            'height' => $box[0]->height,
            'top' => $box[0]->top,
            'left' => $box[0]->left,
        ];

        $memes_chosen[] = $meme_chosen;
    }

    // Generate the meme
    foreach ($memes as $meme) {
        $meme_image = Image::make($meme['path']);

        $meme_image->orientate();

        $meme_image->resize($meme['width'], $meme['height'], function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->insert($meme_image, 'top-left', $meme['left'], $meme['top']);

        $meme_name = $meme_name . '-' . $meme['meme_id'];
    }

    $image->save(dirname(__DIR__, 1) . '/storage/meme_generator/generated_memes/' . $meme_name . '.jpg');

    // Output the meme
    $message_builder = MessageBuilder::new()->addFile(dirname(__DIR__, 1) . '/storage/meme_generator/generated_memes/' . $meme_name . '.jpg');

    $message->channel->sendMessage($message_builder);
}
