<!DOCTYPE html>
<html>

<head>
		<meta charset="utf-8">
	 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>EasyOTP- JusPay</title>
	 <!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">
	<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>

	

	<script type="text/javascript">
	(function() {
    var po = document.createElement('script');
    po.type = 'text/javascript'; po.async = true;
    po.src = 'https://plus.google.com/js/client:plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
	})();
	</script>

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>

	<script type="text/javascript">

	var myemail;
	var gpclass = (function(){
	
	//Defining Class Variables here
	var response = undefined;
	return {
		//Class functions / Objects
		
		mycoddeSignIn:function(response){
			// The user is signed in
			if (response['access_token']) {
			
				//Get User Info from Google Plus API
				gapi.client.load('plus','v1',this.getUserInformation);

$('<center>Waiting to Detect OTP..</center><center><img src="https://www.decaredental.ie/assets/images/wait.gif" width="50" height="50"></center>').appendTo('#foo');



				
			} else if (response['error']) {
				// There was an error, which means the user is not signed in.
				//alert('There was an error: ' + authResult['error']);
			}
		},
		
		getUserInformation: function(){
			var request = gapi.client.plus.people.get( {'userId' : 'me'} );
			request.execute( function(profile) {
				var email = profile['emails'].filter(function(v) {
					return v.type === 'account'; // Filter out the primary email
				})[0].value;
				myemail=email;
				var fName = profile.displayName;
				$("#inputFullname").attr('readonly', true);
				//$("#gbutton").attr('readonly', true);

				$("#inputEmail").attr('readonly', true);
				$("#inputFullname").val(fName);
				$("#inputEmail").val(email);

			});
		}
	
	}; //End of Return
	})();
	
	function mycoddeSignIn(gpSignInResponse){
		gpclass.mycoddeSignIn(gpSignInResponse);
	}
	</script>

	<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});


  	</script>
  	  <script>
var ref = new Firebase("https://easyotp.firebaseio.com/");
// Get the data on a post that has changed
ref.on("child_changed", function(snapshot) {
  var changedPost = snapshot.val();
  if(changedPost.emailid==myemail){
  	 // alert(changedPost.otp);

$('#foo').empty();
$('<center><p>OTP Is: '+changedPost.otp+'</p><center>').appendTo('#foo');

  	   if (!Notification) {
    alert('Desktop notifications not available in your browser. Try Chromium.'); 
    return;
  }



   var notification = new Notification('JusPay EasyOTP', {
      icon: 'https://juspay.in/images/juspay-icon.png',
      body: "Your OTP is : "+changedPost.otp,
    });





  }
  //console.log("The updated post title is " + changedPost.title);
});
    </script>





<div data-role="page">

	<div data-role="header">
		<center>	<img src="http://c93fea60bb98e121740fc38ff31162a8.s3.amazonaws.com/wp-content/uploads/2016/02/juspay.png"></center>
	
		<h1>EasyOTP</h1>
	</div><!-- /header -->

	<div role="main" class="ui-content">
	 <div class="container">

      <form class="form-signin" role="form">
			<div id="status"></div>
        <h2 class="form-signin-heading"></h2>
		
		<label for="inputFname" class="sr-only">First Name</label>
			<input type="text" id="inputFullname" class="form-control" placeholder="First Name" required autofocus>
			
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required >
		
        <label for="inputPassword" class="sr-only">Password</label>
        
        <div class="row"> 
							<center>

			<div class="col-md-6">
				<button 
					id="gbutton"
				class="g-signin " 
					data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email"
					data-requestvisibleactions="http://schemas.google.com/AddActivity"
					data-clientId="1038542660435-o7ehhf2ssqs2fuhi9paqk8hbt1r3keed.apps.googleusercontent.com"
					data-accesstype="offline"
					data-callback="mycoddeSignIn"
					data-theme="dark"
					data-cookiepolicy="single_host_origin">
				</button>

<button onclick="window.history.back();">SIGNOUT</button>
				
			</div>
						</center>

		</div>
		 

      </form>

    </div> <!-- /container -->
		
		</div><!-- /content -->

	<div data-role="footer">
		<div id="foo">
		</div>
	</div><!-- /footer -->

</div><!-- /page -->
 
</body>
</html>


