<?php

namespace emuse\BehatHTMLFormatter\Formatter;

use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\AfterOutlineTested;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Testwork\Counter\Memory;
use Behat\Testwork\Counter\Timer;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use emuse\BehatHTMLFormatter\Classes\Feature;
use emuse\BehatHTMLFormatter\Classes\Scenario;
use emuse\BehatHTMLFormatter\Classes\Step;
use emuse\BehatHTMLFormatter\Classes\Suite;
use emuse\BehatHTMLFormatter\Printer\FileOutputPrinter;
use emuse\BehatHTMLFormatter\Renderer\BaseRenderer;

/**
 * Class BehatHTMLFormatter.
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
     * @var
     */
    private $timer;

    /**
     * @var
     */
    private $memory;

    /**
     * @param string $outputPath where to save the generated report file
     */
    private $outputPath;

    /**
     * @param string $base_path Behat base path
     */
    private $base_path;

    /**
     * Printer used by this Formatter.
     *
     * @param $printer OutputPrinter
     */
    private $printer;

    /**
     * Renderer used by this Formatter.
     *
     * @param $renderer BaseRenderer
     */
    private $renderer;

    /**
     * Flag used by this Formatter.
     *
     * @param $print_args boolean
     */
    private $print_args;

    /**
     * Flag used by this Formatter.
     *
     * @param $print_outp boolean
     */
    private $print_outp;

    /**
     * Flag used by this Formatter.
     *
     * @param $loop_break boolean
     */
    private $loop_break;

    /**
     * @var array
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
    private $failedScenarios = array();

    /**
     * @var Scenario[]
     */
    private $pendingScenarios = array();

    /**
     * @var Scenario[]
     */
    private $passedScenarios = array();

    /**
     * @var Feature[]
     */
    private $failedFeatures = array();

    /**
     * @var Feature[]
     */
    private $passedFeatures = array();

    /**
     * @var Step[]
     */
    private $failedSteps = array();

    /**
     * @var Step[]
     */
    private $passedSteps = array();

    /**
     * @var Step[]
     */
    private $pendingSteps = array();

    /**
     * @var Step[]
     */
    private $skippedSteps = array();

    //</editor-fold>

    //<editor-fold desc="Formatter functions">

    /**
     * @param $name
     * @param $base_path
     */
    public function __construct($name, $renderer, $filename, $print_args, $print_outp, $loop_break, $base_path)
    {
        $this->name = $name;
        $this->base_path = $base_path;
        $this->print_args = $print_args;
        $this->print_outp = $print_outp;
        $this->loop_break = $loop_break;
        $this->renderer = new BaseRenderer($renderer, $base_path);
        $this->printer = new FileOutputPrinter($this->renderer->getNameList(), $filename, $base_path);
        $this->timer = new Timer();
        $this->memory = new Memory();
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
     * @return string
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * Returns formatter description.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Formatter for teamcity';
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
     * @param mixed  $value
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
     * Returns output path.
     *
     * @return string output path
     */
    public function getOutputPath()
    {
        return $this->printer->getOutputPath();
    }

    /**
     * Returns if it should print the step arguments.
     *
     * @return bool
     */
    public function getPrintArguments()
    {
        return $this->print_args;
    }

    /**
     * Returns if it should print the step outputs.
     *
     * @return bool
     */
    public function getPrintOutputs()
    {
        return $this->print_outp;
    }

    /**
     * Returns if it should print scenario loop break.
     *
     * @return bool
     */
    public function getPrintLoopBreak()
    {
        return $this->loop_break;
    }

    public function getTimer()
    {
        return $this->timer;
    }

    public function getMemory()
    {
        return $this->memory;
    }

    public function getSuites()
    {
        return $this->suites;
    }

    public function getCurrentSuite()
    {
        return $this->currentSuite;
    }

    public function getFeatureCounter()
    {
        return $this->featureCounter;
    }

    public function getCurrentFeature()
    {
        return $this->currentFeature;
    }

    public function getCurrentScenario()
    {
        return $this->currentScenario;
    }

    public function getFailedScenarios()
    {
        return $this->failedScenarios;
    }

    public function getPendingScenarios()
    {
        return $this->pendingScenarios;
    }

    public function getPassedScenarios()
    {
        return $this->passedScenarios;
    }

    public function getFailedFeatures()
    {
        return $this->failedFeatures;
    }

    public function getPassedFeatures()
    {
        return $this->passedFeatures;
    }

    public function getFailedSteps()
    {
        return $this->failedSteps;
    }

    public function getPassedSteps()
    {
        return $this->passedSteps;
    }

    public function getPendingSteps()
    {
        return $this->pendingSteps;
    }

    public function getSkippedSteps()
    {
        return $this->skippedSteps;
    }

    //</editor-fold>

    //<editor-fold desc="Event functions">

    /**
     * @param BeforeExerciseCompleted $event
     */
    public function onBeforeExercise(BeforeExerciseCompleted $event)
    {
        $this->timer->start();

        $print = $this->renderer->renderBeforeExercise($this);
        $this->printer->write($print);
    }

    /**
     * @param AfterExerciseCompleted $event
     */
    public function onAfterExercise(AfterExerciseCompleted $event)
    {
        $this->timer->stop();

        $print = $this->renderer->renderAfterExercise($this);
        $this->printer->writeln($print);
    }

    /**
     * @param BeforeSuiteTested $event
     */
    public function onBeforeSuiteTested(BeforeSuiteTested $event)
    {
        $this->currentSuite = new Suite();
        $this->currentSuite->setName($event->getSuite()->getName());

        $print = $this->renderer->renderBeforeSuite($this);
        $this->printer->writeln($print);
    }

    /**
     * @param AfterSuiteTested $event
     */
    public function onAfterSuiteTested(AfterSuiteTested $event)
    {
        $this->suites[] = $this->currentSuite;

        $print = $this->renderer->renderAfterSuite($this);
        $this->printer->writeln($print);
    }

    /**
     * @param BeforeFeatureTested $event
     */
    public function onBeforeFeatureTested(BeforeFeatureTested $event)
    {
        $feature = new Feature();
        $feature->setId($this->featureCounter);
        ++$this->featureCounter;
        $feature->setName($event->getFeature()->getTitle());
        $feature->setDescription($event->getFeature()->getDescription());
        $feature->setTags($event->getFeature()->getTags());
        $feature->setFile($event->getFeature()->getFile());
        $feature->setScreenshotFolder($event->getFeature()->getTitle());
        $this->currentFeature = $feature;

        $print = $this->renderer->renderBeforeFeature($this);
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

        $print = $this->renderer->renderAfterFeature($this);
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
        $scenario->setScreenshotName($event->getScenario()->getTitle());
        $scenario->setScreenshotPath(
            $this->printer->getOutputPath().
            '/assets/screenshots/'.
            preg_replace('/\W/', '', $event->getFeature()->getTitle()).'/'.
            preg_replace('/\W/', '', $event->getScenario()->getTitle()).'.png'
        );
        $this->currentScenario = $scenario;

        $print = $this->renderer->renderBeforeScenario($this);
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
            $this->currentScenario->setPassed(true);
        } elseif (StepResult::PENDING == $event->getTestResult()->getResultCode()) {
            $this->pendingScenarios[] = $this->currentScenario;
            $this->currentFeature->addPendingScenario();
            $this->currentScenario->setPending(true);
        } else {
            $this->failedScenarios[] = $this->currentScenario;
            $this->currentFeature->addFailedScenario();
            $this->currentScenario->setPassed(false);
            $this->currentScenario->setPending(false);
        }

        $this->currentScenario->setLoopCount(1);
        $this->currentFeature->addScenario($this->currentScenario);

        $print = $this->renderer->renderAfterScenario($this);
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

        $print = $this->renderer->renderBeforeOutline($this);
        $this->printer->writeln($print);
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
            $this->currentScenario->setPassed(true);
        } elseif (StepResult::PENDING == $event->getTestResult()->getResultCode()) {
            $this->pendingScenarios[] = $this->currentScenario;
            $this->currentFeature->addPendingScenario();
            $this->currentScenario->setPending(true);
        } else {
            $this->failedScenarios[] = $this->currentScenario;
            $this->currentFeature->addFailedScenario();
            $this->currentScenario->setPassed(false);
            $this->currentScenario->setPending(false);
        }

        $this->currentScenario->setLoopCount(sizeof($event->getTestResult()));
        $this->currentFeature->addScenario($this->currentScenario);

        $print = $this->renderer->renderAfterOutline($this);
        $this->printer->writeln($print);
    }

    /**
     * @param BeforeStepTested $event
     */
    public function onBeforeStepTested(BeforeStepTested $event)
    {
        $print = $this->renderer->renderBeforeStep($this);
        $this->printer->writeln($print);
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
        $step->setResult($result);
        $step->setResultCode($result->getResultCode());

        if ($event->getStep()->hasArguments()) {
            $object = $this->getObject($event->getStep()->getArguments());
            $step->setArgumentType($object->getNodeType());
            $step->setArguments($object);
        }

        //What is the result of this step ?
        if (is_a($result, 'Behat\Behat\Tester\Result\UndefinedStepResult')) {
            //pending step -> no definition to load
            $this->pendingSteps[] = $step;
        } else {
            if (is_a($result, 'Behat\Behat\Tester\Result\SkippedStepResult')) {
                //skipped step
                /* @var ExecutedStepResult $result */
                $step->setDefinition($result->getStepDefinition());
                $this->skippedSteps[] = $step;
            } else {
                //failed or passed
                if ($result instanceof ExecutedStepResult) {
                    $step->setDefinition($result->getStepDefinition());
                    $exception = $result->getException();
                    if ($exception) {
                        if ($exception instanceof PendingException) {
                            $this->pendingSteps[] = $step;
                        } else {
                            $step->setException($exception->getMessage());
                            $this->failedSteps[] = $step;
                        }
                    } else {
                        $step->setOutput($result->getCallResult()->getStdOut());
                        $this->passedSteps[] = $step;
                    }
                }
            }
        }

        $this->currentScenario->addStep($step);

        $print = $this->renderer->renderAfterStep($this);
        $this->printer->writeln($print);
    }

    //</editor-fold>

    /**
     * @param $arguments
     */
    public function getObject($arguments)
    {
        foreach ($arguments as $argument => $args) {
            return $args;
        }
    }
}
