
<?php
class Zend_View_Helper_AdItem extends Zend_View_Helper_Abstract
{

	public function AdItem ($data) {
        global $translate;
?>  
    <div class="span3 bottom-offset">
        <a class="img-wrapper" href="/show/<?= $data["seo_name"] ?>"> <img src="/media/<?= $data["photoimg"] ?>" class="img-polaroid">
            <div class="post-link img-info">
                <div data-link="/auth" title="<?= $translate->getAdapter()->translate("add_to_favorites") ?>" data-id="<?= $data["post_id"] ?>" class="favorites-icon favorites-icon-off"></div>
                <p class="ellipsis"><?= $data["name"] ?></p>
                <p class="ellipsis"><?= $data["brand_name"] ?></p>
                <p class="ellipsis"><?= $data["description"] ?></p>
                <p class="ellipsis"><?= $translate->getAdapter()->translate("days_left"). ": " .$data["days"] ?></p>
            </div>
        </a>
    </div>    
<?php
	}
}