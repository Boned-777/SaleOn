<?php
class Application_Model_Category extends Application_Model_FilterMapper
{
    public function getName() {
        global $translate;
        return $translate->getAdapter()->translate($this->name);
    }
}
