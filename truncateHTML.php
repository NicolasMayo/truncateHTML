<?php

/**
 * @param string $htmlText The text to truncate
 * @param int $truncateLength The length of the truncated text
 * @param bool $truncateWords Defines if the truncate can happen in the middle of a word or not
 * @param bool $removeImages Defines if the images are removed or not
 * @param bool $ignoreTagsSize Defines if the HTMl tags length is included in the excerpt size
 * @return string Returns an excerpt of a given HTML Text without removing the tags, and close the non-closed ones
 */
function truncateHTML($htmlText, $truncateLength = 300, $truncateWords = FALSE, $removeImages = TRUE)
{
    if(!is_string($htmlText) || empty($htmlText)) {
        return '';
    }

    $singleTagSubPattern = "(?:<[a-z0-9 ]+/>)|(?:<br>)|(?:<img )";
    $openingTagSubPattern = "<([a-z0-9]+)(?:(?:[^>]*[^/]>)|(?:>))";
    $closingTagSubPattern = "</([a-z0-9]+)[^>]*>";
    $allTagsPattern = "#(?:$singleTagSubPattern)|(?:$openingTagSubPattern)|(?:$closingTagSubPattern)#i";
    $doubleTagsPattern = "#(?:$openingTagSubPattern)|(?:$closingTagSubPattern)#i";

    $tagsHTMLText = array();
    preg_match_all($allTagsPattern, $htmlText, $tagsHTMLText);

    $offset = 0;
    foreach($tagsHTMLText[0] as $tag) {
        $tagSize = mb_strlen($tag);
        $tagStart = mb_strpos($htmlText, $tag, $offset);
        $tagEnd = $tagStart + $tagSize;
        $offset = $tagEnd;
        if($truncateLength > $tagStart) {
            $truncateLength += $tagSize;
        }
    }

    if(!$truncateWords) {
        $HTMLLength = mb_strlen($htmlText);
        while(
            !in_array(mb_substr($htmlText, $truncateLength, 1) , array(' ', '>', '<', '.', '?', '!'))
            && $HTMLLength > $truncateLength
        ) {
            $truncateLength++;
        }
    }

    $truncatedText = mb_substr($htmlText, 0, $truncateLength);

    if($removeImages) {
        $truncatedText = preg_replace("#<img [^>]+>#", '', $truncatedText);
    }

    $tagsTruncatedText = array();
    $nbTagsTruncatedText = preg_match_all($doubleTagsPattern, $truncatedText, $tagsTruncatedText);
    $openedTagsTruncatedText = array();
    for($i = 0; $i < $nbTagsTruncatedText; $i++) {
        if(preg_match("#^$openingTagSubPattern$#i", $tagsTruncatedText[0][$i])) {
            $openedTagsTruncatedText[$i] = $tagsTruncatedText[1][$i];
        } else {
            $cursor = $i;
            do {
                if(isset($openedTagsTruncatedText[$cursor]) && $openedTagsTruncatedText[$cursor] === $tagsTruncatedText[2][$i]) {
                    unset($openedTagsTruncatedText[$cursor]);
                    break;
                }
            } while(--$cursor >= 0);
        }
    }

    $tagsToAdd = array_reverse($openedTagsTruncatedText);

    foreach($tagsToAdd as $tag) {
        $truncatedText .= "</$tag>";
    }

    return $truncatedText;
}
