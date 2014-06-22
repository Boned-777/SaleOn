<?php
class Zend_View_Helper_SetupEditor {

    function setupEditor( $textareaId ) {
        return "<script type=\"text/javascript\">
 CKEDITOR.replace( '". $textareaId ."' );
  </script>";
    }
}
?>
<?php
class Zend_View_Helper_BaseUrl {
    function baseUrl() {
        $fc = Zend_Controller_Front::getInstance();
        return $fc->getBaseUrl();
    }
}
?>