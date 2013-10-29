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
        global $translate;
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

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

        $formsOrder = array(
            'AdMain' => "dates",
            'AdDates' => "settings",
            'AdSettings' => "contacts",
            'AdContacts' => "media"
        );

        $item = new Application_Model_Ad();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            if ($formData["id"])
                $item->get($formData["id"]);
            $form = $forms[$formData["form"]];
            if ($form->isValid($formData)) {
                if ($formData["form"] == "AdMedia") {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $images = $this->_processImage($upload);
                }

                $itemData = $form->getValues();
                if (sizeof($images)) {
                    foreach ($images as $imgKey => $imgVal) {
                        switch ($imgKey) {
                            case "image_file" :
                                $itemData["image"] = $imgVal;
                                break;

                            case "banner_file" :
                                $itemData["banner"] = $imgVal;
                                break;
                        }
                    }
                }

                $itemData["owner"] = $this->user->id;
                $item->load($itemData);
                $id = $item->save();
                if ($id) {
                    $url = $this->_helper->url('edit', 'ad', null, array("id" => $item->id));
                    if ($formData["form"] != "AdMain")
                        $url .= '#main';
                    else
                        $url .= '#' . $formsOrder[$formData["form"]];
                    $this->redirect($url);
                    $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                } else {
                    $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                }
            } else {
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        }

        $data = $item->toArray();
        foreach ($forms as $form) {
            $form->populate($data);
        }
    }

    private function _processImage($upload)
    {
        $upload->addValidator('Size', false, array('max' => "5MB"));
        $upload->addValidator('MimeType', false, array('image/gif', 'image/jpeg', 'image/png'));

        $resArray = array();
        $files = $upload->getFileInfo();
        foreach ($files as $file => $info) {
            if (!$upload->isValid($file)) {
                continue;
            }

            $newName = uniqid() . "_" . $info['name'];
            $upload->setDestination(APPLICATION_PATH . "/../public/ads");
            $upload->addFilter('Rename', APPLICATION_PATH . "/../public/ads" . DIRECTORY_SEPARATOR . $newName);

            try {
                $upload->receive($file);
            } catch (Zend_File_Transfer_Exception $e) {
                $e->getMessage();
                return false;
            }

            $resArray[$file] = $newName;
        }

        return $resArray;
    }

    public function editAction()
    {
        global $translate;
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

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

        $formsOrder = array(
            'AdMain' => "dates",
            'AdDates' => "settings",
            'AdSettings' => "contacts",
            'AdContacts' => "media"
        );

        $item = new Application_Model_Ad();
        $formData = $this->getAllParams();
        if ($formData["id"])
            $item->get($formData["id"]);
        $this->view->image = $item->image;
        $this->view->banner = $item->banner;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getAllParams();
            $form = $forms[$formData["form"]];
            if ($form->isValid($formData)) {
                if ($formData["form"] == "AdMedia") {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $images = $this->_processImage($upload);
                }

                $itemData = $form->getValues();
                if (sizeof($images)) {
                    foreach ($images as $imgKey => $imgVal) {
                        switch ($imgKey) {
                            case "image_file" :
                                $itemData["image"] = $imgVal;
                                break;

                            case "banner_file" :
                                $itemData["banner"] = $imgVal;
                                break;
                        }
                    }
                }

                $itemData["owner"] = $this->user->id;
                $item->load($itemData);
                $id = $item->save();
                if ($item->id) {
                    $url = $this->_helper->url->url(array(
                        'controller' => 'ad',
                        'action' => 'edit'
                    ));
                    $url .= '#' . $formsOrder[$vars["form"]];
                    $this->_helper->redirector->gotoUrl($url);
                    $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                } else {
                    $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                }
            } else {
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        }

        $data = $item->toArray();
        foreach ($forms as $form) {
            $form->populate($data);
        }
    }

    public function _createEditLink($id, $name)
    {
        if (empty($name))
            $name = "Empty name";
        return '<a href="/ad/edit/id/' . $id . '">' . $name . '</a>';
    }

    private function _cropImage($image, $targetWidth, $targetHeight)
    {
        list($width, $height, $type) = getimagesize($image);
        $types = array("", "gif", "jpeg", "png");
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img_i = $func($image);
        }
        $img_o = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopy($img_o, $img_i, 0, 0, 0, 0, $targetWidth, $targetHeight);
        $func = 'image'.$ext;
        $newName = uniqid() . "." . $ext;
        return $func($img_o, "/ads/" . $newName);

    }

    public function activeAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt"));
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
        $grid->updateColumn('start_dt',array(
            "title" =>  $translate->getAdapter()->translate("start_date"),
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

    public function noactiveAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status IN (?, ?)", array(Application_Model_DbTable_Ad::STATUS_DRAFT, Application_Model_DbTable_Ad::STATUS_READY));
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt"));
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
        $grid->updateColumn('start_dt',array(
            "title" =>  $translate->getAdapter()->translate("start_date"),
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

    public function archiveAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ?", Application_Model_DbTable_Ad::STATUS_ARCHIVE);
        $grid->setGridColumns(array("name", "public_dt", "start_dt", "end_dt"));
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
        $grid->updateColumn('start_dt',array(
            "title" =>  $translate->getAdapter()->translate("start_date"),
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

    public function getfullinfoAction()
    {
        $vars = $this->getAllParams();
        $item = new Application_Model_Ad();
        $item->get((int)$vars["id"]);
        echo $item->full_description;
        exit();
    }


}









