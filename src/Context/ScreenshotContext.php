<?php

namespace emuse\BehatHTMLFormatter\Context;

use Behat\MinkExtension\Context\RawMinkContext;

class ScreenshotContext extends RawMinkContext
{
    private static $screenshotPath;

    public static function setScreenshotPath($screenshotPath)
    {
        self::$screenshotPath = $screenshotPath;
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
        $fileName = basename(self::$screenshotPath);
        $screenshotDir = str_replace('/' . $fileName, '', self::$screenshotPath);

        // create screenshots directory if it doesn't exist
        if (!file_exists($screenshotDir)) {
            mkdir($screenshotDir);
        }

        $this->saveScreenshot($fileName, $screenshotDir);
        $file = $screenshotDir . '/' . $fileName;
        echo file_exists($file) ? "Saved screenshot: $file" : "Cannot save screenshot: $file";
    }
}
