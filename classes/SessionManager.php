<?php

class SessionManager
{
    public function sessionStart($user_id,$name, $limit = 0, $path = '/', $domain = null, $secure = null)
   {
      // Set the cookie name before we start.
     /* session_name($name . '_Session');

      // Set the domain to default to the current domain.
      $domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);

      // Set the default secure value to whether the site is being accessed with SSL
      $https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

      // Set the cookie settings and start the session
      session_set_cookie_params($limit, $path, $domain, $secure, true);
*/     
	 session_start();
	  
	  $_SESSION['user_id']=$user_id;
	  $_SESSION['domain']=$domain;
	  $_SESSION['user_agent']=$_SERVER['HTTP_USER_AGENT'];
	  $_SESSION['active_time']= time();
	  $_SESSION['expiry'] = 1800;
	  
	  
	 /* if(self::validateSession())
	{
		// Check to see if the session is new or a hijacking attempt
		if(!self::preventHijacking())
		{
			// Reset session data and regenerate id
			$_SESSION = array();
			//$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
			self::regenerateSession();

		// Give a 5% chance of the session id changing on any request
		}elseif(rand(1, 100) <= 5){
			self::regenerateSession();
		}
	}else{
		$_SESSION = array();
		session_destroy();
		session_start();
	}
   */
   
   }


// SessionManage::sessionStart('Accounts_Bank', 0, '/', 'accounts.bank.com', true);

/*static protected function preventHijacking()
{
	if(!isset($_SESSION['userAgent']))
		return false;

	if( $_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
		return false;

	return true;
}

*/

 function regenerateSession()
{
	// If this session is obsolete it means there already is a new id
	if(isset($_SESSION['OBSOLETE']) && $_SESSION['OBSOLETE'] == true)
		return;

	// Set current session to expire in 10 seconds
	$_SESSION['OBSOLETE'] = true;
	$_SESSION['EXPIRES'] = time() + 10;

	// Create new session without destroying the old one
	session_regenerate_id(false);

	// Grab current session ID and close both sessions to allow other scripts to use them
	$newSession = session_id();
	session_write_close();

	// Set session ID to the new one, and start it back up again
	session_id($newSession);
	session_start();

	// Now we unset the obsolete and expiration values for the session we want to keep
	unset($_SESSION['OBSOLETE']);
	unset($_SESSION['EXPIRES']);
}

public function validateSession()
{
	session_start();
	
	if( !isset($_SESSION['domain'] ,$_SESSION['user_agent'] ,$_SESSION['active_time'], $_SESSION['expiry'], $_SESSION['user_id'] ) )
	{
		//echo "<script>console.log('new session')</script>"; 
		//console.log("new session");
		return false;
	}
    
	if( strpos($_SERVER['SERVER_NAME'] , $_SESSION['domain']) === false)
	{	
		//echo "<script>console.log('bad url')</script>"; 
		return false;
	}
	if( $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] )
	{	
		//echo "<script>console.log('bad browser')</script>"; 
		return false;
	}
	if (!isset($_SESSION['timeout_idle'])) 
	{
		$_SESSION['timeout_idle'] = time() + $_SESSION['expiry'];
						} else {
								if ($_SESSION['timeout_idle'] < time()) {    
									//destroy session
								//echo "<script>console.log('session timeout')</script>"; 
								return false;
									
									
						} else {
							$_SESSION['timeout_idle'] = time() + $_SESSION['expiry'];
								}
						}
	
	

	return true;

	
}

}

?>