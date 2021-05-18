<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
?>

<div class="module">
	<div class="moduletable">
	    <div class="item-page-title">
	        <h1 class="title"><?php  echo $this->HeadItemRecall; ?></h1>
	    </div>
	    <div class="items-leading">
		    <div>
		        <?php  echo $this->ToolsItemRecall; ?>
		    </div>
		    <div class="moduletable" style="text-align: center; margin: auto;">
    			<?php  echo $this->ContentItemRecall; ?> 
		    </div>
		    <div class="MEDICAL_REGISTRY_View_Result_Reports">
    			<?php  echo $this->ViewItemRecall; ?>            
		    </div>
		</div>
	</div>
</div>           
