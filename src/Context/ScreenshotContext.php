<?php

namespace emuse\BehatHTMLFormatter\Context;

use Behat\MinkExtension\Context\RawMinkContext;

class ScreenshotContext extends RawMinkContext
{
    private $currentScenario;
    private $screenshotDir;

    public function __construct($screenshotDir)
    {
        $this->screenshotDir = $screenshotDir;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function setUpTestEnvironment($scope)
    {
        $this->currentScenario = $scope->getScenario();
    }

    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function afterStep($scope)
    {
        // if test is passed, skip taking screenshot
        if ($scope->getTestResult()->isPassed()) {
            return;
        }

        // create filename string
        $featureFolder = preg_replace('/\W/', '', $scope->getFeature()->getTitle());

        $scenarioName = $this->currentScenario->getTitle();
        $fileName = preg_replace('/\W/', '', $scenarioName).'.png';

        // create screenshots directory if it doesn't exist
        if (!file_exists($this->screenshotDir.'/'.$featureFolder)) {
            mkdir($this->screenshotDir.'/'.$featureFolder, 0777, true);
        }

        $this->saveScreenshot($fileName, $this->screenshotDir.'/'.$featureFolder.'/');
    }
}
