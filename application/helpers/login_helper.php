<?php

function is_logged_in($destroy = null) {
    //session_start();
    // Get current CodeIgniter instance
    $CI =& get_instance();
    
    $user = $CI->session->userdata('user');
    //$user = isset($_SESSION['id_user'])?$_SESSION['id_user']:'';
    if ($user == '') { redirect(base_url()); } else { return true; }
}

function forbidden_access(){
		$CI =& get_instance();
    
    	$user = $CI->session->userdata('user');
	    if ($user == '') {
	    	redirect(base_url());
	    } else {
	    	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'){
		    // none
			}else{
				redirect(base_url());
			}
	    }		
		
	}
?>