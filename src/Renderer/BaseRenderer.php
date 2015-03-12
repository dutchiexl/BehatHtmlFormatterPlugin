<?php
/**
 * Base renderer for Behat report
 * @author DaSayan <glennwall@free.fr>
 */

namespace emuse\BehatHTMLFormatter\Renderer ;

use emuse\BehatHTMLFormatter\Renderer\Behat2Renderer ;

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
        $rendererList = explode(',' , $renderer) ;
        foreach($rendererList as $r) {
            $className = __NAMESPACE__ . '\\' . $r . 'Renderer' ;
            $this->rendererTab[] = new $className() ; 
        }
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
}
