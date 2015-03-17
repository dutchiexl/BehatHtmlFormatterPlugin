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
        renderer: Twig,Behat2
</pre>

The *output* parameter is relative to %paths.base% and, when omitted, will default to that same path.

The *renderer* is the renderer engine and the report format that you want to be generated.

Actually, there is 3 formats :

- **Twig** : new report format based on Twig, **requires Twig installed**
- **Behat2** : like Behat 2 HTML report
- **Minimal** : ultra minimal...

You must specify the format that you want to use in the *renderer* parameter.

You can combine formats to generate multiple reports with multiple formats at one time for one test suite : you just need to separate them by commas

File names have this format : *"renderer name"*_*"date hour"*

To be done:
========================

1. Store previous runs --> Done
2. Add parameters for behat.yml file
3. Add bootstrap as dependency
4. clean up html report
5. Add out parameter

=========================

Twig :
<img src="http://i.imgur.com/o0zCqiB.png"></img>

Behat2 :
<img src="http://i57.tinypic.com/287g942.jpg"></img>


