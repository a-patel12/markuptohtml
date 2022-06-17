<?php

use Apatel\MailChimp\MarkupToHtml;
use PHPUnit\Framework\TestCase;

class MarkupToHtmlTest extends TestCase
{

    private $markup_html;

    protected function setUp(): void
    {
        $this->markup_html = new MarkupToHtml();
    }

    // dataprovider: input, expected
    public function textDataProvider()
    {
        return [
            ['# Sample Document
Hello!

This is sample markdown for the [Mailchimp](https://www.mailchimp.com) homework assignment.', '<h1>Sample Document</h1>
<p>Hello!</p>
<p>This is sample markdown for the <a href="https://www.mailchimp.com">Mailchimp</a> homework assignment.</p>',
            ],
            ['## Header second
[First link](https://www.mailchimp.com) then some text.','<h2>Header second</h2>
<a href="https://www.mailchimp.com">First link</a> then some text.'
            ],

        ];
    }

    /**
     * @dataProvider textDataProvider
     */
    public function testReturnsHtml($text, $expected)
    {
        $result = $this->markup_html->convertToHtml($text);
        $this->assertEquals($expected, $result['htmltext']);
    }

}