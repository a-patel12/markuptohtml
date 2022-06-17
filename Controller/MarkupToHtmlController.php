<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Apatel\MailChimp\MarkupToHtml;

/* Converts markup to HTML for header tags, links and unformatted text. <h1>..<h6>, <p>, <a>
 *
 * */

$text_array =["## Header second
[First link](https://www.mailchimp.com) then some text.","# Sample Document

Hello!

This is sample markdown for the [Mailchimp](https://www.mailchimp.com) homework assignment.",
    "# Header one

Hello there

How are you?
What's going on?

## Another Header

This is a paragraph [with an inline link](http://google.com). Neat, eh?

## This is a header [with a link](http://yahoo.com)",
    "" // empty text
];

$converter = new MarkupToHtml();
try{

    foreach($text_array as $text_to_convert){

        if($text_to_convert == ""){
            throw new Exception("Text to convert must not be empty");
        }

        $converted_text = $converter->convertToHtml($text_to_convert);
        if(isset($converted_text['error'])){
            throw new Exception($converted_text['error']);

        }
        echo $converted_text['htmltext'].PHP_EOL;

    }

}catch (Exception $exception){
    echo $exception->getMessage();
}
