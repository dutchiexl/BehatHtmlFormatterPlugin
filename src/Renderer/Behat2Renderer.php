<?php
/**
 * Behat2 renderer for Behat report.
 *
 * @author DaSayan <glennwall@free.fr>
 */

namespace emuse\BehatHTMLFormatter\Renderer;

use Behat\Gherkin\Node\TableNode;

class Behat2Renderer implements RendererInterface
{
    /**
     * Renders before an exercice.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeExercise($obj)
    {
        $print = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
        <html xmlns ='http://www.w3.org/1999/xhtml'>
        <head>
            <meta http-equiv='Content-Type' content='text/html;charset=utf-8'/>
            <title>Behat Test Suite</title> ".$this->getCSS()."
        </head>
        <body>
        <div id='behat'>";

        return $print;
    }

    /**
     * Renders after an exercice.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterExercise($obj)
    {
        //--> features results
        $featTotal = 0;
        $sceTotal = 0;
        $stepsTotal = 0;

        $strFeatPassed = '';

        if (null !== $obj->getPassedFeatures() && count($obj->getPassedFeatures()) > 0) {
            $strFeatPassed = ' <strong class="passed">'.count($obj->getPassedFeatures()).' success</strong>';
            $featTotal += count($obj->getPassedFeatures());
        }

        $strFeatFailed = '';
        $sumRes = 'passed';
        if (null !== $obj->getFailedFeatures() && count($obj->getFailedFeatures()) > 0) {
            $strFeatFailed = ' <strong class="failed">'.count($obj->getFailedFeatures()).' fail</strong>';
            $sumRes = 'failed';
            $featTotal += count($obj->getFailedFeatures());
        }

        //--> scenarios results
        $strScePassed = '';
        if (null !== $obj->getPassedScenarios() && count($obj->getPassedScenarios()) > 0) {
            $strScePassed = ' <strong class="passed">'.count($obj->getPassedScenarios()).' success</strong>';
            $sceTotal += count($obj->getPassedScenarios());
        }

        $strScePending = '';
        if (null !== $obj->getPendingScenarios() && count($obj->getPendingScenarios()) > 0) {
            $strScePending = ' <strong class="failed">'.count($obj->getPendingScenarios()).' fail</strong>';
            $sceTotal += count($obj->getPendingScenarios());
        }

        $strSceFailed = '';
        if (null !== $obj->getFailedScenarios() && count($obj->getFailedScenarios()) > 0) {
            $strSceFailed = ' <strong class="failed">'.count($obj->getFailedScenarios()).' fail</strong>';
            $sceTotal += count($obj->getFailedScenarios());
        }

        //--> steps results
        $strStepsPassed = '';
        if (null !== $obj->getPassedSteps() && count($obj->getPassedSteps()) > 0) {
            $strStepsPassed = ' <strong class="passed">'.count($obj->getPassedSteps()).' success</strong>';
            $stepsTotal += count($obj->getPassedSteps());
        }

        $strStepsPending = '';
        if (null !== $obj->getPendingSteps() && count($obj->getPendingSteps()) > 0) {
            $strStepsPending = ' <strong class="pending">'.count($obj->getPendingSteps()).' pending</strong>';
            $stepsTotal += count($obj->getPendingSteps());
        }

        $strStepsSkipped = '';
        if (null !== $obj->getSkippedSteps() && count($obj->getSkippedSteps()) > 0) {
            $strStepsSkipped = ' <strong class="skipped">'.count($obj->getSkippedSteps()).' skipped</strong>';
            $stepsTotal += count($obj->getSkippedSteps());
        }

        $strStepsFailed = '';
        if (null !== $obj->getFailedSteps() && count($obj->getFailedSteps()) > 0) {
            $strStepsFailed = ' <strong class="failed">'.count($obj->getFailedSteps()).' fail</strong>';
            $stepsTotal += count($obj->getFailedSteps());
        }

        //list of pending steps to display
        $strPendingList = '';
        if (null !== $obj->getPendingSteps() && count($obj->getPendingSteps()) > 0) {
            foreach ($obj->getPendingSteps() as $pendingStep) {
                $strPendingList .= '
                    <li>'.$pendingStep->getKeyword().' '.$pendingStep->getText().'</li>';
            }
            $strPendingList = '
            <div class="pending">Pending steps :
                <ul>'.$strPendingList.'
                </ul>
            </div>';
        }

        $print = '
        <div class="summary '.$sumRes.'">
            <div class="counters">
                <p class="features">
                    '.$featTotal.' features ('.$strFeatPassed.$strFeatFailed.' )
                </p>
                <p class="scenarios">
                    '.$sceTotal.' scenarios ('.$strScePassed.$strScePending.$strSceFailed.' )
                </p>
                <p class="steps">
                    '.$stepsTotal.' steps ('.$strStepsPassed.$strStepsPending.$strStepsSkipped.$strStepsFailed.' )
                </p>
                <p class="time">
                '.$obj->getTimer().' - '.$obj->getMemory().'
                </p>
            </div>
            <div class="switchers">
                <a href="javascript:void(0)" id="behat_show_all">[+] all</a>
                <a href="javascript:void(0)" id="behat_hide_all">[-] all</a>
            </div>
        </div> '.$strPendingList.'
    </div>'.$this->getJS().'
</body>
</html>';

        return $print;
    }

    /**
     * Renders before a suite.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeSuite($obj)
    {
        $print = '
        <div class="suite">Suite : '.$obj->getCurrentSuite()->getName().'</div>';

        return $print;
    }

    /**
     * Renders after a suite.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterSuite($obj)
    {
        return '';
    }

    /**
     * Renders before a feature.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeFeature($obj)
    {
        //feature head
        $print = '
        <div class="feature">
            <h2>
                <span id="feat'.$obj->getCurrentFeature()->getId().'" class="keyword"> Feature: </span>
                <span class="title">'.$obj->getCurrentFeature()->getName().'</span>
            </h2>
            <p>'.$obj->getCurrentFeature()->getDescription().'</p>
            <ul class="tags">';
        foreach ($obj->getCurrentFeature()->getTags() as $tag) {
            $print .= '
                <li>@'.$tag.'</li>';
        }
        $print .= '
            </ul>';

        //TODO path is missing (?)

        return $print;
    }

    /**
     * Renders after a feature.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterFeature($obj)
    {
        //list of results
        $print = '
            <div class="featureResult '.$obj->getCurrentFeature()->getPassedClass().'">Feature has '.$obj->getCurrentFeature()->getPassedClass();

        //percent only if failed scenarios
        if ($obj->getCurrentFeature()->getTotalAmountOfScenarios() > 0 && 'failed' === $obj->getCurrentFeature()->getPassedClass()) {
            $print .= '
                <span>Scenarios passed : '.round($obj->getCurrentFeature()->getPercentPassed(), 2).'%,
                Scenarios failed : '.round($obj->getCurrentFeature()->getPercentFailed(), 2).'%</span>';
        }

        $print .= '
            </div>
        </div>';

        return $print;
    }

    /**
     * Renders before a scenario.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeScenario($obj)
    {
        //scenario head
        $print = '
            <div class="scenario">
                <ul class="tags">';
        foreach ($obj->getCurrentScenario()->getTags() as $tag) {
            $print .= '
                    <li>@'.$tag.'</li>';
        }
        $print .= '
                </ul>';

        $print .= '
                <h3>
                    <span class="keyword">'.$obj->getCurrentScenario()->getId().' Scenario: </span>
                    <span class="title">'.$obj->getCurrentScenario()->getName().'</span>
                </h3>
                <ol>';

        //TODO path is missing

        return $print;
    }

    /**
     * Renders after a scenario.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterScenario($obj)
    {
        $print = '
                </ol>
            </div>';

        return $print;
    }

    /**
     * Renders before an outline.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeOutline($obj)
    {
        //scenario head
        $print = '
            <div class="scenario">
                <ul class="tags">';
        foreach ($obj->getCurrentScenario()->getTags() as $tag) {
            $print .= '
                    <li>@'.$tag.'</li>';
        }
        $print .= '
                </ul>';

        $print .= '
                <h3>
                    <span class="keyword">'.$obj->getCurrentScenario()->getId().' Scenario Outline: </span>
                    <span class="title">'.$obj->getCurrentScenario()->getName().'</span>
                </h3>
                <ol>';

        //TODO path is missing

        return $print;
    }

    /**
     * Renders after an outline.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterOutline($obj)
    {
        return $this->renderAfterScenario($obj);
    }

    /**
     * Renders before a step.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeStep($obj)
    {
        return '';
    }

    /**
     * Renders TableNode arguments.
     *
     * @param TableNode $table
     *
     * @return string : HTML generated
     */
    public function renderTableNode(TableNode $table)
    {
        $arguments = '<table class="argument"> <thead>';
        $header = $table->getRow(0);
        $arguments .= $this->preintTableRows($header);

        $arguments .= '</thead><tbody>';
        foreach ($table->getHash() as $row) {
            $arguments .= $this->preintTableRows($row);
        }

        $arguments .= '</tbody></table>';

        return $arguments;
    }

    /**
     * Renders table rows.
     *
     * @param array $row
     *
     * @return string : HTML generated
     */
    public function preintTableRows($row)
    {
        $return = '<tr class="row">';
        foreach ($row as $column) {
            $return .= '<td>'.htmlentities($column).'</td>';
        }
        $return .= '</tr>';

        return $return;
    }

    /**
     * Renders after a step.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterStep($obj)
    {
        $feature = $obj->getCurrentFeature();
        $scenario = $obj->getCurrentScenario();

        $steps = $scenario->getSteps();
        $step = end($steps); //needed because of strict standards

        //path displayed only if available (it's not available in undefined steps)
        $strPath = '';
        if (null !== $step->getDefinition()) {
            $strPath = $step->getDefinition()->getPath();
        }

        $stepResultClass = '';
        if ($step->isPassed()) {
            $stepResultClass = 'passed';
        }
        if ($step->isFailed()) {
            $stepResultClass = 'failed';
        }
        if ($step->isSkipped()) {
            $stepResultClass = 'skipped';
        }
        if ($step->isPending()) {
            $stepResultClass = 'pending';
        }

        $arguments = '';
        $argumentType = $step->getArgumentType();

        if ('PyString' == $argumentType) {
            $arguments = '<br><pre class="argument">'.htmlentities($step->getArguments()).'</pre>';
        }

        if ('Table' == $argumentType) {
            $arguments = '<br><pre class="argument">'.$this->renderTableNode($step->getArguments()).'</pre>';
        }

        $print = '
                    <li class="'.$stepResultClass.'">
                        <div class="step">
                            <span class="keyword">'.$step->getKeyWord().' </span>
                            <span class="text">'.htmlentities($step->getText()).' </span>
                            <span class="path">'.$strPath.'</span>'
                            .$arguments.'
                        </div>';
        $exception = $step->getException();
        if (!empty($exception)) {
            $print .= '
                        <pre class="backtrace">'.$step->getException().'</pre>';
            if ($scenario->getRelativeScreenshotPath()) {
                $print .= '<a href="'.$scenario->getRelativeScreenshotPath().'">Screenshot</a>';
            }
        }
        $print .= '
                    </li>';

        return $print;
    }

    /**
     * To include CSS.
     *
     * @return string : HTML generated
     */
    public function getCSS()
    {
        return "<style type='text/css'>
                body {
                    margin:0px;
                    padding:0px;
                    position:relative;
                    padding-top:93px;
                }
                #behat {
                    float:left;
                    font-family: Georgia, serif;
                    font-size:18px;
                    line-height:26px;
                    width:100%;
                }
                #behat .statistics {
                    float:left;
                    width:100%;
                    margin-bottom:15px;
                }
                #behat .statistics p {
                    text-align:right;
                    padding:5px 15px;
                    margin:0px;
                    border-right:10px solid #000;
                }
                #behat .statistics.failed p {
                    border-color:#C20000;
                }
                #behat .statistics.passed p {
                    border-color:#3D7700;
                }
                #behat .suite {
                    margin:8px;
                }
                #behat .feature {
                    margin:15px;
                }
                #behat h2, #behat h3, #behat h4 {
                    margin:0px 0px 5px 0px;
                    padding:0px;
                    font-family:Georgia;
                }
                #behat h2 .title, #behat h3 .title, #behat h4 .title {
                    font-weight:normal;
                }
                #behat .path {
                    font-size:10px;
                    font-weight:normal;
                    font-family: 'Bitstream Vera Sans Mono', 'DejaVu Sans Mono', Monaco, Courier, monospace !important;
                    color:#999;
                    padding:0px 5px;
                    float:right;
                }
                #behat .path a:link,
                #behat .path a:visited {
                    color:#999;
                }
                #behat .path a:hover,
                #behat .path a:active {
                    background-color:#000;
                    color:#fff;
                }
                #behat h3 .path {
                    margin-right:4%;
                }
                #behat ul.tags {
                    font-size:14px;
                    font-weight:bold;
                    color:#246AC1;
                    list-style:none;
                    margin:0px;
                    padding:0px;
                }
                #behat ul.tags li {
                    display:inline;
                }
                #behat ul.tags li:after {
                    content:' ';
                }
                #behat ul.tags li:last-child:after {
                    content:'';
                }
                #behat .feature > p {
                    margin-top:0px;
                    margin-left:20px;
                }
                #behat .scenario {
                    margin-left:20px;
                    margin-bottom:20px;
                }
                #behat .scenario > ol,
                #behat .scenario .examples > ol {
                    margin:0px;
                    list-style:none;
                    padding:0px;
                }
                #behat .scenario > ol {
                    margin-left:20px;
                }
                #behat .scenario > ol:after,
                #behat .scenario .examples > ol:after {
                    content:'';
                    display:block;
                    clear:both;
                }
                #behat .scenario > ol li,
                #behat .scenario .examples > ol li {
                    float:left;
                    width:95%;
                    padding-left:5px;
                    border-left:5px solid;
                    margin-bottom:4px;
                }
                #behat .scenario > ol li .argument,
                #behat .scenario .examples > ol li .argument {
                    margin:10px 20px;
                    font-size:14px;
                    overflow:auto;
                }
                #behat .scenario > ol li table.argument,
                #behat .scenario .examples > ol li table.argument {
                    border:1px solid #d2d2d2;
                }
                #behat .scenario > ol li table.argument thead td,
                #behat .scenario .examples > ol li table.argument thead td {
                    font-weight: bold;
                }
                #behat .scenario > ol li table.argument td,
                #behat .scenario .examples > ol li table.argument td {
                    padding:5px 10px;
                    background:#f3f3f3;
                }
                #behat .scenario > ol li .keyword,
                #behat .scenario .examples > ol li .keyword {
                    font-weight:bold;
                }
                #behat .scenario > ol li .path,
                #behat .scenario .examples > ol li .path {
                    float:right;
                }
                #behat .scenario .examples {
                    margin-top:20px;
                    margin-left:40px;
                }
                #behat .scenario .examples h4 span {
                    font-weight:normal;
                    background:#f3f3f3;
                    color:#999;
                    padding:0 5px;
                    margin-left:10px;
                }
                #behat .scenario .examples table {
                    margin-left:20px;
                }
                #behat .scenario .examples table thead td {
                    font-weight:bold;
                    text-align:center;
                }
                #behat .scenario .examples table td {
                    padding:2px 10px;
                    font-size:16px;
                }
                #behat .scenario .examples table .failed.exception td {
                    border-left:5px solid #000;
                    border-color:#C20000 !important;
                    padding-left:0px;
                }
                pre {
                    font-family:monospace;
                }
                .snippet {
                    font-size:14px;
                    color:#000;
                    margin-left:20px;
                }
                .backtrace {
                    font-size:12px;
                    line-height:18px;
                    color:#000;
                    overflow:auto;
                    margin-left:20px;
                    padding:15px;
                    border-left:2px solid #C20000;
                    background: #fff;
                    margin-right:15px;
                }
                #behat .passed {
                    background:#DBFFB4;
                    border-color:#65C400 !important;
                    color:#3D7700;
                }
                #behat .failed {
                    background:#FFFBD3;
                    border-color:#C20000 !important;
                    color:#C20000;
                }
                #behat .undefined, #behat .pending {
                    border-color:#FAF834 !important;
                    background:#FCFB98;
                    color:#000;
                }
                #behat .skipped {
                    background:lightCyan;
                    border-color:cyan !important;
                    color:#000;
                }
                #behat .summary {
                    position: absolute;
                    top: 0px;
                    left: 0px;
                    width:100%;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    line-height: 18px;
                }
                #behat .summary .counters {
                    padding: 10px;
                    border-top: 0px;
                    border-bottom: 0px;
                    border-right: 0px;
                    border-left: 5px;
                    border-style: solid;
                    overflow: hidden;
                }
                #behat .summary .switchers {
                    position: absolute;
                    right: 15px;
                    top: 25px;
                }
                #behat .summary .switcher {
                    text-decoration: underline;
                    cursor: pointer;
                }
                #behat .summary .switchers a {
                    margin-left: 10px;
                    color: #000;
                }
                #behat .summary .switchers a:hover {
                    text-decoration:none;
                }
                #behat .summary p {
                    margin:0px;
                }

                #behat .featureResult > span {
                    font-size: 14px;
                }

                #behat .jq-toggle > .scenario,
                #behat .jq-toggle > ol,
                #behat .jq-toggle > .examples {
                    display:none;
                }
                #behat .jq-toggle-opened > .scenario,
                #behat .jq-toggle-opened > ol,
                #behat .jq-toggle-opened > .examples {
                    display:block;
                }
                #behat .jq-toggle > h2,
                #behat .jq-toggle > h3 {
                    cursor:pointer;
                }
                #behat .jq-toggle > h2:after,
                #behat .jq-toggle > h3:after {
                    content:' |+';
                    font-weight:bold;
                }
                #behat .jq-toggle-opened > h2:after,
                #behat .jq-toggle-opened > h3:after {
                    content:' |-';
                    font-weight:bold;
                }
            </style>

            <style type='text/css' media='print'>
                body {
                    padding:0px;
                }

                #behat {
                    font-size:11px;
                }

                #behat .jq-toggle > .scenario,
                #behat .jq-toggle > .scenario .examples,
                #behat .jq-toggle > ol {
                    display:block;
                }

                #behat .summary {
                    position:relative;
                }

                #behat .summary .counters {
                    border:none;
                }

                #behat .summary .switchers {
                    display:none;
                }

                #behat .step .path {
                    display:none;
                }

                #behat .jq-toggle > h2:after,
                #behat .jq-toggle > h3:after {
                    content:'';
                    font-weight:bold;
                }

                #behat .jq-toggle-opened > h2:after,
                #behat .jq-toggle-opened > h3:after {
                    content:'';
                    font-weight:bold;
                }

                #behat .scenario > ol li,
                #behat .scenario .examples > ol li {
                    border-left:none;
                }
            </style>";
    }

    /**
     * To include JS.
     *
     * @return string : HTML generated
     */
    public function getJS()
    {
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js'></script>
        <script type='text/javascript'>
            $(document).ready(function(){
                Array.prototype.diff = function(a) {
                    return this.filter(function(i) {return a.indexOf(i) < 0;});
                };

                $('#behat .feature h2').click(function(){
                    $(this).parent().toggleClass('jq-toggle-opened');
                }).parent().addClass('jq-toggle');

                $('#behat .scenario h3').click(function(){
                    $(this).parent().toggleClass('jq-toggle-opened');
                }).parent().addClass('jq-toggle');

                $('#behat_show_all').click(function(){
                    $('#behat .feature').addClass('jq-toggle-opened');
                    $('#behat .scenario').addClass('jq-toggle-opened');
                });

                $('#behat_hide_all').click(function(){
                    $('#behat .feature').removeClass('jq-toggle-opened');
                    $('#behat .scenario').removeClass('jq-toggle-opened');
                });

                $('#behat .summary .counters .scenarios .passed')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:not(:has(.failed, .pending))');
                        var feature  = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });


                $('#behat .summary .counters .scenarios .failed')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:has(.failed, .pending)');
                        var feature = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });

                $('#behat .summary .counters .steps .passed')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:has(.passed)');
                        var feature  = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });

                $('#behat .summary .counters .steps .failed')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:has(.failed)');
                        var feature = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });

                $('#behat .summary .counters .steps .skipped')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:has(.skipped)');
                        var feature = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });

                $('#behat .summary .counters .steps .pending')
                    .addClass('switcher')
                    .click(function(){
                        var scenario = $('.feature .scenario:has(.pending)');
                        var feature = scenario.parent();

                        $('#behat_hide_all').click();

                        scenario.addClass('jq-toggle-opened');
                        feature.addClass('jq-toggle-opened');
                    });
            });
        </script>";
    }
}
