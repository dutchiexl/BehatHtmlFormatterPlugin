<?php
/**
 * Behat2 renderer for Behat report.
 *
 * @author DaSayan <glennwall@free.fr>
 */

namespace emuse\BehatHTMLFormatter\Renderer;

class MinimalRenderer
{
    private $extension = 'csv';

    public function __construct()
    {
    }

    public function getExtension($renderer)
    {
        return $this->rendererList[$renderer]->getExtension();
    }

    /**
     * Renders before an exercice.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeExercise($obj)
    {
        return '';
    }

    /**
     * Renders after an exercice.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterExercise($obj)
    {
        $strFeatPassed = count($obj->getPassedFeatures());
        $strFeatFailed = count($obj->getFailedFeatures());
        $strScePassed = count($obj->getPassedScenarios());
        $strScePending = count($obj->getPendingScenarios());
        $strSceFailed = count($obj->getFailedScenarios());
        $strStepsPassed = count($obj->getPassedSteps());
        $strStepsPending = count($obj->getPendingSteps());
        $strStepsSkipped = count($obj->getSkippedSteps());
        $strStepsFailed = count($obj->getFailedSteps());

        $featTotal = (count($obj->getFailedFeatures()) + count($obj->getPassedFeatures()));
        $sceTotal = (count($obj->getFailedScenarios()) + count($obj->getPendingScenarios()) + count($obj->getPassedScenarios()));
        $stepsTotal = (count($obj->getFailedSteps()) + count($obj->getPassedSteps()) + count($obj->getSkippedSteps()) + count($obj->getPendingSteps()));

        $print = $featTotal.','.$strFeatPassed.','.$strFeatFailed."\n";
        $print .= $sceTotal.','.$strScePassed.','.$strScePending.','.$strSceFailed."\n";
        $print .= $stepsTotal.','.$strStepsPassed.','.$strStepsFailed.','.$strStepsSkipped.','.$strStepsPending."\n";
        $print .= $obj->getTimer().','.$obj->getMemory()."\n";

        return $print;
    }

    /**
     * Renders before a suite.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeSuite($obj)
    {
        return '';
    }

    /**
     * Renders after a suite.
     *
     * @param object   : BehatHTMLFormatter object
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
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeFeature($obj)
    {
        return '';
    }

    /**
     * Renders after a feature.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterFeature($obj)
    {
        return '';
    }

    /**
     * Renders before a scenario.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeScenario($obj)
    {
        return '';
    }

    /**
     * Renders after a scenario.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterScenario($obj)
    {
        return '';
    }

    /**
     * Renders before an outline.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeOutline($obj)
    {
        return '';
    }

    /**
     * Renders after an outline.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterOutline($obj)
    {
        return '';
    }

    /**
     * Renders before a step.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeStep($obj)
    {
        return '';
    }

    /**
     * Renders after a step.
     *
     * @param object   : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterStep($obj)
    {
        return '';
    }

    /**
     * To include CSS.
     *
     * @return string : HTML generated
     */
    public function getCSS()
    {
        return '';
    }

    /**
     * To include JS.
     *
     * @return string : HTML generated
     */
    public function getJS()
    {
        return '';
    }
}
