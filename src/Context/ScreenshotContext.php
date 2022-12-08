<?php

namespace emuse\BehatHTMLFormatter\Context;

use Behat\MinkExtension\Context\RawMinkContext;

class ScreenshotContext extends RawMinkContext
{
    private $currentScenario;
    private $currentFeature;
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
        $this->currentFeature = $scope->getFeature();
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
        $featureFolder = preg_replace('/\W/', '', $this->currentFeature->getTitle());
        $fileName = preg_replace('/\W/', '', $this->currentScenario->getTitle()).'.png';
        $screenshotDir = $this->screenshotDir.'/'.$featureFolder;

        // create screenshots directory if it doesn't exist
        if (!file_exists($screenshotDir)) {
            mkdir($screenshotDir);
        }

        $this->saveScreenshot($fileName, $screenshotDir);
        $file =  $screenshotDir . '/' . $fileName;
        echo file_exists($file) ? "Saved screenshot: $file" : "Cannot save screenshot: $file";
    }
}
