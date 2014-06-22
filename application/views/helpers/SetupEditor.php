<?php
class Zend_View_Helper_CKEditor {
    function CKEditor( $textareaId ) {
        return "<script type=\"text/javascript\">
                       CKEDITOR.replace( '". $textareaId ."' );
                  </script>";
    }
}
?>