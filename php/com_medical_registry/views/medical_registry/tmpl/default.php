<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
?>
<div  itemscope itemtype="https://schema.org/Service" class="module">
	<div class="module-body">
		<h1 itemprop="name" class="title"><?php echo $this->HeadItem; ?></h1>
		<div class="cat-children">
		    <div class="items-leading">
		        <div itemprop="hasOfferCatalog" itemscope itemtype="https://schema.org/OfferCatalog" style="font-size: 14pt;">
		        	<ul class="menu">
		            	<?php echo $this->MenuItem;  ?>
		            </ul>  
		        </div>
		    </div>
		</div>
	</div>
</div> 

