<?php

class Application_Form_AdMedia extends Zend_Form
{
    public $isReady;

    public function __construct($options = null)
    {
        $this->isReady = $options["isReady"]?true:false;
        parent::__construct($options);
        $this->setName('media');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setMethod("post");
    }

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdMedia");
        $this->getElement("form")->setDecorators(array('ViewHelper'));
        $this->addElement('file', 'banner_file', array(
            'class' => "bottom-offset",
            'label' => $translate->getAdapter()->translate("banner"). ' *',
            //'required' => true
        ));
        $this->addElement('file', 'image_file', array(
            'class' => "bottom-offset",
            'label' => $translate->getAdapter()->translate("image") . " *",
            //'required' => true
        ));



        $this->addElement('textarea', 'video', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("video"),
        ));

        $this->addElement('submit', 'submit', array(
            //'class' => 'btn btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }

    public function processData($formData) {
        $upload = new Zend_File_Transfer_Adapter_Http();
        $images = $this->_processImage($upload);
        $mediaItemData = array();
        if (sizeof($images)) {
            foreach ($images as $imgKey => $imgVal) {
                switch ($imgKey) {
                    case "image_file" :
                        if (!isset($images["banner_file"])) {
                            $mediaItemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/media/" . $imgVal, 240, 153);
                        }
                        $mediaItemData["image"] = $imgVal;
                        break;

                    case "banner_file" :
                        $mediaItemData["banner"] = $this->_resizeImage(APPLICATION_PATH . "/../public/media/" . $imgVal, 240, 153);
                        break;
                }
            }
        }

        $mediaItemData["video"] = $formData["video"];
        if (sizeof($mediaItemData))
            return $mediaItemData;
        else
            return false;
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
            $upload->setDestination(APPLICATION_PATH . "/../public/media");
            $upload->addFilter('Rename', APPLICATION_PATH . "/../public/media" . DIRECTORY_SEPARATOR . $newName);

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

    private function _resizeImage($src, $target_width, $target_height)
    {
        if (!file_exists($src))
            return false;
        $image = new Application_Model_Image();
        $image->load($src);
        $image->resize($target_width, $target_height);
        $newName = uniqid() . ".jpg";
        $image->save(APPLICATION_PATH . "/../public/media/" . $newName);
        return $newName;
    }

    public function isValid($data) {
        global $translate;

        $parentRes = parent::isValid($data);

        $customRes = true;
        if (isset($data["invalidFormElements"])) {
            foreach ($data["invalidFormElements"] as $invalidElement) {
                $this->getElement($invalidElement["element"])->addError($translate->getAdapter()->translate($invalidElement["error"]));
            }
            $customRes = false;
        }

        return $parentRes&&$customRes;
    }
}

