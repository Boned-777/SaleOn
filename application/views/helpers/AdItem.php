<?php
class Zend_View_Helper_AdItem extends Zend_View_Helper_Abstract
{
	public function AdItem ($data)
    {
?>  
    <div class="span3 bottom-offset">
        <a class="img-wrapper" href="/ad/index/id/<?= $data["post_id"] ?>"> <img src="/media/<?= $data["photoimg"] ?>" class="img-polaroid">
            <div class="post-link img-info">
                <div data-link="/auth" title="Додати в обране" data-id="130" class="favorites-icon favorites-icon-off"></div>
                <p class="ellipsis"><?= $data["name"] ?></p>
                <p class="ellipsis"><?= $data["brand_name"] ?></p>
                <p class="ellipsis">Залишилося днів: <?= $data["days"] ?></p>
            </div>
        </a>
    </div>    
<?php
	}
}