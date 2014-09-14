<?php

class AdminController extends Zend_Controller_Action
{

    private $user = null;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->getIdentity()->role != Application_Model_User::ADMIN) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->user = $auth->getIdentity()->role;
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $item = new Application_Model_Ad();
        $owner = new Application_Model_Partner();
        $vars = $this->getAllParams();

        if (isset($vars["id"])) {
            $item->get($vars["id"]);
            $owner->getByUserId($auth->getIdentity()->id);
            $this->view->ad = $item;
            $this->view->user = $owner;
        } else {
            $this->redirect("/admin/ready");
        }
    }

    public function _createEditLink($id, $name)
    {
        global $translate;
        if (empty($name))
            $name = $translate->getAdapter()->translate("empty_name");
        return '<a href="/ad/edit/id/' . $id . '">' . $name . '</a>';
    }

    public function _paidText($val)
    {
        global $translate;
        if ($val)
            $text = $translate->getAdapter()->translate("yes");
        else
            $text = $translate->getAdapter()->translate("no");
        return $text;
    }

    public function _daysLeft($end_dt, $public_dt)
    {
        if (strtotime($public_dt) < time())
            return ceil((strtotime($end_dt) - time()) / 86400) + 1;
        else
            return ceil((strtotime($end_dt) - strtotime($public_dt)) / 86400) + 1;
    }

    public function _changeState($val, $status)
    {
        global $translate;
        return '<a href = "' . "/ad/set-status/id/$val/status/$status" . '">' . $text = $translate->getAdapter()->translate("set_" . $status) . '</a>';
    }

    public function _getOwner($userId) {
        $user = new Application_Model_User();
        if ($user->getUser($userId)) {
            return "<a href='/partner/profile/id/$userId'>" . $user->username . "</a>";
        } else {
            return false;
        }
    }

    public function _createPreviewLink($id, $name)
    {
        global $translate;
        if (empty($name))
            $name = $translate->getAdapter()->translate("empty_name");
        return '<a href="/ad/index/id/' . $id . '">' . $name . '</a>';
    }

    public function activeAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ? AND end_dt > NOW()", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        $grid->setGridColumns(array("name", "days_left", "public_dt", "end_dt", "set_status"));
        $grid->updateColumn('name',array(
            "title" =>  $translate->getAdapter()->translate("name"),
            'callback'=>array(
                'function'=>array($this, '_createEditLink'),
                'params'=>array('{{id}}', "{{name}}")
            )
        ));
        $grid->updateColumn('public_dt',array(
            "title" =>  $translate->getAdapter()->translate("public_date"),
        ));
        $grid->updateColumn('end_dt',array(
            "title" =>  $translate->getAdapter()->translate("end_date"),
        ));

        $grid->addExtraColumn(array(
            "name" => "days_left",
            "position" => "right",
            "title" =>  $translate->getAdapter()->translate("days_left"),
            'callback'=>array(
                'function'=>array($this, '_daysLeft'),
                'params'=>array('{{end_dt}}','{{public_dt}}')
            ))
        );

        $grid->addExtraColumn(array(
                "name" => "set_status",
                "position" => "right",
                "title" =>  $translate->getAdapter()->translate("set_status"),
                'callback'=>array(
                    'function'=>array($this, '_changeState'),
                    'params'=>array('{{id}}', Application_Model_DbTable_Ad::STATUS_ARCHIVE)
                ))
        );
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function noactiveAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status IN (?)", array(Application_Model_DbTable_Ad::STATUS_DRAFT));
        $grid->setGridColumns(array("name", "public_dt", "end_dt", "owner_email"));
        $grid->updateColumn('name',array(
            "title" =>  $translate->getAdapter()->translate("name"),
            'callback'=>array(
                'function'=>array($this, '_createEditLink'),
                'params'=>array('{{id}}', "{{name}}")
            )
        ));
        $grid->updateColumn('public_dt',array(
            "title" =>  $translate->getAdapter()->translate("public_date"),
        ));
        $grid->updateColumn('end_dt',array(
            "title" =>  $translate->getAdapter()->translate("end_date"),
        ));
        $grid->addExtraColumn(array(
                "name" => "owner_email",
                "position" => "right",
                "title" =>  $translate->getAdapter()->translate("email"),
                'callback'=>array(
                    'function'=>array($this, '_getOwner'),
                    'params'=>array('{{owner}}')
                ))
        );
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function readyAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status IN (?)", array(Application_Model_DbTable_Ad::STATUS_READY));
        $grid->setGridColumns(array("name", 'paid', "public_dt", "end_dt", "set_status"));
        $grid->updateColumn('name',array(
            "title" =>  $translate->getAdapter()->translate("name"),
            'callback'=>array(
                'function'=>array($this, '_createEditLink'),
                'params'=>array('{{id}}', "{{name}}")
            )
        ));
        $grid->updateColumn('public_dt',array(
            "title" =>  $translate->getAdapter()->translate("public_date"),
        ));
        $grid->updateColumn('end_dt',array(
            "title" =>  $translate->getAdapter()->translate("end_date"),
        ));
        $grid->updateColumn('paid',array(
            "title" =>  $translate->getAdapter()->translate("paid"),
            'callback'=>array(
                'function'=>array($this, '_paidText'),
                'params'=>array('{{paid}}')
            )
        ));
        $grid->addExtraColumn(array(
            "name" => "set_status",
            "position" => "right",
            "title" =>  $translate->getAdapter()->translate("set_status"),
            'callback'=>array(
                'function'=>array($this, '_changeState'),
                'params'=>array('{{id}}', Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ))
        );

        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function archiveAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("(status = ? OR end_dt < NOW())", Application_Model_DbTable_Ad::STATUS_ARCHIVE);
        $grid->setGridColumns(array("name", "public_dt", "end_dt"));
        $grid->updateColumn('name',array(
            "title" =>  $translate->getAdapter()->translate("name"),
            'callback'=>array(
                'function'=>array($this, '_createEditLink'),
                'params'=>array('{{id}}', "{{name}}")
            )
        ));
        $grid->updateColumn('public_dt',array(
            "title" =>  $translate->getAdapter()->translate("public_date"),
        ));
        $grid->updateColumn('end_dt',array(
            "title" =>  $translate->getAdapter()->translate("end_date"),
        ));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function favoritesAction()
    {
        global $translate;
        if (!empty($this->user->favorites_ads)) {
            $grid = Bvb_Grid::factory('Table');
            $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
            $grid->setSource($source);
            $grid->getSelect()->where("status = ? AND id IN (" . $this->user->favorites_ads . ")", Application_Model_DbTable_Ad::STATUS_ACTIVE);
            $grid->setGridColumns(array("name", "geo_name", "public_dt", "end_dt"));
            $grid->updateColumn('name',array(
                "title" =>  $translate->getAdapter()->translate("name"),
                'callback'=>array(
                    'function'=>array($this, '_createPreviewLink'),
                    'params'=>array('{{id}}', "{{name}}")
                )
            ));
            $grid->updateColumn('public_dt',array(
                "title" =>  $translate->getAdapter()->translate("public_date"),
            ));
            $grid->updateColumn('end_dt',array(
                "title" =>  $translate->getAdapter()->translate("end_date"),
            ));
            $grid->updateColumn('geo_name',array(
                "title" =>  $translate->getAdapter()->translate("geo_name"),
            ));
            $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
            $grid->setNoFilters(true);
            $grid->setExport(array());
            $grid->setImagesUrl('/img/');
            $this->view->grid = $grid;
        }
    }

    public function getfullinfoAction()
    {
        $vars = $this->getAllParams();
        $item = new Application_Model_Ad();
        $item->get((int)$vars["id"]);
        echo nl2br($item->full_description);
        exit();
    }

    public function geoEditAction()
    {
        $geoEditForm = new Application_Form_GeoEdit();
        $this->view->geoEditForm = $geoEditForm;
    }
}