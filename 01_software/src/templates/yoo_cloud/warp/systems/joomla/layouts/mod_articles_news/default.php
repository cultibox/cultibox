<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die;

?>

<?php foreach ($list as $item) :?>
	<?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
<?php endforeach; ?>