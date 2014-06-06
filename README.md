truncateHTML
============

Function to truncate HTML text without removing the tags and by closing the non-closed ones

``` php
function truncateHTML($htmlText, $truncateLength = 300, $truncateWords = FALSE, $removeImages = TRUE)
```

<h2>Parameters</h2>

- <strong>htmlText</strong> : The text to truncate
- <strong>truncateLength</strong> : The length of the truncated text
- <strong>truncateWords</strong> : Defines if the truncate can happen in the middle of a word or not
- <strong>removeImages</strong> : Defines if the images are removed or not

<h2>Return value</h2>

Returns an excerpt of a given HTML Text without removing the tags, and close the non-closed ones
