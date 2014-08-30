<?php
/**
 * Created by PhpStorm.
 * User: Pavlo
 * Date: 13.08.14
 * Time: 13:42
 */
class Zend_View_Helper_CKEditor {
    function CKEditor( $textareaId ) {
        return "<script type=\"text/javascript\">
                       CKEDITOR.replace( '". $textareaId ."' );
                  </script>";
    }
}
?>