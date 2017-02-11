<?php

class Meow_Lightbox_Core {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/*
		INIT
	*/

	function init() {
    include( 'mwl_run.php' );
    new Meow_Lightbox_Run;
		if ( is_admin() ) {
			include( 'mwl_admin.php' );
	    new Meow_Lightbox_Admin;
		}
	}

}

?>
