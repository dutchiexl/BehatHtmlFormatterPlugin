<?php
/**
 * Base renderer for Behat report.
 *
 * @author DaSayan <glennwall@free.fr>
 */

namespace emuse\BehatHTMLFormatter\Renderer;

class BaseRenderer
{
    /**
     * @var : List of the renderer names
     */
    private $nameList;

    /**
     * @var : List of the renderer objects
     */
    private $rendererList;

    /**
     * Constructor : load the renderers.
     *
     * @param string : list of the renderer
     * @param string : base_path
     */
    public function __construct($renderer, $base_path)
    {
        $rendererList = explode(',', $renderer);

        $this->nameList = array();
        $this->rendererList = array();

        //let's load the renderer dynamically
        foreach ($rendererList as $renderer) {
            $this->nameList[] = $renderer;
            if (in_array($renderer, array('Behat2', 'Twig', 'Minimal'))) {
                $className = __NAMESPACE__.'\\'.$renderer.'Renderer';
            } else {
                $className = $renderer;
            }
            $this->rendererList[$renderer] = new $className();
        }
    }

    /**
     * Return the list of the name of the renderers.
     *
     * @return array
     */
    public function getNameList()
    {
        return $this->nameList;
    }

    /**
     * Renders before an exercice.
     *
     * @param object : BehatHTMLFormatter object
     *
     * @return string : HTML generated
     */
    public function renderBeforeExercise($obj)
    {
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeExercise($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterExercise($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeSuite($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterSuite($obj);
        }

        return $print;
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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeFeature($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterFeature($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeScenario($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterScenario($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeOutline($obj);
        }

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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterOutline($obj);
        }

        return $print;
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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderBeforeStep($obj);
        }

        return $print;
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
        $print = array();
        foreach ($this->rendererList as $name => $renderer) {
            $print[$name] = $renderer->renderAfterStep($obj);
        }

        return $print;
    }
}
