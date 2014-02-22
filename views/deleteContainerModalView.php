<div class="modal fade" id="modalDeleteContainer" tabindex="-1" role="dialog" aria-labelledby="deleteContainerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="deleteContainerLabel">Delete container <?php echo $myContainer->Hostname; ?></h4>
		  </div>
		  <div class="modal-body">	
				Are you sure ?
		  </div>
		  <div class="row">
			<div class="col-xs-offset-1 col-xs-10 col-md-offset-1 col-md-10"  id="deleteContainerNotification">
			
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			<button id="deleteContainerButton"  containerID="<?php echo $myContainer->ID; ?>" type="button" class="btn btn-danger">Delete</button>
		  </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
