<?php

namespace emuse\BehatHTMLFormatter\Formatter;

use Behat\Behat\EventDispatcher\Event\AfterOutlineTested;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\Output\Exception\BadOutputPathException;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use emuse\BehatHTMLFormatter\Classes\Feature;
use emuse\BehatHTMLFormatter\Classes\Scenario;
use emuse\BehatHTMLFormatter\Classes\Step;
use emuse\BehatHTMLFormatter\Classes\Suite;
use emuse\BehatHTMLFormatter\Printer\FileOutputPrinter;


/**
 * Class BehatHTMLFormatter
 * @package tests\features\formatter
 */
class BehatHTMLFormatter implements Formatter
{

    //<editor-fold desc="Variables">
    /**
     * @var array
     */
    private $parameters;
    /**
     * @var
     */
    private $name;

    /**
     * @param String $outputPath where to save the generated report file
     */
    private $outputPath;

    /**
     * @param String $base_path Behat base path
     */
    private $base_path;

    /**
     * Printer used by this Formatter
     * @param $printer OutputPrinter
     */
    private $printer;

    /**
     * @var Array
     */
    private $suites;

    /**
     * @var Suite
     */
    private $currentSuite;

    /**
     * @var int
     */
    private $featureCounter = 1;
    /**
     * @var Feature
     */
    private $currentFeature;

    /**
     * @var Scenario
     */
    private $currentScenario;

    /**
     * @var Scenario[]
     */
    private $failedScenarios;

    /**
     * @var Scenario[]
     */
    private $passedScenarios;

    /**
     * @var Feature[]
     */
    private $failedFeatures;

    /**
     * @var Feature[]
     */
    private $passedFeatures;

    /**
     * @var Step[]
     */
    private $failedSteps;

    /**
     * @var Step[]
     */
    private $passedSteps;
    
    /**
     * @var Step[]
     */
    private $pendingSteps;
    
    /**
     * @var Step[]
     */
    private $skippedSteps;
    //</editor-fold>

    //<editor-fold desc="Formatter functions">
    /**
     * @param $name
     * @param $base_path
     */
    function __construct($name, $base_path)
    {
        $this->name = $name;
        $this->printer = new FileOutputPrinter($base_path);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'tester.exercise_completed.before' => 'onBeforeExercise',
            'tester.exercise_completed.after' => 'onAfterExercise',
            'tester.suite_tested.before' => 'onBeforeSuiteTested',
            'tester.suite_tested.after' => 'onAfterSuiteTested',
            'tester.feature_tested.before' => 'onBeforeFeatureTested',
            'tester.feature_tested.after' => 'onAfterFeatureTested',
            'tester.scenario_tested.before' => 'onBeforeScenarioTested',
            'tester.scenario_tested.after' => 'onAfterScenarioTested',
            'tester.outline_tested.before' => 'onBeforeOutlineTested',
            'tester.outline_tested.after' => 'onAfterOutlineTested',
            'tester.step_tested.after' => 'onAfterStepTested',
        );
    }

    /**
     * Returns formatter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns formatter description.
     *
     * @return string
     */
    public function getDescription()
    {
        return "Formatter for teamcity";
    }

    /**
     * Returns formatter output printer.
     *
     * @return OutputPrinter
     */
    public function getOutputPrinter()
    {
        return $this->printer;
    }

    /**
     * Sets formatter parameter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns parameter name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Verify that the specified output path exists or can be created,
     * then sets the output path.
     *
     * @param String $path Output path relative to %paths.base%
     * @throws BadOutputPathException
     */
    public function setOutputPath($path)
    {
        $outpath = realpath($this->base_path . DIRECTORY_SEPARATOR . $path);
        if (!file_exists($outpath)) {
            if (!mkdir($outpath, 0755, true))
                throw new BadOutputPathException(
                    sprintf(
                        'Output path %s does not exist and could not be created!',
                        $outpath
                    ),
                    $outpath
                );
        } else {
            if (!is_dir($outpath)) {
                throw new BadOutputPathException(
                    sprintf(
                        'The argument to `output` is expected to the a directory, but got %s!',
                        $outpath
                    ),
                    $outpath
                );
            }
        }
        $this->outputPath = $outpath;
    }

    /**
     * Returns output path
     *
     * @return String output path
     */
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    //</editor-fold>

    //<editor-fold desc="Event functions">
    /**
     * @param BeforeExerciseCompleted $event
     */
    public function onBeforeExercise(BeforeExerciseCompleted $event)
    {
        //creating report file with CSS at head
        $print = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
        <html xmlns ='http://www.w3.org/1999/xhtml'>
        <head>
            <meta http-equiv='Content-Type' content='text/html;charset=utf-8'/>
            <title>Behat Test Suite</title> " . $this->getCSS() . "
        </head>
        <body>
        <div id='behat'>" ;
        
        $this->printer->write($print) ;
    }

    /**
     * @param AfterExerciseCompleted $event
     */
    public function onAfterExercise(AfterExerciseCompleted $event)
    {
        //--> features results
        $strFeatPassed = '' ;
        if (count($this->passedFeatures) > 0) {
            $strFeatPassed = ' <strong class="passed">'.count($this->passedFeatures).' success</strong>';
        }      
        
        $strFeatFailed = '' ;    
        $sumRes = 'passed' ;        
        if (count($this->failedFeatures) > 0) {
            $strFeatFailed = ' <strong class="failed">'.count($this->failedFeatures).' fail</strong>';
            $sumRes = 'failed' ;
        } 
        
        //--> scenarios results
        $strScePassed = '' ;
        if (count($this->passedScenarios) > 0) {
            $strScePassed = ' <strong class="passed">'.count($this->passedScenarios).' success</strong>';
        }             
        
        $strSceFailed = '' ;
        if (count($this->failedScenarios) > 0) {
            $strSceFailed = ' <strong class="failed">'.count($this->failedScenarios).' fail</strong>';
        } 
        
        //--> steps results
        $strStepsPassed = '' ;
        if (count($this->passedSteps) > 0) {
            $strStepsPassed = ' <strong class="passed">'.count($this->passedSteps).' success</strong>';
        }             
        
        $strStepsPending = '' ;
        if (count($this->pendingSteps) > 0) {
            $strStepsPending = ' <strong class="pending">'.count($this->pendingSteps).' pending</strong>';
        } 

        $strStepsSkipped = '' ;
        if (count($this->skippedSteps) > 0) {
            $strStepsSkipped = ' <strong class="skipped">'.count($this->skippedSteps).' skipped</strong>';
        } 
        
        $strStepsFailed = '' ;
        if (count($this->failedSteps) > 0) {
            $strStepsFailed = ' <strong class="failed">'.count($this->failedSteps).' fail</strong>';
        } 
        
        
        //totals
        $featTotal = (count($this->failedFeatures) + count($this->passedFeatures));
        $sceTotal = (count($this->failedScenarios) + count($this->passedScenarios)) ;
        $stepsTotal = (count($this->failedSteps) + count($this->passedSteps) + count($this->skippedSteps) + count($this->pendingSteps)) ;

        //list of pending steps to display
        $strPendingList = '' ;
        if (count($this->pendingSteps) > 0) {
            foreach($this->pendingSteps as $pendingStep) {
                $strPendingList .= '
                    <li>' . $pendingStep->getKeyword() . ' ' . $pendingStep->getText() . '</li>' ;
            }
                $strPendingList = '
            <div class="pending">Pending steps : 
                <ul>' . $strPendingList . '
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
                    '.$sceTotal.' scenarios ('.$strScePassed.$strSceFailed.' )
                </p>
                <p class="steps">
                    '.$stepsTotal.' steps ('.$strStepsPassed.$strStepsPending.$strStepsSkipped.$strStepsFailed.' )
                </p>
                <p class="time">
                XmXX.XXXs
                </p>
            </div>
            <div class="switchers">
                <a href="javascript:void(0)" id="behat_show_all">[+] all</a>
                <a href="javascript:void(0)" id="behat_hide_all">[-] all</a>
            </div>
        </div> ' .$strPendingList. '
    </div>' . $this->getJS() . '
</body>
</html>' ;

    $this->printer->writeln($print) ;
    }

    /**
     * @param BeforeSuiteTested $event
     */
    public function onBeforeSuiteTested(BeforeSuiteTested $event)
    {
        $this->currentSuite = new Suite();
        $this->currentSuite->setName($event->getSuite()->getName());
        
        $print = '
        <div class="suite">Suite : ' . $event->getSuite()->getName() . '</div>';
        $this->printer->writeln($print);
    }

    /**
     * @param AfterSuiteTested $event
     */
    public function onAfterSuiteTested(AfterSuiteTested $event)
    {
        $this->suites[] = $this->currentSuite;
    }

    /**
     * @param BeforeFeatureTested $event
     */
    public function onBeforeFeatureTested(BeforeFeatureTested $event)
    {
        $feature = new Feature();
        $feature->setId($this->featureCounter);
        $this->featureCounter++;
        $feature->setName($event->getFeature()->getTitle());
        $feature->setDescription($event->getFeature()->getDescription());
        $feature->setTags($event->getFeature()->getTags());
        $feature->setFile($event->getFeature()->getFile());
        $this->currentFeature = $feature;    
        
        //feature head
        $print = '
        <div class="feature">
            <h2>
                <span id="feat'.$this->currentFeature->getId().'" class="keyword"> Feature: </span>
                <span class="title">' . $this->currentFeature->getName() . '</span>
            </h2>
            <p>' . $this->currentFeature->getDescription() . '</p>
            <ul class="tags">' ;
        foreach($this->currentFeature->getTags() as $tag) {
            $print .= '
                <li>@' . $tag .'</li>' ;
        }      
        $print .= '
            </ul>' ;
        
        //TODO path is missing (?)
        
        $this->printer->writeln($print);
    }

    /**
     * @param AfterFeatureTested $event
     */
    public function onAfterFeatureTested(AfterFeatureTested $event)
    {
        $this->currentSuite->addFeature($this->currentFeature);
        if ($this->currentFeature->allPassed()) {
            $this->passedFeatures[] = $this->currentFeature;
        } else {
            $this->failedFeatures[] = $this->currentFeature;
        }
                      
        //list of results
        $print = '
            <div class="featureResult '.$this->currentFeature->getPassedClass().'">Feature has ' . $this->currentFeature->getPassedClass() ;

        //percent only if failed scenarios
        if ($this->currentFeature->getTotalAmountOfScenarios() > 0 && $this->currentFeature->getPassedClass() === 'failed') {
            $print .= '
                <span>Scenarios passed : ' . round($this->currentFeature->getPercentPassed(), 2) . '%, 
                Scenarios failed : ' . round($this->currentFeature->getPercentFailed(), 2) . '%</span>' ;
        }

        $print .= '
            </div>
        </div>';   
        
        $this->printer->writeln($print);
    }

    /**
     * @param BeforeScenarioTested $event
     */
    public function onBeforeScenarioTested(BeforeScenarioTested $event)
    {
        $scenario = new Scenario();
        $scenario->setName($event->getScenario()->getTitle());
        $scenario->setTags($event->getScenario()->getTags());
        $scenario->setLine($event->getScenario()->getLine());
        $this->currentScenario = $scenario;

        //scenario head
        $print = '
            <div class="scenario">
                <ul class="tags">' ;
        foreach($scenario->getTags() as $tag) {
            $print .= '
                    <li>@' . $tag .'</li>';
        }         
        $print .= '
                </ul>';        
        
        $print .= '
                <h3>
                    <span class="keyword">' . $scenario->getId() . ' Scenario: </span>
                    <span class="title">' . $scenario->getName() . '</span>
                </h3>
                <ol>' ;
        
        //TODO path is missing

        $this->printer->writeln($print);
    }

    /**
     * @param AfterScenarioTested $event
     */
    public function onAfterScenarioTested(AfterScenarioTested $event)
    {
        $scenarioPassed = $event->getTestResult()->isPassed();

        if ($scenarioPassed) {
            $this->passedScenarios[] = $this->currentScenario;
            $this->currentFeature->addPassedScenario();
        } else {
            $this->failedScenarios[] = $this->currentScenario;
            $this->currentFeature->addFailedScenario();
        }

        $this->currentScenario->setPassed($event->getTestResult()->isPassed());
        $this->currentFeature->addScenario($this->currentScenario);
        
        $print = '
                </ol>
            </div>';
        
        $this->printer->writeln($print);
    }

    /**
     * @param BeforeOutlineTested $event
     */
    public function onBeforeOutlineTested(BeforeOutlineTested $event)
    {
        $scenario = new Scenario();
        $scenario->setName($event->getOutline()->getTitle());
        $scenario->setTags($event->getOutline()->getTags());
        $scenario->setLine($event->getOutline()->getLine());
        $this->currentScenario = $scenario;
    }

    /**
     * @param AfterOutlineTested $event
     */
    public function onAfterOutlineTested(AfterOutlineTested $event)
    {
        $scenarioPassed = $event->getTestResult()->isPassed();

        if ($scenarioPassed) {
            $this->passedScenarios[] = $this->currentScenario;
            $this->currentFeature->addPassedScenario();
        } else {
            $this->failedScenarios[] = $this->currentScenario;
            $this->currentFeature->addFailedScenario();
        }

        $this->currentScenario->setPassed($event->getTestResult()->isPassed());
        $this->currentFeature->addScenario($this->currentScenario);
    }

    /**
     * @param AfterStepTested $event
     */
    public function onAfterStepTested(AfterStepTested $event)
    {
        $result = $event->getTestResult();

        /** @var Step $step */
        $step = new Step();
        $step->setKeyword($event->getStep()->getKeyword());
        $step->setText($event->getStep()->getText());
        $step->setLine($event->getStep()->getLine());
        $step->setArguments($event->getStep()->getArguments());
        $step->setResult($result);
        
        //What is the result of this step ?
        if (is_a($result, 'Behat\Behat\Tester\Result\UndefinedStepResult')) {
            //pending step -> no definition to load
            $step->setStatus('pending') ;
            $this->pendingSteps[] = $step;
            
        } else if (is_a($result, 'Behat\Behat\Tester\Result\SkippedStepResult')){
            //skipped step
            $step->setDefinition($result->getStepDefinition());
            $step->setStatus('skipped') ;
            $this->skippedSteps[] = $step;
        } else {
            //failed or passed
            if ($result instanceof ExecutedStepResult) {
                $step->setDefinition($result->getStepDefinition());
                $exception = $result->getException();
                if ($exception) {
                    $step->setException($exception->getMessage());
                    $step->setStatus('failed') ;
                    $this->failedSteps[] = $step;
                } else {
                    $step->setStatus('passed') ;
                    $this->passedSteps[] = $step;
                }
            }         
        }

        $this->currentScenario->addStep($step);
        
        //path displayed only if available (it's not available in undefined steps)
        $strPath = '' ;
        if ($step->getDefinition() !== NULL ) {
            $strPath = $step->getDefinition()->getPath() ;
        } 

        $print = '
                    <li class="'.$step->getStatus().'">
                        <div class="step">
                            <span class="keyword">' . $step->getKeyWord() . ' </span>
                            <span class="text">' . $step->getText() . ' </span>
                            <span class="path">' . $strPath . '</span>
                        </div>' ;
        if (!empty($step->getException())) {
            $print .= '
                        <pre class="backtrace">' . $step->getException() . '</pre>' ;
        }
        $print .=  '
                    </li>';
        
        $this->printer->writeln($print);
    }

    /**
     * @param $text
     */
    public function printText($text)
    {
        file_put_contents('php://stdout', $text);
    }
    
    
    /**
     * WIP : to include a selected CSS file
     *
     * @param $file
     */
    public function getCSS($file = '') {
    
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
                    font-size:16px;
                    overflow:hidden;
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
                    overflow:hidden;
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
                    height: 70px;
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
            </style>" ;
    
    }
    
    /**
     * WIP : to include a selected JS file
     *
     * @param $file
     */    
    public function getJS($file = '') {
    
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
        </script>" ;
    
    }
}
