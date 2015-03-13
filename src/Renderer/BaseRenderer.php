<?php
/**
 * Base renderer for Behat report
 * @author DaSayan <glennwall@free.fr>
 */

namespace emuse\BehatHTMLFormatter\Renderer ;

use emuse\BehatHTMLFormatter\Renderer\Behat2Renderer ;
use emuse\BehatHTMLFormatter\Renderer\TwigRenderer ;

class BaseRenderer
{

    /**
     * @var : Renderer(s) asked by config
     */
    private $rendererTab;


    public function __construct($renderer, $base_path)
    {
        //Getting the list of the renderers
        $this->rendererTab = array() ;
        $className = __NAMESPACE__ . '\\' . $renderer . 'Renderer' ;
        $this->rendererTab[0] = new $className() ;   
    }

    
    /**
     * Renders before an exercice.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderBeforeExercise($obj) {
        
        $print = $this->rendererTab[0]->renderBeforeExercise($obj) ;
        return $print ;
    }
    
    /**
     * Renders after an exercice.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderAfterExercise($obj) {
        $print = $this->rendererTab[0]->renderAfterExercise($obj) ;
        return $print ;
    }    
    
    /**
     * Renders before a suite.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderBeforeSuite($obj) {
        $print = $this->rendererTab[0]->renderBeforeSuite($obj) ;
        return $print ;
    }     

    /**
     * Renders after a suite.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */     
    public function renderAfterSuite($obj) {
        $print = $this->rendererTab[0]->renderAfterSuite($obj) ;
        return $print ;
    } 
    
    /**
     * Renders before a feature.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderBeforeFeature($obj) {
        $print = $this->rendererTab[0]->renderBeforeFeature($obj) ;
        return $print ;
    }     

    /**
     * Renders after a feature.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */     
    public function renderAfterFeature($obj) {
        $print = $this->rendererTab[0]->renderAfterFeature($obj) ;
        return $print ;
    }    

    /**
     * Renders before a scenario.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */            
    public function renderBeforeScenario($obj) {
        $print = $this->rendererTab[0]->renderBeforeScenario($obj) ;
        return $print ;
    }     

    /**
     * Renders after a scenario.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */     
    public function renderAfterScenario($obj) {
        $print = $this->rendererTab[0]->renderAfterScenario($obj) ;
        return $print ;
    }   
    
    /**
     * Renders before an outline.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */            
    public function renderBeforeOutline($obj) {
        $print = $this->rendererTab[0]->renderBeforeOutline($obj) ;
        return $print ;
    }
    
    /**
     * Renders after an outline.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */     
    public function renderAfterOutline($obj) {
        $print = $this->rendererTab[0]->renderAfterOutline($obj) ;
        return $print ;
    } 
    
    /**
     * Renders before a step.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderBeforeStep($obj) {
        $print = $this->rendererTab[0]->renderBeforeStep($obj) ;
        return $print ;
    }
    
    /**
     * Renders after a step.
     *
     * @param object   : BehatHTMLFormatter object
     * @return string  : HTML generated
     */        
    public function renderAfterStep($obj) {
        $print = $this->rendererTab[0]->renderAfterStep($obj) ;
        return $print ;
    }   
}
