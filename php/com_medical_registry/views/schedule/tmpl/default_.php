<?php
// Запрет прямого доступа.
defined('_JEXEC') or die; 
?>
<div class="module">
	<div class="module-body">
		<h1><?php echo $this->HeadItemSchedule; ?></h1>
		
	    <div class="moduletable">
	        <?php echo $this->ToolsItemSchedule; ?>
	    </div>    
	    <div class="inputbox">
	            <?php echo $this->MenuItemSchedule; ?>  
	    </div>
	    <div class="small">
	        <?php echo $this->ViewItemSchedule; ?>  
	    </div>
	</div>
</div>