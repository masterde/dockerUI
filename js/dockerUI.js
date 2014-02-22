var loadingGif='<img src="assets/loading.gif" />';

/*COMMON FUNCTION*/

function createNotification(divID,type,title,message)
{
	//go back to top of the page to show notif
	 $("html, body").animate({ scrollTop: 0 }, "slow");
	//types are : danger, success, warning and info (see bootstrap classes)
	divID.html('<div class="alert alert-'+type+' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>'+title+'</b><pre>'+message+'</pre></div>');	
}


function resetButton(buttonID,buttonValue,time)
{
	//reset button after 3 seconds
	setTimeout(function (){

            buttonID.html(buttonValue);

         }, time);
}

/*SETTINGS PAGE*/

$( "#btnSaveSettings" ).click(function() { 
		
		//to use browser's HTML5 validator
		if(!$('#formSettings')[0].checkValidity())
		{
			$('#formSettings .submit').click();
			return;
		}
		var btn = $(this);
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		//check input aren't empty or placeholder isn't basic
		$("#notificationSettings").empty();
		request = $.ajax({
					url: "scripts/saveSettings.php",
					type: "post",
					data: { host:$('#hostInput').val(),user:$('#userInput').val(),password:$('#passwordInput').val(),port:$('#portInput').val()}
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				if(msg!=0)
				{
					createNotification($("#notificationSettings"),'danger','Error !',msg);
					
				}
				else
				{						
					location.reload();
					
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

$( "#btnCheckSettings" ).click(function() { 
		
		//to use browser's HTML5 validator
		if(!$('#formSettings')[0].checkValidity())
		{
			$('#formSettings .submit').click();
			return;
		}
		var btn = $(this);
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		//check input aren't empty or placeholder isn't basic
		$("#notificationSettings").empty();
		request = $.ajax({
					url: "scripts/sshSettings.php",
					type: "post",
					data: { host:$('#hostInput').val(),user:$('#userInput').val(),password:$('#passwordInput').val(),port:$('#portInput').val()}
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				if(msg!=0)
				{
					createNotification($("#notificationSettings"),'danger','Error !',msg);
				}
				else
				{						
					btn.html('SSH connection is good !');
					$("#notificationSettings").empty();
					resetButton(btn,'Check SSH',5000);
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

$( "#btnClearIntermediateImages" ).click(function() { 
		
		var btn = $(this);
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		//check input aren't empty or placeholder isn't basic
		$("#notificationSettings").empty();
		request = $.ajax({
					url: "scripts/deleteIntermediateImages.php",
					type: "post",
					data: { }
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				if(msg!=0)
				{
					createNotification($("#notificationSettings"),'danger','Error !',msg);
				}
				else
				{						
					location.reload();
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

/*************/

/*CONTAINER PAGE*/

$( "#btnStartStop" ).click(function() { 
		
		var btn = $(this);
		var isStart="stop";

		if(btn.attr('isStart')=="true")
		{
			isStart="start";

		}
		var containerID=btn.attr('containerID');
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		request = $.ajax({
					url: "scripts/cmdContainer.php",
					type: "post",
					data: {command:'docker '+isStart+' '+containerID}
					});
					
			request.done(function( msg ) 
			{	
				if(msg!=0)
				{
					createNotification($("#notificationContainer"),'danger','Error !',msg);
					
				}
				else
				{						

					$("#notificationContainer").empty();
					location.reload();
					
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

$( "#btnRestart" ).click(function() { 
		
		var btn = $(this);

		var containerID=btn.attr('containerID');
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		request = $.ajax({
					url: "scripts/cmdContainer.php",
					type: "post",
					data: { command:'docker restart '+containerID}
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				if(msg!=0)
				{
					createNotification($("#notificationContainer"),'danger','Error !',msg);
					
				}
				else
				{						

					$("#notificationContainer").empty();
					location.reload();
					
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

$( "#deleteContainerButton" ).click(function() { 
		
		var btn = $(this);

		var containerID=btn.attr('containerID');
		//close modal
		$('#modalDeleteContainer').modal('hide')
		
		btn=$("#btncontainerDelete");
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		request = $.ajax({
					url: "scripts/cmdContainer.php",
					type: "post",
					data: { command:'docker rm '+containerID}
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				if(msg!=0)
				{
					createNotification($("#notificationContainer"),'danger','Error !',msg);
					
				}
				else
				{						

					$("#notificationContainer").empty();
					window.location = 'index.php?subMenuID=2';
					
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});

/*************/

/*CREATE CONTAINER PAGE*/

$( "#btncontainerCreate" ).click(function() { 
		
		var containerIDRegex = '[0-9a-fA-F]{64}';
		
		//to use browser's HTML5 validator
		if(!$('#formCreateContainer')[0].checkValidity())
		{
			$('#formCreateContainer .submit').click();
			return;
		}
		var btn = $(this);
		btn.button('loading');
		btn.html(btn.html()+' '+loadingGif);
		//check input aren't empty or placeholder isn't basic
		$("#notificationSettings").empty();
		request = $.ajax({
					url: "scripts/createContainer.php",
					type: "post",
					data: { hostname:$('#containerNameInput').val(),image:$('#containerImageInput').val(),cmd:$('#commandInput').val()}
					});
					
			request.done(function( msg ) 
			{	
				btn.button('reset');
				//will return containerID if kaikki OK
				if(!msg.match(containerIDRegex))
				{
					createNotification($("#notificationContainer"),'danger','Error !',msg);
					
				}
				else
				{					
					//go to containerID.php page
					window.location = 'index.php?containerID='+msg;		
				}
				
			});	
			request.fail(function( msg ) {	
					   alert(msg);
					  });	
					  
});


/**************/