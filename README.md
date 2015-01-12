BehatHtmlFormatterPlugin
========================

Suggestions are more than welcome !

This is a behat 3 extension to generate HTML reports from your test results.

Add this to your behat.yml file:

<pre>
formatters:
    html:
        output_path: %paths.base%/build/html/behat
  extensions:
    emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
        name: html
</pre>

The *output* parameter is relative to %paths.base% and, when omitted, will default to that same path.

To be done:
========================

1. Store previous runs
2. Add parameters for behat.yml file
3. Add bootstrap as dependency
4. clean up html report
5. Add out parameter

=========================
<img src="http://i.imgur.com/o0zCqiB.png"></img>
