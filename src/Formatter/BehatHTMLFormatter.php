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
use Twig_Environment;
use Twig_Loader_Filesystem;

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
    }

    /**
     * @param AfterExerciseCompleted $event
     */
    public function onAfterExercise(AfterExerciseCompleted $event)
    {
        $this->createReport();
    }

    /**
     * @param BeforeSuiteTested $event
     */
    public function onBeforeSuiteTested(BeforeSuiteTested $event)
    {
        $this->currentSuite = new Suite();
        $this->currentSuite->setName($event->getSuite()->getName());
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
        $step->setPassed($result->isPassed());

        if ($result instanceof ExecutedStepResult) {
            $step->setDefinition($result->getStepDefinition());
            $exception = $result->getException();
            if ($exception) {
                $step->setException($exception->getMessage());
                $this->failedSteps[] = $step;
            } else {
                $this->passedSteps[] = $step;
            }
        }

        $this->currentScenario->addStep($step);
    }
    //</editor-fold>

    /**
     * Generate the final html output file from the tests results
     * and save it to the location specified in $output
     *
     * @return void
     */
    public function createReport()
    {
    
    

    /*
        $templatePath = dirname(__FILE__) . '/../../templates';
        $loader = new Twig_Loader_Filesystem($templatePath);
        $twig = new Twig_Environment($loader, array());

        $test = $twig->render('index.html.twig',
            array(
                'suites' => $this->suites,
                'failedScenarios' => $this->failedScenarios,
                'passedScenarios' => $this->passedScenarios,
                'failedSteps' => $this->failedSteps,
                'passedSteps' => $this->passedSteps,
                'failedFeatures' => $this->failedFeatures,
                'passedFeatures' => $this->passedFeatures,
            )
        );
        
        $this->printer->write($test);
       */ 
        
        //global
        $print = count($this->failedFeatures) . ' features failed of ' . (count($this->failedFeatures) + count($this->passedFeatures)) . '<br />' ;
        $print .= count($this->failedScenarios) . ' scenarios failed of ' . (count($this->failedScenarios) + count($this->passedScenarios)) . '<br />' ;
        $print .= count($this->failedSteps) . ' steps failed of ' . (count($this->failedSteps) + count($this->passedSteps)) . '<br />' ;
        
        $print .= '<br />VUE GLOBALE<br />' ;
        
        foreach($this->suites as $suite) {
            $print .= 'Suite : ' . $suite->getName() . '<br /><br />';
            
            foreach($suite->getFeatures() as $feature) {
                $print .= 'Feature ID : ' . $feature->getId() . '<br />';
                $print .= 'Feature result : ' . $feature->getPassedClass() . '<br />';
                $print .= 'Feature name : ' . $feature->getName() . '<br />';
                $print .= 'Feature description : ' . $feature->getDescription() . '<br />';
                foreach($feature->getTags() as $tag) {
                    $print .= $tag ;
                }
                if (count($feature->getTags() > 0)) {
                    $print .= '<br />' ;
                }
                if ($feature->getTotalAmountOfScenarios() > 0 ) {
                    $print .= 'Features passed :' . $feature->getPercentPassed() . '<br />' ;
                    $print .= 'Features failed :' . $feature->getPercentFailed() . '<br />' ;
                }
                $print .= '<br />' ;

            }
        }
        
        $print .= '<br />VUE DETAILLEE<br />' ;
        
        foreach($this->suites as $suite) {    
            foreach($suite->getFeatures() as $feature) {
                $print .= 'Feature ID : ' . $feature->getId() . '<br />';
                $print .= 'Feature result : ' . $feature->getPassedClass() . '<br />';
                $print .= 'Feature name : ' . $feature->getName() . '<br />';
                $print .= 'Feature description : ' . $feature->getDescription() . '<br />';
                foreach($feature->getTags() as $tag) {
                    $print .= $tag ;
                }                
                if (count($feature->getTags()) > 0) {
                    $print .= '<br />' ;
                }                
                if ($feature->getTotalAmountOfScenarios() > 0 ) {
                    $print .= 'Features passed :' . $feature->getPercentPassed() . '<br />' ;
                    $print .= 'Features failed :' . $feature->getPercentFailed() . '<br />' ;
                }
                $print .= '<br />' ;
                foreach($feature->getScenarios() as $scenario) {
                    if ($scenario->isPassed()) {
                        $print .= 'passed' ;
                    } else {
                        $print .= 'failed' ;
                    }
                    $print .= '<br />' ;
                    $print .= 'Scenario ID : ' . $scenario->getId() . '<br />';
                    $print .= 'Scenario Name : ' . $scenario->getName() . '<br />';
                    foreach($scenario->getTags() as $tag) {
                        $print .= $tag ;
                    }      
                    if (count($scenario->getTags()) > 0) {
                        $print .= '<br />' ;
                    }     
                    foreach($scenario->getSteps() as $step) {
                        if ($step->getPassed()) {
                            $print .= 'success' ;
                        } else {
                            $print .= 'danger' ;
                        }
                        $print .= '<br />' ;
                        $print .= $step->getKeyWord() . ' - ' . $step->getText() ;
                    }
                }
            }                
        }

        $this->printer->write($print);
        $this->printer->writeln('toto');
        
    }

    /**
     * @param $text
     */
    public function printText($text)
    {
        file_put_contents('php://stdout', $text);
    }
}
