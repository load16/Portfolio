<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
?>

<div class="module">
	<div class="moduletable">
	    <div class="item-page-title">
	        <h1 class="title"><?php echo $this->HeadItemReports; ?></h1>
	    </div>
	    <div class="items-leading">
		    <div class="inputbox">
		        <?php echo $this->ToolsItemReports; ?>
		    </div>
		    <div class="moduletable" style="text-align: center; margin: auto;">
    			<?php echo $this->ContentItemReports; ?> 
		    </div>
		    <div class="MEDICAL_REGISTRY_View_Result_Reports">
    			<?php echo $this->ViewItemReports; ?>            
		    </div>
		</div>
	</div>
</div>           
