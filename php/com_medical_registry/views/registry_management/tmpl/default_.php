<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
?>

<div class="component-content">
    <div class="module-body">
        <h1 class="title"><?php echo $this->HeadItemManagement; ?></h1>
	    <div class="item-page-title">
	        <h2><?php echo $this->DescriptionItemManagement; ?></h2>
	    </div>
	    <div class="moduletable">
		    <div class="menu">
		        <?php echo $this->ToolsItemManagement; ?>
		    </div>
		    <div class="component-content">
		    	<?php echo $this->MenuItem; ?> 
			    <div style="width: 100%; height: 100%; text-align: center; margin: auto;" class="item-page">
			        <?php echo $this->ContentItem; ?>
			    </div>
			    <div style="text-align: center; margin: auto;">
			        <div style="text-align: center; margin: auto;">
			            <?php echo $this->MenuItemSchedule; ?>  
			        </div>
			        <div style="text-align: center; margin: auto;">
			            <?php echo $this->ViewItemManagement; ?>            
			        </div>
			    </div>
			</div>
		</div>
    </div>
</div>