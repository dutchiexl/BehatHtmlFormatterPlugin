BehatHtmlFormatterPlugin
========================

Suggestions are more than welcome !

This is a behat 3 extension to generate HTML reports from your test results.

Add this to your behat.yml file:

<pre>
formatters:
<<<<<<< HEAD
    html: true
    output: build/html/behat
=======
    html:
        output_path: build/html/behat
>>>>>>> fcedea033d2066beb94124318dd76f57b7bce868
  extensions:
    emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
        name: html
</pre>

<<<<<<< HEAD
<<<<<<< HEAD

The HTML will be generated at {$PROJECTROOT}/vendor/emuse/behat-html-formatter/reports/test_report.html. until the --out parameter is supported

=======
>>>>>>> e370bf764d3365a8d9a379739c1eb36ebe040183
The *output* parameter is relative to %paths.base% and, when omitted, will default to that same path.
=======
The *output_path* parameter is relative to %paths.base% and, when omitted, will default to that same path.

>>>>>>> fcedea033d2066beb94124318dd76f57b7bce868

To be done:
========================

1. Store previous runs
2. Add parameters for behat.yml file
3. Add bootstrap as dependency
4. clean up html report
5. Add out parameter

=========================
<img src="http://i.imgur.com/o0zCqiB.png"></img>
