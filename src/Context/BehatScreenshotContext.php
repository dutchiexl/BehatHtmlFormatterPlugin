<?php

namespace cckakhandki\BehatHTMLFormatter\Context;

use Behat\Behat\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Class BehatFormatterContext
 */
class BehatScreenshotContext implements SnippetAcceptingContext {
    private $currentScenario;
    protected static $currentSuite;
    private $output_path;
    private $screenshotName;
    
    /** 
     * @var \Drupal\DrupalExtension\Context\MinkContext 
     */
    private $minkContext;
    
    /**
     * @param string $screenshot_path
     */
    public function __construct($screenshot_path) {
    	$this->output_path = $screenshot_path;
    }
    
	/** @BeforeScenario */
 	public function gatherContexts(BeforeScenarioScope $scope)
 	{
 		$environment = $scope->getEnvironment();
	
 		$this->minkContext = $environment-> getContext('Drupal\DrupalExtension\Context\MinkContext');
 	}
    
    /**
     * @Then /^I take screenshot of current page$/
     */
    public function getScreenshot($return = FALSE)
    {
    	if (!is_dir($this->output_path)){
    		mkdir($this->output_path, 0755, TRUE);
    	}
    	$url = $this->minkContext->getSession()->getCurrentUrl();
    	$browser = $this->minkContext->getMinkParameter('browser_name');
    	$fileName = $browser . '-' . date('Y-m-d-H-i-s') . '.png';
    
    	$this->minkContext->saveScreenshot($fileName, $this->output_path);
    	$path = $this->output_path.'/'.$fileName;
    	print "screenshot taken at : " . $path;
    	
    	if($return)
    		return $fileName;
    }
    
}