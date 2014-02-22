<?php
echo '<div class="alert alert-'.$this->lastNotification->Type.' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>'.$this->lastNotification->Title.'</b><pre>'.$this->lastNotification->Message.'</pre></div>';
?>