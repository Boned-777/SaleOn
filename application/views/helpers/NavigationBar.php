<?php
class Zend_View_Helper_NavigationBar extends Zend_View_Helper_Abstract
{
	public function NavigationBar ()
	{
		$auth = Zend_Auth::getInstance();
        $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $menuItems = array(
                "profile" => array("link" => "/partner/profile", "caption" => "Мой профиль"),
                "active" => array("link" => "/ad/active", "caption" => "Активные"),
                "archive" => array("link" => "/ad/archive", "caption" => "Архив"),
                "noactive" => array("link" => "/ad/noactive", "caption" => "Неоплаченные"),
                "add_new" => array("link" => "/ad/new", "caption" => "Добавить акцию"),
                "rules" => array("link" => "#", "caption" => "Правила"),
                "exit" => array("link" => "/auth/logout", "caption" => "Выход")
        );

		if ($auth->hasIdentity()) {
			if ($auth->getIdentity()->role == "PARTNER") {
        ?>
        <div class="row offset1">
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