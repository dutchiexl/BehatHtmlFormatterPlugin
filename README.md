BehatHtmlFormatterPlugin
========================

This is a behat plugin to generate HTML reports

Add this to your behat.yml file

formatters:
    html: true
  extensions:
    emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
        name: html
