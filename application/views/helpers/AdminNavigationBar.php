<?php
class Zend_View_Helper_AdminNavigationBar extends Zend_View_Helper_Abstract
{
	public function AdminNavigationBar ()
	{
        global $translate;

		$auth = Zend_Auth::getInstance();
        $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

        $URIArr = explode("/", $uri);
        if (sizeof($URIArr) < 2)
            return false;
        $uri = '/' . implode("/", $URIArr);
        $menuItems = array(
            "noactive" => array("link" => array("/admin/noactive", "/ad/edit"), "caption" => $translate->getAdapter()->translate("noactive")),
            "ready" => array("link" => array("/admin/ready"), "caption" => $translate->getAdapter()->translate("ready")),
            "active" => array("link" => array("/admin/active"), "caption" => $translate->getAdapter()->translate("active")),
            "archive" => array("link" => array("/admin/archive"), "caption" => $translate->getAdapter()->translate("archive")),
        );

		if ($auth->hasIdentity()) {
            if (isset($auth->getIdentity()->role))
			    if ($auth->getIdentity()->role == Application_Model_User::ADMIN) {
            ?>
            <div id="admin_nav" class="row">
                <ul class="nav nav-pills">
                    <?php
                    foreach ($menuItems as $value) {
                        if (in_array($uri, $value["link"]))
                            echo '<li class="active"><a href="' . $value["link"][0] . '">' . $value["caption"] . '</a></li>';
                        else
                            echo '<li><a href="' . $value["link"][0] . '">' . $value["caption"] . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <?php }
        }
	}
}