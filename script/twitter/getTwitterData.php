<?php
session_start();

require("twitter/twitteroauth.php");
require 'config/twconfig.php';
require 'config/functions.php';
require "../StoreImage.php";


if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
    // We've got everything we need
    $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
// Let's request the access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
// Save it in a session var
    $_SESSION['access_token'] = $access_token;
// Let's get the user's info
    $user_info = $twitteroauth->get('account/verify_credentials');
// Print user's info
    echo '<pre>';
    ///print_r($user_info);exit();
	
    echo '</pre><br/>';
    if (isset($user_info->error)) {
        // Something's wrong, go back to square 1  
        header('Location: login-twitter.php');
    } else {
        $twtrimg=new StoreImage();
        $uid = $user_info->id;
        $username = $user_info->name; 
		$usernamePhoto = $user_info->profile_image_url;
        $user = new User();
        $userdata = $user->checkUser($uid, 'twitter', $username);
		//print_r($userdata);
        if(!empty($userdata)){
            session_start();
            //$_SESSION['id'] = $userdata['id'];
 $_SESSION['oauth_id'] = $uid;
            $_SESSION['username'] = $userdata['username'];
            $_SESSION['oauth_provider'] = $userdata['oauth_provider'];
			if($usernamePhoto)
				$_SESSION['usernamePhoto']=$twtrimg->StoreTwitterImage($usernamePhoto);
            $_SESSION['shwsgnup']=1;
            
            header("Location: ../index.php");
        }
    }
} else {
    // Something's missing, go back to square 1
    header('Location: login-twitter.php');
}

?>
