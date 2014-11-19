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

        $uri = "";
        for($i=1; ($i<sizeof($URIArr) && $i<3); $i++) {
            $uri .= "/".$URIArr[$i];
        }
        if (!$uri) {
            $uri = "/";
        }

        $hideOnPages = array(
            "/",
            "/index",
            "/index/index",
            "/index/news",
            "/ad/index",
            "/index/favorites",
            "/index/contacts"
        );

        if (in_array($uri, $hideOnPages)) {
            return false;
        }

        if (strpos($uri, "filter")) {
            return false;
        }

        $menuItems = array(
            "noactive" => array("link" => array("/admin/noactive", "/ad/edit"), "caption" => $translate->getAdapter()->translate("noactive")),
            "ready" => array("link" => array("/admin/ready"), "caption" => $translate->getAdapter()->translate("ready")),
            "active" => array("link" => array("/admin/active"), "caption" => $translate->getAdapter()->translate("active")),
            "archive" => array("link" => array("/admin/archive"), "caption" => $translate->getAdapter()->translate("archive")),
            "geo_edit" => array("link" => array("/admin/geo-edit"), "caption" => $translate->getAdapter()->translate("geo_edit")),


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