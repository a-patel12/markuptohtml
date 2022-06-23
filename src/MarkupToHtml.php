<?php
namespace Apatel\MailChimp;

class MarkupToHtml{

    /**
     * @var string[] predefined tag types to process
     */
    private $tagTypes = [
        '#' => 'Header',
        '[' => 'Link',
        ']' => 'Link',
    ];

    /**
     * @param $text
     * @return array|string|string[]
     */
    public function convertToHtml($text){

        $textLines = $this->textLines($text); // creates lines array
        return $this->convertToTags($textLines);
    }

    /**
     * @param $text
     * @return false|string[]
     */
    public function textLines($text)
    {
       // remove extra lines from both sides
       $text = trim($text, "\n");
       // convert line breaks chars to unified \n
       $text = str_replace(["\r\n", "\r"], "\n", $text);

       return explode("\n", $text);
    }

    /**
     * @param array $textLines
     * @return array|string|string[]
     */
    public function convertToTags(array $textLines){
        if(count($textLines) > 0){
            $markup_to_html = "";

            foreach($textLines as $line){
                $find_inline_tag = 0;
                $unformatted = 1; // marks a text to add <p> paragraph
                if($line!= ""){
                    $line_start = $line[0];

                    foreach ($this->tagTypes as $tag => $tagElement) {

                        // line starts with markup
                        if($line_start == $tag) {
                            $unformatted = 0;
                        }

                        if (strpos($line, $tag) !== false) {
                            $get_converted_line = $this->appliedHtml($tagElement, $line);
                            if(isset($get_converted_line['error'])){
                                return $get_converted_line;
                            }
                            $line = $get_converted_line;
                            $find_inline_tag = 1;
                        }
                    }
                    if($unformatted == 1 || $find_inline_tag == 0)
                        $markup_to_html.= $this->addParagraph($line);
                    else
                        $markup_to_html.= $get_converted_line . "\n";

                }else{
                    // attach empty/new lines if any exist inside
                    $markup_to_html.= "\n";
                }
            }
        }
        $sanitized_text = trim($markup_to_html, "\n");
        return ['htmltext' => trim($sanitized_text)];

    }

    /**
     * @param $element_type
     * @param $line
     * @return string
     */
    protected function appliedHtml($element_type, $line){
        $converted_line = "";
        if($element_type == 'Header'){
            $converted_line = $this->generateHeader($line);
        }
        else if ($element_type == 'Link'){
            $converted_line = $this->generateLink($line);
        }
        return $converted_line;
    }

    /**
     * @param $line
     * @return string
     */
    protected function addParagraph($line){
        return "<p>". $line ."</p>";
    }

    /**
     * @param $line
     * @return string
     */
    protected function generateHeader($line){
        $line_length = strlen($line);
        $hcount = 0;
        for($i=0; $i < $line_length; $i++){

            // get previous character to check for consecutive occurrence of #
            $previous_letter = $line[$i];

            // browsers recognizes six headings h1-h6.
            // for this assignment added hard coded number for header tag and if it goes above 6 it will keep h6
            if($line[$i] == '#' && $previous_letter == '#' && $hcount <= 5){
                $hcount++;
            }else{
                break;
            }
        }

        $line = str_replace("#", "", $line); // remove markup
        $converted_line = '<h'.$hcount.'>'. trim($line) . '</h'.$hcount.'>';

        return $converted_line;
    }

    /**
     * @param $line
     * @return array|string|string[]
     */
    protected function generateLink($line){
        $linktitle_exist = strpos($line,'[') !== false && strpos($line,']') !== false;
        $linkurl_exist = strpos($line,'(') !== false && strpos($line,')') !== false;

        if($linktitle_exist && $linkurl_exist) {
            preg_match('#\((.*?)\)#', $line, $match);
            $href = $match[1];

            preg_match('#\[(.*?)\]#', $line, $matches);
            $link_title = $matches[1];

            $url = '<a href="' . $href . '">' . $link_title . '</a>';
            $line = str_replace("[$link_title]", "", $line); // remove markup
            $line = str_replace("($href)", $url, $line); // remove markup

            $converted_line = $line;
        }else{
            return ["error" => "Missing bracket(s) for link markup"]; // missing square bracket
        }
        return $converted_line;
    }
}