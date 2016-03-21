
<!DOCTYPE html>
<html>
<head>
<title>Juspay's Easy OTP</title>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">
	<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>

<link href="./css/bootstrap.css" rel="stylesheet"></link>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  
  <script type="text/javascript">
	(function() {
    var po = document.createElement('script');
    po.type = 'text/javascript'; po.async = true;
    po.src = 'https://plus.google.com/js/client:plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
	})();
	</script>
  
  
<style type="text/css">
html, 
body {
height: 100%;
}

body {
background-image: url(backj.jpg);
background-repeat: no-repeat;
background-size: 100% 100%;
}
</style>




<style type="text/css">
.background {
box-sizing: border-box;
width: 100%;
height: 150px;
padding: 3px;
background-image: url('backj.jpg');
border: 1px solid black;
background-size: 100% 100%;
}
</style>

<style type="text/css">

body {
        padding-top: 40px;
        padding-bottom: 40px;

      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
      .errorContainer{ max-width:370px; margin:0 auto; }
	.errorContainer ul{ list-style:none; padding:5px; margin:0px;}
	.errorContainer li { color:red; padding:5px; }
    </style>
<script src="js/jquery.min.js"></script>

</head>
<body background="backj.jpg">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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



<div data-role=""page>	
	<div data-role="header">
		<center><span><img src='juspay.png' height="200" width="200"/></span><br>
	</div>
	<br><br><br>
	<div class="container">
		<div role="main" class="ui-content">
			<div class="container">
				<form class="form-signin" role="form">
				
					<label for="inputFname" class="sr-only">First Name</label>
					<input type="text" id="inputFullname" class="form-control" placeholder="First Name" required autofocus>
			
					<label for="inputEmail" class="sr-only">Email address</label>
					<input type="email" id="inputEmail" class="form-control" placeholder="Email address" required ><br><br>
		
					<center>
							
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
								<br><br>

								<button onclick="window.history.back();" class="btn btn-primary">SIGNOUT</button>
				
							
					</center>
					
				</form>
			</div>
		</div>
	</div>
</div>
</body>
</html>