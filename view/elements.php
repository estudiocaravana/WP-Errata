<?php 

	require_once(WP_ERRATA_PATH . 'util.php');

?>

<div id='com-estudiocaravana-errata-boxWrapper'>
	<div id='com-estudiocaravana-errata-box'>
		<a id="com-estudiocaravana-errata-title" href='javascript:errata.showForm()'><?php _e("Errata report",'ecerpl'); ?></a>
		<div id="com-estudiocaravana-errata-form">
			<?php _e("Errata:",'ecerpl')?> "<span id="com-estudiocaravana-errata-errata"></span>"
			<span id="com-estudiocaravana-errata-errata-error-noerrata" class="com-estudiocaravana-errata-error"><?php _e("An errata must be selected",'ecerpl')?></span>
			<br>
			<?php _e("Correction:",'ecerpl')?>
			<input type="text" name="com-estudiocaravana-errata-correction" value="" id="com-estudiocaravana-errata-correction"/>
			<span id="com-estudiocaravana-errata-correction-error-nocorrection" class="com-estudiocaravana-errata-error"><?php _e("A correction must be written",'ecerpl')?></span>
			<br>				
			<input type="hidden" name="com-estudiocaravana-errata-ipAddress" id="com-estudiocaravana-errata-ipAddress" value="<?php echo Util::getIpAddress(); ?>" />
			<?php 
				global $post;
				if ($post){ ?>
				<input type="hidden" name="com-estudiocaravana-errata-postID" id="com-estudiocaravana-errata-postID" value="<?php echo $post->ID; ?>" />
			<?php
				}
			?>
			<a href="javascript:errata.showDetails()"><?php _e("+ More details",'ecerpl')?></a>
			<br>
			<div id="com-estudiocaravana-errata-details">
				<?php _e("Description:",'ecerpl')?>
				<br>
				<textarea name="com-estudiocaravana-errata-description" id="com-estudiocaravana-errata-description"></textarea><br>
				<?php _e("Email:",'ecerpl')?>
				<input type="text" name="com-estudiocaravana-errata-email" value="" id="com-estudiocaravana-errata-email"/>
				<span id="com-estudiocaravana-errata-email-error-invalidformat" class="com-estudiocaravana-errata-error"><?php _e("Invalid email format",'ecerpl')?></span>
				<br>
			</div>
			<a href="javascript:errata.sendErrata()"><?php _e("Send errata report",'ecerpl')?></a>
		</div>
		<div id="com-estudiocaravana-errata-status">
			<span id="com-estudiocaravana-errata-status-sendingErrata"><?php _e("Sending errata...",'ecerpl')?></span>
			<span id="com-estudiocaravana-errata-status-errataSent"><?php _e("Errata sent!",'ecerpl')?></span>
		</div>
	</div>
</div>
