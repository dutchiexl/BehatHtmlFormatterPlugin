<?php

namespace emuse\BehatHTMLFormatter\Classes;

class Suite
{
    private $name;
    private $features;

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
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param mixed $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    public function addFeature($feature)
    {
        $this->features[] = $feature;
    }
}
