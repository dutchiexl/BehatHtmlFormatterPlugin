<?php
/**
 * Created by PhpStorm.
 * User: nealv
 * Date: 05/01/15
 * Time: 14:39
 */

namespace emuse\BehatHTMLFormatter\Classes;


class Scenario
{
    /**
     * @var int
     */
    private $id;
    private $name;
    private $line;
    private $tags;
    private $loopCount;
    private $screenshotName;

    /**
     * @var bool
     */
    private $passed;

    /**
     * @var bool
     */
    private $pending;

    /**
     * @var Step[]
     */
    private $steps;
    private $screenshotPath;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getScreenshotName()
    {
        return $this->screenshotName;
    }

    public function setScreenshotName($scenarioName)
    {
        $this->screenshotName = preg_replace('/\W/', '', $scenarioName) . '.png';
    }

    /**
     * @return int
     */
    public function getLoopCount()
    {
        return $this->loopCount;
    }

    /**
     * @param int $loopCount
     */
    public function setLoopCount($loopCount)
    {
        $this->loopCount = $loopCount;
    }
    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param mixed $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return boolean
     */
    public function isPassed()
    {
        return $this->passed;
    }

    /**
     * @param boolean $passed
     */
    public function setPassed($passed)
    {
        $this->passed = $passed;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return $this->pending;
    }

    /**
     * @param boolean $pending
     */
    public function setPending($pending)
    {
        $this->pending = $pending;
    }

    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param Step[] $steps
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;
    }

    /**
     * @param Step $step
     */
    public function addStep($step)
    {
        $this->steps[] = $step;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLoopSize()
    {
        //behat
        return $this->loopCount > 0 ? sizeof($this->steps)/$this->loopCount : sizeof($this->steps);
    }

    public function setScreenshotPath($string)
    {
        $this->screenshotPath = $string;
    }

    /**
     * @return mixed
     */
    public function getScreenshotPath()
    {
        if (file_exists($this->screenshotPath)) {
            return "file://" . realpath($this->screenshotPath);
        }

        return false;
    }
}
