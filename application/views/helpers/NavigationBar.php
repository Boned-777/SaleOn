<?php
class Zend_View_Helper_NavigationBar extends Zend_View_Helper_Abstract
{
	public function NavigationBar ()
	{
        global $translate, $session;

		$auth = Zend_Auth::getInstance();
        $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

        $URIArr = explode("/", $uri);
        if (sizeof($URIArr) < 3)
            return false;
        $uri = '/' . $URIArr[1] . '/' . $URIArr[2];
        $menuItems = array(
            "profile" => array("link" => array("/partner/profile"), "caption" => $translate->getAdapter()->translate("profile")),
            "add_new" => array("link" => array("/ad/new#main"), "caption" => $translate->getAdapter()->translate("add_new")),
            "noactive" => array("link" => array("/ad/noactive", "/ad/edit"), "caption" => $translate->getAdapter()->translate("noactive")),
            "ready" => array("link" => array("/ad/ready"), "caption" => $translate->getAdapter()->translate("ready")),
            "active" => array("link" => array("/ad/active"), "caption" => $translate->getAdapter()->translate("active")),
            "archive" => array("link" => array("/ad/archive"), "caption" => $translate->getAdapter()->translate("archive")),
            //"favorites" => array("link" => array("/index/favorites"), "caption" => $translate->getAdapter()->translate("mm_favorites")),
            "rules" => array("link" => array("/partner/rules"), "caption" => $translate->getAdapter()->translate("rules")),
            //"exit" => array("link" => array("/auth/logout"), "caption" => $translate->getAdapter()->translate("exit"))
        );

		if ($auth->hasIdentity()) {
            if (isset($auth->getIdentity()->role))
			    if ($auth->getIdentity()->role == "PARTNER") {
            ?>
            <div class="row">
                <ul class="nav nav-pills <?= ($session->locale=="ru") ? "nav-wide" : "" ?>">
                    <?php 
                    foreach ($menuItems as $value) {
                        if (
                            in_array($uri, $value["link"]) ||
                            (($uri == "/ad/new") && ($value["link"][0]=="/ad/new#main"))
                        ) {
                            echo '<li class="active"><a href="' . $value["link"][0] . '">' . $value["caption"] . '</a></li>';
                        } else {
                            echo '<li><a href="' . $value["link"][0] . '">' . $value["caption"] . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php }
        }
	}
}