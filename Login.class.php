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

<?php
	//don't run the rest of the page
	exit;
}#-#prompt()


}//CLASS Login

?>