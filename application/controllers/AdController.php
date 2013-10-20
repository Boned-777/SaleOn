<?php

class AdController extends Zend_Controller_Action
{

    private $user = null;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }

        $this->user = $auth->getIdentity();
    }

    public function indexAction()
    {
        $item = new Application_Model_Ad();
        $owner = new Application_Model_Partner();
        $vars = $this->getAllParams();

        if (isset($vars["id"])) {
            $item->get($vars["id"]);
            $owner->get(2);
            $this->view->ad = $item;
            $this->view->user = $owner;
        } else {
            $this->redirect("/index/index");
        }
    }

    public function newAction()
    {
        $this->view->mainForm = new Application_Form_AdMain();
        $this->view->contactsForm = new Application_Form_AdContacts();
        $this->view->datesForm = new Application_Form_AdDates();
        $this->view->settingsForm = new Application_Form_AdSettings();
        $this->view->mediaForm = new Application_Form_AdMedia();

        $forms = array(
            "AdMain" => $this->view->mainForm,
            "AdContacts" => $this->view->contactsForm,
            "AdDates" => $this->view->datesForm,
            "AdSettings" => $this->view->settingsForm,
            "AdMedia" => $this->view->mediaForm
        );

        $item = new Application_Model_Ad();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            if ($formData["id"])
                $item->get($formData["id"]);
            $form = $forms[$formData["form"]];
            if ($form->isValid($formData)) {
                $image = "";
                if ($formData["form"] == "AdMedia") {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $image = $this->_processImage($upload);
                }

                $itemData = $form->getValues();
                if (!empty($image)) {
                    $itemData["image"] = $image;
                    $itemData["banner"] = $image;
                }
                $itemData["owner"] = $this->user->id;
                $item->load($itemData);
                $id = $item->save();
                if ($id) {
                    $this->_helper->redirector('edit', 'ad', null, array("id" => $item->id));
                } else {

                }
            }
        }

        $data = $item->toArray();
        foreach ($forms as $form) {
            $form->populate($data);
        }
    }

    private function _processImage($upload)
    {
        //$upload->addValidator('Size', false, array('max' => "90MB"));
        $upload->addValidator('MimeType', false, array('image/gif', 'image/jpeg', 'image/png'));

        $files = $upload->getFileInfo("img");
        foreach ($files as $file => $info) {
            if (!$upload->isValid()) {
                print_r($upload->getMessages()); die("out");
                return false;
            }

            $newName = uniqid() . "_" . $info['name'];
            $upload->setDestination(APPLICATION_PATH . "/../public/ads");
            $upload->addFilter('Rename', APPLICATION_PATH . "/../public/ads" . DIRECTORY_SEPARATOR . $newName);

            try {
                $upload->receive('img');
            } catch (Zend_File_Transfer_Exception $e) {
                $e->getMessage();
                return false;
            }
            return $newName;
        }
    }

    public function editAction()
    {
        $vars = $this->getAllParams();

        $this->view->mainForm = new Application_Form_AdMain();
        $this->view->contactsForm = new Application_Form_AdContacts();
        $this->view->datesForm = new Application_Form_AdDates();
        $this->view->settingsForm = new Application_Form_AdSettings();
        $this->view->mediaForm = new Application_Form_AdMedia();

        $forms = array(
            "AdMain" => $this->view->mainForm,
            "AdContacts" => $this->view->contactsForm,
            "AdDates" => $this->view->datesForm,
            "AdSettings" => $this->view->settingsForm,
            "AdMedia" => $this->view->mediaForm
        );

        $item = new Application_Model_Ad();
        $formData = $this->getAllParams();
        if ($formData["id"])
            $item->get($formData["id"]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getAllParams();
            if ($formData["id"])
                $item->get($formData["id"]);
            $form = $forms[$formData["form"]];
            if ($form->isValid($formData)) {
                $image = "";
                if ($formData["form"] == "AdMedia") {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $image = $this->_processImage($upload);
                }

                $itemData = $form->getValues();
                if (!empty($image)) {
                    $itemData["image"] = $image;
                    $itemData["banner"] = $image;
                }
                $itemData["owner"] = $this->user->id;
                $item->load($itemData);
                $id = $item->save();
                if ($item->id) {
                    $this->_helper->redirector('edit', 'ad', null, array("id" => $item->id));
                } else {

                }
            }
        }

        $data = $item->toArray();
        foreach ($forms as $form) {
            $form->populate($data);
        }
    }

    public function activeAction()
    {
        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt", "status"));
        $grid->updateColumn('field',array('class'=>'my_css_class'));
        $grid->updateColumn("name", array("title" => "Ad Name"));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function noactiveAction()
    {
        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status IN (?, ?)", array(Application_Model_DbTable_Ad::STATUS_DRAFT, Application_Model_DbTable_Ad::STATUS_READY));
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt", "status"));
        $grid->updateColumn('field',array('class'=>'my_css_class'));
        $grid->updateColumn("name", array("title" => "Ad Name"));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }

    public function archiveAction()
    {
        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ?", Application_Model_DbTable_Ad::STATUS_ARCHIVE);
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt", "status"));
        $grid->updateColumn('field',array('class'=>'my_css_class'));
        $grid->updateColumn("name", array("title" => "Ad Name"));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');
        $this->view->grid = $grid;
    }
}







