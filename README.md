BehatHtmlFormatterPlugin
========================

Suggestions are more than welcome !

This is a behat 3 extension to generate HTML reports from your test results.
Fork of dutchiexl HTML report for behat.

Actually, it can generate a HTML report with Behat 2 HTML report format 

Add this to your behat.yml file:

<pre>
formatters:
    html:
        output_path: %paths.base%/build/html/behat
  extensions:
    dasayan\BehatHTMLFormatter\BehatHTMLFormatterExtension:
        name: html
</pre>

The *output* parameter is relative to %paths.base% and, when omitted, will default to that same path.

WIP : There is many to be done !

