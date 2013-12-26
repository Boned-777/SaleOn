<?php

class AdController extends Zend_Controller_Action
{

    private $user = null;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            //$this->_helper->redirector('index', 'auth');
        }

        $this->user = $auth->getIdentity();
        $vars = $this->getAllParams();

        if (($vars["action"] == "set-status") && ($this->user->role !== Application_Model_User::ADMIN)) {
            $this->_helper->redirector('index', 'index');
        }
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
            'AdContacts' => "media",
            'AdMedia' => "main"
        );

        $item = new Application_Model_Ad();
        $request = $this->getRequest();
        $geoItem = new Application_Model_Geo();

        $partner = new Application_Model_Partner();
        $partner->getByUserId($this->user->id);
        $partnerData = $partner->toArray();
        unset($partnerData["id"]);
        $item->loadIfEmpty($partnerData);

        if ($this->_getParam('geo'))
            $geoVal = $this->_getParam('geo');
        if (empty($geoVal)) {
            $geoVal = "1.0.0";
        }
        $geoVals = explode(".", $geoVal);
        if (!isset($geoVals[1])) {
            $geoVals[1] = 0;
        }
        if (!isset($geoVals[2])) {
            $geoVals[2] = 0;
        }

        $this->view->settingsForm->getElement("region")->setMultiOptions($geoItem->getAll($geoVals[0]));
        $this->view->settingsForm->getElement("region")->setValue($geoVals[0].'.'.$geoVals[1]);

        $this->view->settingsForm->getElement("district")->setMultiOptions($geoItem->getAll($geoVals[0].'.'.$geoVals[1]));
        $this->view->settingsForm->getElement("district")->setValue($geoVals[0].'.'.$geoVals[1].'.'.$geoVals[2]);

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

                if ($formData["form"] == "AdSettings") {
                    if ((!empty($formData["brand_name"]))&(!$formData["brand"])) {
                        $brand = new Application_Model_DbTable_Brand();
                        $brand_res = $brand->save(array(
                            "name" => $formData["brand_name"]
                        ));
                    }
                    if ($brand_res) {
                        $formData["brand"] = $brand_res;
                    }
                }

                $itemData = $form->getValues();
                if (sizeof($images)) {
                    foreach ($images as $imgKey => $imgVal) {
                        switch ($imgKey) {
                            case "image_file" :
                                if (!isset($images["banner_file"])) {
                                    $itemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/ads/" . $imgVal, 240, 153);
                                }
                                $itemData["image"] = $imgVal;
                                break;

                            case "banner_file" :
                                $itemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/ads/" . $imgVal, 240, 153);
                                break;
                        }
                    }
                }
                $itemData["geo_name"] = $geoItem->getFullGeoName($geoVal);
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
                $tabs = explode("Ad", $formData["form"]);
                $this->view->gotoTab = strtolower($tabs[1]);
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        }

        $data = $item->toArray();
        foreach ($forms as $form) {
            $form->populate($data);
        }
        if (isset($formData["form"]))
            $forms[$formData["form"]]->populate($formData);
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

    public function listAction () {
        global $translate;
        $ad = new Application_Model_Ad();
        $res = $ad->getList("");
        $data = array();
        foreach ($res AS $val) {
            $data[] = $val->toListArray($this->user);
        }
        $res = array(
            "list" => $data,
            "options" => array(
                "days_left_text" => $translate->getAdapter()->translate("days_left")
            )
        );
        $this->_helper->json($res);
    }

    public function setStatusAction () {
        global $translate;

        $vars = $this->getAllParams();
        $ad = new Application_Model_Ad();
        if ($ad->get($vars["id"])){
            eval("\$ad->status = Application_Model_DbTable_Ad::STATUS_" . $vars["status"] . ";");
            $ad->save();
            $this->_helper->json(array("success" => true));
        } else {
            $this->_helper->json(array("success" => false));
        };
    }

    public function editAction()
    {
        global $translate;
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

        $vars = $this->getAllParams();

        $item = new Application_Model_Ad();
        $formData = $this->getAllParams();
        if ($formData["id"])
            $item->get($formData["id"]);

        $isReady = $item->isValid();

        $partner = new Application_Model_Partner();
        $partner->getByUserId($this->user->id);
        $partnerData = $partner->toArray();
        unset($partnerData["id"]);
        $item->loadIfEmpty($partnerData);

        $this->view->mainForm = new Application_Form_AdMain(array("isReady" => $isReady));
        $this->view->contactsForm = new Application_Form_AdContacts(array("isReady" => $isReady));
        $this->view->datesForm = new Application_Form_AdDates(array("isReady" => $isReady));
        $this->view->settingsForm = new Application_Form_AdSettings(array("isReady" => $isReady));
        $this->view->mediaForm = new Application_Form_AdMedia(array("isReady" => $isReady));

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
            'AdContacts' => "media",
            'AdMedia' => "main"
        );

        $this->view->image = $item->image;
        $this->view->banner = $item->banner;
        $request = $this->getRequest();
        $geoItem = new Application_Model_Geo();
        $geoVal = $item->geo;
        if ($this->_getParam('geo'))
            $geoVal = $this->_getParam('geo');
        if (empty($geoVal)) {
            $geoVal = "1.0.0";
        }
        $geoVals = explode(".", $geoVal);
        if (!isset($geoVals[1])) {
            $geoVals[1] = 0;
        }
        if (!isset($geoVals[2])) {
            $geoVals[2] = 0;
        }

        $this->view->settingsForm->getElement("region")->setMultiOptions($geoItem->getAll($geoVals[0]));
        $this->view->settingsForm->getElement("region")->setValue($geoVals[0].'.'.$geoVals[1]);

        $this->view->settingsForm->getElement("district")->setMultiOptions($geoItem->getAll($geoVals[0].'.'.$geoVals[1]));
        $this->view->settingsForm->getElement("district")->setValue($geoVals[0].'.'.$geoVals[1].'.'.$geoVals[2]);

        $this->view->ad = $item;
        if ($request->isPost()) {
            $formData = $this->getAllParams();
            if (isset($formData["district"])) {
                if (sizeof(explode(".", $formData["district"])) == 2)
                    $formData["district"] .= ".0";
            }
            $form = $forms[$formData["form"]];
            $mediaItemData = array();
            if ($form->isValid($formData)) {
                if ($formData["form"] == "AdMedia") {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $images = $this->_processImage($upload);

                    if (sizeof($images)) {
                        foreach ($images as $imgKey => $imgVal) {
                            switch ($imgKey) {
                                case "image_file" :
                                    if (!isset($images["banner_file"])) {
                                        $mediaItemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/ads/" . $imgVal, 240, 153);
                                    }
                                    $mediaItemData["image"] = $imgVal;
                                    break;

                                case "banner_file" :
                                    $mediaItemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/ads/" . $imgVal, 240, 153);
                                    break;
                            }
                        }
                    }
                }

                $itemData = $form->getValues();
                $itemData = array_merge($mediaItemData, $itemData);
                if ($formData["form"] == "AdSettings") {
                    if ((!empty($formData["brand_name"])) && (!$formData["brand"])) {
                        $brand = new Application_Model_DbTable_Brand();
                        $brand_res = $brand->save(array(
                            "name" => $formData["brand_name"]
                        ));
                        if ($brand_res) {
                            $itemData["brand_name"] = $formData["brand_name"];
                            $itemData["brand"] = $brand_res;
                        }
                    }

                    if ((!empty($formData["product_name"])) && (!$formData["product"])) {
                        $product = new Application_Model_DbTable_Product();
                        $product_res = $product->save(array(
                            "name" => $formData["product_name"]
                        ));
                        if ($product_res) {
                            $itemData["product_name"] = $formData["product_name"];
                            $itemData["product"] = $product_res;
                        }
                    }

                    $itemData["geo_name"] = $geoItem->getFullGeoName($geoVal);
                }

                if ($isReady) {
                    $item->status = Application_Model_DbTable_Ad::STATUS_READY;
                }

                $itemData["owner"] = $this->user->id;
                $item->load($itemData);
                $id = $item->save();
                if ($item->id) {
                    if ($isReady) {
                        $this->_helper->redirector('ready', 'ad');
                    }
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
                $tabs = explode("Ad", $formData["form"]);
                $this->view->gotoTab = strtolower($tabs[1]);
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        }

        $data = $item->toArray();
        foreach ($forms as $key => $form) {
            $form->populate($data);
        }

        if (isset($formData["form"]))
            $forms[$formData["form"]]->populate($formData);
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

    public function _daysLeft($val)
    {
        return ceil((strtotime($val) - time()) / 86400) + 1;
    }

    public function _createPreviewLink($id, $name)
    {
        global $translate;
        if (empty($name))
            $name = $translate->getAdapter()->translate("empty_name");
        return '<a href="/ad/index/id/' . $id . '">' . $name . '</a>';
    }

    private function _resizeImage($src, $target_width, $target_height)
    {
        if (!file_exists($src))
            return false;
        $image = new Application_Model_Image();
        $image->load($src);
        $image->smartResize($target_width, $target_height);
        $newName = uniqid() . ".jpg";
        $image->save(APPLICATION_PATH . "/../public/ads/" . $newName);
        return $newName;
    }

    public function activeAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status = ? AND end_dt > NOW() AND owner = " . $this->user->id, Application_Model_DbTable_Ad::STATUS_ACTIVE);
        $grid->setGridColumns(array("name", "days_left", "public_dt", "start_dt", "end_dt"));
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

        $grid->addExtraColumn(array(
            "name" => "days_left",
            "position" => "right",
            "title" =>  $translate->getAdapter()->translate("days_left"),
            'callback'=>array(
                'function'=>array($this, '_daysLeft'),
                'params'=>array('{{end_dt}}')
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
        $grid->getSelect()->where("status IN (?) AND owner = " . $this->user->id, array(Application_Model_DbTable_Ad::STATUS_DRAFT));
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

    public function readyAction()
    {
        global $translate;

        $grid = Bvb_Grid::factory('Table');
        $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
        $grid->setSource($source);
        $grid->getSelect()->where("status IN (?) AND owner = " . $this->user->id, array(Application_Model_DbTable_Ad::STATUS_READY));
        $grid->setGridColumns(array("name", 'paid', "public_dt", "start_dt", "end_dt"));
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
        $grid->updateColumn('paid',array(
            "title" =>  $translate->getAdapter()->translate("paid"),
            'callback'=>array(
                'function'=>array($this, '_paidText'),
                'params'=>array('{{paid}}')
            )
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
        $grid->getSelect()->where("(status = ? OR end_dt < NOW()) AND owner = " . $this->user->id, Application_Model_DbTable_Ad::STATUS_ARCHIVE);
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

    public function favoritesAction()
    {
        global $translate;
        if (!empty($this->user->favorites_ads)) {
            $grid = Bvb_Grid::factory('Table');
            $source = new Bvb_Grid_Source_Zend_Table(new Application_Model_DbTable_Ad());
            $grid->setSource($source);
            $grid->getSelect()->where("status = ? AND id IN (" . $this->user->favorites_ads . ")", Application_Model_DbTable_Ad::STATUS_ACTIVE);
            $grid->setGridColumns(array("name", "geo_name", "public_dt", "start_dt", "end_dt"));
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
            $grid->updateColumn('start_dt',array(
                "title" =>  $translate->getAdapter()->translate("start_date"),
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
}