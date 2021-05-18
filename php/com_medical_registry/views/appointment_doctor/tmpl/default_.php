<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

?>
<div class="module">
	<div class="module-body">
	    <div class="item-page-title">
	        <h1><?php echo $this->HeadItemAppointment; ?></h1>
	    </div>
	    <div class="module_content">
			<div class="message">
				<?php echo $this->DescriptionItemAppointment; ?>
			</div>
			    <?php if($this->ToolsItemAppointment != ''){
    				echo "\t".'<div class="input">'."\n";
					echo "\t"."\t".$this->ToolsItemAppointment."\n";
					echo "\t".'</div>'."\n";
			    } 
			    ?>
			<div style="width: 100%; height: 100%; text-align: center; margin: auto;">
				<div style="width: 95%; height: 95%; text-align: center; margin: auto; display: inline;">
		    		<?php echo $this->MenuItemAppointment; ?>
			    </div>
			</div>
		</div>
	</div>
	    
<?php 
//echo '<pre>';
//print_r ($this->ViewItemAppointment);
//echo '</pre>';
?> 
</div> 

