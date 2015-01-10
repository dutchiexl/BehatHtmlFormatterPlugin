BehatHtmlFormatterPlugin
========================

Suggestions are more than welcome !!!

This is a behat plugin to generate HTML reports

Add this to your behat.yml file

<pre>
formatters:
    html: true
  extensions:
    emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
        name: html
</pre>

The HTML will be generated at {$PROJECTROOT}/vendor/emuse/behat-html-formatter/reports/test_report.html. until the --out parameter is supported

To be done:
========================

1. Store previous runs
2. Add parameters for behat.yml file
3. Add bootstrap as dependency
4. clean up html report
5. Add out parameter

=========================
<img src="http://i.imgur.com/o0zCqiB.png"></img>
