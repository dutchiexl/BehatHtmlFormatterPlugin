<?php

namespace emuse\BehatHTMLFormatter\Renderer;

use emuse\BehatHTMLFormatter\Formatter\BehatHTMLFormatter;
use Twig\Environment;
use \Twig\Loader\FilesystemLoader;

/**
 * Twig renderer for Behat report.
 *
 * Class TwigRenderer
 */
class TwigRenderer
{
    /**
     * Renders before an exercise.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeExercise(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after an exercise.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterExercise(BehatHTMLFormatter $obj)
    {
        $templatePath = dirname(__FILE__).'/../../templates';
        $loader = new FilesystemLoader($templatePath);
        $twig = new Environment($loader, array());
        $print = $twig->render('index.html.twig',
            array(
                'suites' => $obj->getSuites(),
                'failedScenarios' => $obj->getFailedScenarios(),
                'pendingScenarios' => $obj->getPendingScenarios(),
                'passedScenarios' => $obj->getPassedScenarios(),
                'failedSteps' => $obj->getFailedSteps(),
                'passedSteps' => $obj->getPassedSteps(),
                'skippedSteps' => $obj->getSkippedSteps(),
                'failedFeatures' => $obj->getFailedFeatures(),
                'passedFeatures' => $obj->getPassedFeatures(),
                'printStepArgs' => $obj->getPrintArguments(),
                'printStepOuts' => $obj->getPrintOutputs(),
                'printLoopBreak' => $obj->getPrintLoopBreak(),
            )
        );

        return $print;
    }

    /**
     * Renders before a suite.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeSuite(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after a suite.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterSuite(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders before a feature.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeFeature(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after a feature.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterFeature(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders before a scenario.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeScenario(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after a scenario.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterScenario(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders before an outline.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeOutline(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after an outline.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterOutline(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders before a step.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderBeforeStep(BehatHTMLFormatter $obj)
    {
        return '';
    }

    /**
     * Renders after a step.
     *
     * @param BehatHTMLFormatter $obj
     *
     * @return string : HTML generated
     */
    public function renderAfterStep(BehatHTMLFormatter $obj)
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
