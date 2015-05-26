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
        file_name: Index
        print_args: true
        loop_break: true
</pre>

The *output* parameter is relative to %paths.base% and, when omitted, will default to that same path.

The *renderer* is the renderer engine and the report format that you want to be generated.

The *file_name* is optional. When it is added, the report name will be fixed instead fo generated, and this file will be overwritten with every build.

Actually, there is 3 formats :

- **Twig** : new report format based on Twig, **requires Twig installed**
- **Behat2** : like Behat 2 HTML report
- **Minimal** : ultra minimal...

You must specify the format that you want to use in the *renderer* parameter.

You can combine formats to generate multiple reports with multiple formats at one time for one test suite : you just need to separate them by commas

File names have this format : *"renderer name"*_*"date hour"*

**Twig renderer only parameters:**

The *print_args* is optional. When it is added, the report will contain the arguments for each step if exists. (e.g. Tables) 

The *print_outp* is optional. When it is added, the report will contain the output of each step if exists. (e.g. Exceptions) 

The *loop_break* is optional. When it is added, Scenario Outlines printed to the report will have a break line separating the executions.

To be done:
========================

1. Add parameters for behat.yml file
2. Add bootstrap as dependency
3. clean up html report
4. Add out parameter

Screenshots
=========================

Twig :

<img src="http://i.imgur.com/o0zCqiB.png"></img>

Behat2 :

<img src="http://i57.tinypic.com/287g942.jpg"></img>


