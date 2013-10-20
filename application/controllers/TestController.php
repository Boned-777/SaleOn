<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ?", Application_Model_DbTable_Ad::STATUS_DRAFT);
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt"));
        $grid->updateColumn('field',array('class'=>'my_css_class'));
        $grid->updateColumn("name", array("title" => "Ad Name"));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;

	}
}

