<?php
class Zend_View_Helper_NavigationBar extends Zend_View_Helper_Abstract
{
	public function NavigationBar ()
	{
        global $translate;

		$auth = Zend_Auth::getInstance();
        $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $menuItems = array(
            "profile" => array("link" => "/partner/profile", "caption" => $translate->getAdapter()->translate("profile")),
            "active" => array("link" => "/ad/active", "caption" => $translate->getAdapter()->translate("active")),
            "archive" => array("link" => "/ad/archive", "caption" => $translate->getAdapter()->translate("archive")),
            "noactive" => array("link" => "/ad/noactive", "caption" => $translate->getAdapter()->translate("noactive")),
            "add_new" => array("link" => "/ad/new", "caption" => $translate->getAdapter()->translate("add_new")),
            "rules" => array("link" => "#", "caption" => $translate->getAdapter()->translate("rules")),
            "exit" => array("link" => "/auth/logout", "caption" => $translate->getAdapter()->translate("exit"))
        );

		if ($auth->hasIdentity()) {
			if ($auth->getIdentity()->role == "PARTNER") {
        ?>
        <div class="row">
            <ul class="nav nav-pills">
                <?php
                foreach ($menuItems as $value) {
                    if ($uri == $value["link"])
                        echo '<li class="active"><a href="' . $value["link"] . '">' . $value["caption"] . '</a></li>';
                    else
                        echo '<li><a href="' . $value["link"] . '">' . $value["caption"] . '</a></li>';
                }
                ?>
            </ul>
        </div>
		<?php }
        }
	}
}