<?php
/**
 * Created by PhpStorm.
 * User: nealv
 * Date: 05/01/15
 * Time: 14:39
 */

namespace emuse\BehatHTMLFormatter\Classes;


class Feature
{
    //<editor-fold desc="Variables">
    private $id;
    private $name;
    private $description;
    private $tags;
    private $file;
    private $failedScenarios = 0;
    private $passedScenarios = 0;
    private $scenarioCounter = 1;

    /**
     * @var Scenario[]
     */
    private $scenarios;
    //</editor-fold>

    //<editor-fold desc="Getters/Setters">
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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }

    /**
     * @param Scenario[] $scenarios
     */
    public function setScenarios($scenarios)
    {
        $this->scenarios = $scenarios;
    }

    /**
     * @param $scenario Scenario
     */
    public function addScenario($scenario)
    {
        $scenario->setId($this->scenarioCounter);
        $this->scenarioCounter++;
        $this->scenarios[] = $scenario;
    }

    /**
     * @return mixed
     */
    public function getFailedScenarios()
    {
        return $this->failedScenarios;
    }

    /**
     * @param mixed $failedScenarios
     */
    public function setFailedScenarios($failedScenarios)
    {
        $this->failedScenarios = $failedScenarios;
    }

    public function addFailedScenario($number = 1)
    {
        $this->failedScenarios++;
    }

    /**
     * @return mixed
     */
    public function getPassedScenarios()
    {
        return $this->passedScenarios;
    }

    /**
     * @param mixed $passedScenarios
     */
    public function setPassedScenarios($passedScenarios)
    {
        $this->passedScenarios = $passedScenarios;
    }

    public function addPassedScenario($number = 1)
    {
        $this->passedScenarios++;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    //</editor-fold>

    //<editor-fold desc="Function">
    public function allPassed()
    {
        if ($this->failedScenarios == 0) {
            return true;
        }
        return false;
    }

    public function getPassedClass()
    {
        if ($this->allPassed()) {
            return "passed";
        }
        return "failed";
    }

    public function getPercentPassed()
    {
        return ($this->getPassedScenarios() / ($this->getTotalAmountOfScenarios())) * 100;
    }

    public function getPercentFailed()
    {
        return ($this->getFailedScenarios() / ($this->getTotalAmountOfScenarios())) * 100;
    }

    public function getTotalAmountOfScenarios()
    {
        return $this->getPassedScenarios() + $this->getFailedScenarios();
    }
    //</editor-fold>
}
