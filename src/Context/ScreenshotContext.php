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

        $this->currentScenario->setScreenshotPath( 'data:image/jpeg;base64,' . base64_encode($this->getSession()->getScreenshot()));
    }
}
