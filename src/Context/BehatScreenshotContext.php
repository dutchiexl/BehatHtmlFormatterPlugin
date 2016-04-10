<?php

namespace cckakhandki\BehatHTMLFormatter\Context;

use Behat\Behat\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;


/**
 * Class BehatFormatterContext
 *
 * 
 */
class BehatScreenshotContext implements SnippetAcceptingContext {
    private $currentScenario;
    protected static $currentSuite;
    private $screenshot_path;
    private $screenshotName;
    /** 
     * @var \Drupal\DrupalExtension\Context\MinkContext 
     */
    private $minkContext;
    
    /**
     * 
     * @param string $screenshot_path
     */
    public function __construct($screenshot_path) {
    	//var_dump($params);
    	$this->screenshot_path = $screenshot_path;
    }
    
	/** @BeforeScenario */
 	public function gatherContexts(BeforeScenarioScope $scope)
 	{
 		$environment = $scope->getEnvironment();
	
 		$this->minkContext = $environment-> getContext('Drupal\DrupalExtension\Context\MinkContext');
 	}
 	
    /**
     * Creates filename string.
     *     
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     *
     */
    public function setUpScreenshotName(BeforeScenarioScope $scope) {
    	$this->currentScenario = $scope->getScenario();
    	
    	$scenarioName = $this->currentScenario->getTitle() . '-' . $this->currentScenario->getLine();
    	$this->screenshotName = str_replace(' ', '', str_replace(str_split('\\/:*?"<>|'), '', $scenarioName)) . '.png';
    }
    
    /**
     * @return string $screenshotName 
     */
    public function getScreesnhotName(){
    	return $this->screenshotName;
    }
    
    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function afterStep($scope) {
    	
    	// If test has failed, get a screenshot.
    	if(!$scope->getTestResult()->isPassed())
    	{
    		// Get Feturte title
    		$featureFolder = str_replace(' ', '', str_replace(str_split('\\/:*?"<>|'), '', $scope->getFeature()->getTitle()));
    		
    		//create screenshots directory if it doesn't exist
    		if (!file_exists($this->screenshot_path . DIRECTORY_SEPARATOR . $featureFolder)) {
    			mkdir($this->screenshot_path . DIRECTORY_SEPARATOR . $featureFolder, 0777, true);
    		}
    		
    		//take screenshot and save as the previously defined filename
    		$this->minkContext->saveScreenshot($this->screenshotName, $this->screenshot_path . DIRECTORY_SEPARATOR . $featureFolder);
//     		$ss = "ss saved at: " . $this->screenshot_path . DIRECTORY_SEPARATOR . $featureFolder . DIRECTORY_SEPARATOR . $this->screenshotName;
//     		var_dump($ss);
    	}
    }
    
}