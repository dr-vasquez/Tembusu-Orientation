<?php
// username to login into page
define('LOGIN_USER', "admin");

// password to login into page
define('LOGIN_PASS', "secret");




###################################################################################################
###################################################################################################
###################################################################################################
# CLASS desc: for calling login authentication
# CLASS req: looks for constants LOGIN_USER and LOGIN_PASS
# Can be called:  ?action=clear_login   ?action=prompt
class Login {

	// unique prefix that is used with this object (on cookies and password salt)
	var $prefix = "login_";

	// days "remember me" cookies will remain
	var $cookie_duration = 0;


	// temporary values for comparing login are auto set here. do not set your own $user or $pass here
	var $user = "";
	var $pass = "";


#-#############################################
# desc: calls the rest of the functions depending on login state
# returns: nothing, but will print login prompt and die if necessary
function authorize() {

	//save cookie info to session
	if(isset($_COOKIE[$this->prefix.'user'])){
		$_SESSION[$this->prefix.'user'] = $_COOKIE[$this->prefix.'user'];
		$_SESSION[$this->prefix.'pass'] = $_COOKIE[$this->prefix.'pass'];
	}
	//	else{echo "no cookie<br>";}


	//if setting vars
	if(isset($_POST['action']) && $_POST['action'] == "set_login"){

		$this->user = $_POST['user'];
		$this->pass = md5($this->prefix.$_POST['pass']); //hash password. salt with prefix

		$this->check();//dies if incorrect

		//if "remember me" set cookie
		if(isset($_POST['remember'])){
			setcookie($this->prefix."user", $this->user, time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
			setcookie($this->prefix."pass", $this->pass, time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
		}

		//set session
		$_SESSION[$this->prefix.'user'] = $this->user;
		$_SESSION[$this->prefix.'pass'] = $this->pass;
	}

	//if forced log in
	elseif(isset($_GET['action']) && $_GET['action'] == "prompt"){
		session_unset();
		session_destroy();
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));

		$this->prompt();
	}

	//if clearing the login
	elseif(isset($_GET['action']) && $_GET['action'] == "clear_login"){
		session_unset();
		session_destroy();
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));

		$msg = '<h2 class="msg">**Logout complete**</h2>';
		$this->prompt($msg);
	}

	//prompt for
	elseif(!isset($_SESSION[$this->prefix.'pass']) || !isset($_SESSION[$this->prefix.'user'])){
		$this->prompt();
	}

	//check the pw
	else{
		$this->user = $_SESSION[$this->prefix.'user'];
		$this->pass = $_SESSION[$this->prefix.'pass'];
		$this->check();//dies if incorrect
	}

}#-#authorize()


#-#############################################
# desc: compares the user info
# returns: nothing, but will print login prompt and die if incorrect
function check(){

	if(md5($this->prefix . LOGIN_PASS) != $this->pass || LOGIN_USER != $this->user){
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));
		session_unset();
		session_destroy();

		$msg='<h2 class="warn">Incorrect username or password</h2>';
		$this->prompt($msg);
	}
}#-#check()


#-#############################################
# desc: prompt to enter password
# param: any custom message to display
# returns: nothing, but exits at end
function prompt($msg=''){
?>
	
	<!DOCTYPE html>
<html>
<head>
		<title>Tembusu College Orientation</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.dropotron.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="semantic-ui-1.0/semantic.css">
		<script type="text/javascript" href="semantic-ui-1.0/semantic.js"></script>
	
	
	</head>
<body>

<div id="header-wrapper">
				<div class="container">

					<!-- Header -->
						<header id="header">
							<div class="inner">
							
								<!-- Logo -->
									<h1><a href="index.html" id="logo">Tembusu College Orientation</a></h1>
								
								<!-- Nav -->
									<nav id="nav">
										<ul>
											
											<li><a href="orientationGroups.html">Orientation Groups</a></li>
											<li><a href="timeline.html">Timeline</a></li>
											<li><a href="aboutus.html">About Us</a></li>
											<li><a href="gallery.html">Gallery	</a></li>
											<li><a href="login.html">Login</a></li>


										</ul>
									</nav>
							
							</div>
						</header>
				</li>
				<br><br>
				 <div class="ui segment narrow">
				 <h3 class="ui segment stacked inverted black block header" align = "center">Private Access: OGL's only</h3>
				 	
					
				<form action="index.php" method="post">
					<input type="hidden" name="action" value="set_login">
						<?php echo $msg; ?>
							
							<div class="field">
								<label for="user">Username</label>
									<div class="ui left icon input">
										<input type="text" name="user" placeholder="Username" size = "32" id="user">
										<i class="user icon"></i>
									</div>
				 			</div>
				 			<div class="field">
				 				<label for="pass">Password</label>
				 					<div class="ui left icon input">
				 						<input type="password" name="pass" placeholder="Password" size = "32" id="pass">
				 						<i class="lock icon"></i></div></div>
									<div class="field">
				 						<br>
				 						<input type="submit" value="Login" class="ui black submit button">
									</div>
				 </form>
				 </div>
				</div>
</div>
</body>
</html>
<?php
	//don't run the rest of the page
	exit;
}#-#prompt()


}//CLASS Login

?>