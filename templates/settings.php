<?php
/*
            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.

*/
?>
<form id="pwauth" action="#" method="post">
	<fieldset class="personalblock">
		<legend><strong>Unix Authentication</strong></legend>
		<p>
		<label for="pwauth_path"><?php echo $l->t('pwauth_path'); ?></label><input type="text" id="pwauth_path" name="pwauth_path" value="<?php echo $_['pwauth_path']; ?>" />
		</p><p>
		<label for="uid_list"><?php echo $l->t('uid_list');?></label><input type="text" id="uid_list" name="uid_list" value="<?php echo $_['uid_list']; ?>"  original-title="<?php echo $l->t('uid_list_original-title'); ?>"/>
		</p>
		<input type="hidden" name="requesttoken" value="<?php echo $_['requesttoken'] ?>" id="requesttoken">
		<input type="submit" value="Save" />
	</fieldset>
</form>
