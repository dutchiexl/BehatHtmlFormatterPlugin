<?php

namespace emuse\BehatHTMLFormatter\Renderer;

interface RendererInterface
{
    /**
     * Renders before an exercice.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeExercise($obj);

    /**
     * Renders after an exercice.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterExercise($obj);

    /**
     * Renders before a suite.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeSuite($obj);

    /**
     * Renders after a suite.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterSuite($obj);

    /**
     * Renders before a feature.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeFeature($obj);

    /**
     * Renders after a feature.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterFeature($obj);

    /**
     * Renders before a scenario.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeScenario($obj);

    /**
     * Renders after a scenario.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterScenario($obj);

    /**
     * Renders before an outline.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeOutline($obj);

    /**
     * Renders after an outline.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterOutline($obj);

    /**
     * Renders before a step.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeStep($obj);

    /**
     * Renders after a step.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderAfterStep($obj);

    /**
     * To include CSS.
     *
     * @return string : HTML generated
     */
    public function getCSS();

    /**
     * To include JS.
     *
     * @return string : HTML generated
     */
    public function getJS();
}
