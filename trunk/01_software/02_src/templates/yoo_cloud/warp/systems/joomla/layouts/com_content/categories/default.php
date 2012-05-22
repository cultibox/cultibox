<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>

<div id="system">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_base_description') && ($this->params->get('categories_description') || $this->parent->description)) : ?>
	<div class="description">
		<?php
			if($this->params->get('categories_description')) {
				echo JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_content.categories');
			} elseif ($this->parent->description) {
				echo JHtml::_('content.prepare', $this->parent->description, '', 'com_content.categories');
			}
		?>
	</div>
	<?php endif; ?>
	
	<?php if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) : ?>
		<ul>
		<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
			<?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) : ?>
			<li>
				<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));?>"><?php echo $this->escape($item->title); ?></a>
				
				<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
				<small>(<?php echo $item->numitems; ?>)</small>
				<?php endif; ?>
				
				<?php if (($this->params->get('show_subcat_desc_cat') == 1) && $item->description) : ?>
				<div><?php echo JHtml::_('content.prepare', $item->description, '', 'com_content.categories'); ?></div>
				<?php endif; ?>
		
				<?php
					if (count($item->getChildren()) > 0) {
						$this->items[$item->id] = $item->getChildren();
						$this->parent = $item;
						$this->maxLevelcat--;
						echo $this->loadTemplate('items');
						$this->parent = $item->getParent();
						$this->maxLevelcat++;
					}
				?>
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

</div>