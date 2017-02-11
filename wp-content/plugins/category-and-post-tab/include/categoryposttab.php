<?php  
/**
 * Register shortcode and render post data as per shortcode configuration. 
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'categoryPostTabWidget' ) ) { 
	class categoryPostTabWidget extends categoryPostTabLib {
	 
	   /**
		* PHP5 constructor method.
		*
		* Run the following methods when this class is loaded
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function __construct() {
		
			add_action( 'init', array( &$this, 'init' ) ); 
			parent::__construct();
			
		}  
		
	   /**
		* Load required methods on wordpress init action 
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function init() {
		
			add_action( 'wp_ajax_getTotalPosts',array( &$this, 'getTotalPosts' ) );
			add_action( 'wp_ajax_getPosts',array( &$this, 'getPosts' ) ); 
			add_action( 'wp_ajax_getMorePosts',array( &$this, 'getMorePosts' ) );
			
			add_action( 'wp_ajax_nopriv_getTotalPosts', array( &$this, 'getTotalPosts' ) );
			add_action( 'wp_ajax_nopriv_getPosts', array( &$this, 'getPosts' ) ); 
			add_action( 'wp_ajax_nopriv_getMorePosts', array( &$this, 'getMorePosts' ) ); 
			
			add_shortcode( 'categoryposttab', array( &$this, 'categoryPostTab' ) ); 
			
		} 
		
	   /**
		* Get the total numbers of posts
		*
		* @access  public
		* @since   1.0
		* 
		* @param   int    $category_id  		Category ID  
		* @param   int    $c_flg  				Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of posts  	
		*/  
		public function getTotalPosts( $category_id, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["security_key"], 'security' );

		   /**
			* Fetch posts as per search filter
			*/	
			$_res_total = $this->getSqlResult( $category_id, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}	

		 
	   /**
		* Render tab for category and posts shortcode
		*
		* @access  public
		* @since   1.0
		*
		* @param   array   $params  Shortcode configuration options from admin settings
		* @return  string  Render tab for category and posts HTML
		*/
		public function categoryPostTab( $params = array() ) { 	
		
			$categoryposttab_id = $params["id"]; 
			$cpt_shortcode = get_post_meta( $categoryposttab_id ); 
			
			foreach ( $cpt_shortcode as $sc_key => $sc_val ) {			
				$cpt_shortcode[$sc_key] = $sc_val[0];			
			} 
			
			if(!isset($cpt_shortcode["number_of_post_display"]))	
				$cpt_shortcode["number_of_post_display"] = 0;
			if(!isset($cpt_shortcode["category_id"]))	
				$cpt_shortcode["category_id"] = 0;
				
			$this->_config = shortcode_atts( $this->_config, $cpt_shortcode ); 
			
		   /**
			* Load template according to admin settings
			*/
			ob_start();
			
			require( $this->getCategoryPostTabTemplate( "template_" . $this->_config["template"] . ".php" ) ); 
			
			return ob_get_clean();
		
		}   
		
	   /**
		* Load more post via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  void Displays searched posts HTML to load more pagination
		*/	
		public function getMorePosts() {
		
			global $wpdb, $wp_query; 
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer($this->_config["security_key"], 'security' );
			
			$_total = ( isset( $_REQUEST["total"] )?esc_attr( $_REQUEST["total"] ):0 );
			$category_id = ( isset( $_REQUEST["category_id"] )?esc_attr( $_REQUEST["category_id"] ):0 ); 
			$_limit_start = ( isset( $_REQUEST["limit_start"])?esc_attr( $_REQUEST["limit_start"] ):0 );
			$_limit_end = ( isset( $_REQUEST["number_of_post_display"])?esc_attr( $_REQUEST["number_of_post_display"] ):cpt_number_of_post_display ); 
			
		   /**
			* Fetch posts as per search filter
			*/	
			$_result_items = $this->getSqlResult( $category_id, $_limit_start, $_limit_end );
		  
			require( $this->getCategoryPostTabTemplate( 'ajax_load_more_posts.php' ) );	
			
			wp_die();
		}    
		
	   /**
		* Load more posts via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  object Displays searched posts HTML
		*/
		public function getPosts() {
		
		   global $wpdb; 
			
		   /**
			* Check security token from ajax request
			*/	
		   check_ajax_referer( $this->_config["security_key"], 'security' );	   
		   
		   require( $this->getCategoryPostTabTemplate( 'ajax_load_posts.php' ) );	
		   
  		   wp_die();
		
		}
		 
	   /**
		* Get post list with specified limit and filtered by category and search text
		*
		* @access  public
		* @since   1.0 
		*
		* @param   int     $category_id 		 Selected category ID  
		* @param   int     $_limit_end			 Limit to fetch post ending to given position
		* @return  object  Set of searched post data
		*/
		public function getPostList( $category_id, $_limit_end ) {
			
		   /**
			* Check security token from ajax request
			*/	
			check_ajax_referer( $this->_config["security_key"], 'security' );		
			
		   /**
			* Fetch data from database
			*/
			return $this->getSqlResult( $category_id, 0, $_limit_end );
			 
		}
		 
	   /**
		* Fetch post data from database by category, search text and item limit
		*
		* @access  public
		* @since   1.0 
		* 
		* @param   int    $category_id  		Category ID  
		* @param   int    $_limit_start  		Limit to fetch post starting from given position
		* @param   int    $_limit_end  			Limit to fetch post ending to given position
		* @param   int    $category_flg  		Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @param   int    $is_count  			Whether to fetch only number of posts from database as count of items 
		* @return  object Set of searched post data
		*/
		private function getSqlResult( $category_id, $_limit_start, $_limit_end, $category_flg = 0, $is_default_category_with_hidden = 0, $is_count = 0 ) {
			
			global $wpdb; 
			$_category_filter_query = ""; 
			$_fetch_fields = "";
			$_limit = "";
			
			
		   /**
			* Prepare safe mysql database query
			*/
			if( $is_count == 1 ) {
				if( $category_id > 0 && ( $category_flg == 1 || $is_default_category_with_hidden == 1 ) ) {
					$_category_filter_query .= $wpdb->prepare( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id = %d and wtr.object_id = wp.ID ", $category_id );
				} 
				$_fetch_fields = " count(*) as total_val ";
			} else { 
				if( $category_id > 0 ) {
					$_category_filter_query .= $wpdb->prepare( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id = %d and wtr.object_id = wp.ID ", $category_id );
				} 
				$_fetch_fields = " wp.post_type, pm_image.meta_value as post_image, wp.ID as post_id, wp.post_title as post_title ";
				$_limit = $wpdb->prepare( " order by wp.post_title ASC limit  %d, %d ", $_limit_start, $_limit_end );
			} 
			
			 
		   /**
			* Fetch post data from database
			*/
			$_result_items = $wpdb->get_results( " select $_fetch_fields from {$wpdb->prefix}posts as wp  
				$_category_filter_query LEFT JOIN {$wpdb->prefix}postmeta as pm_image on pm_image.post_id = wp.ID and pm_image.meta_key = '_thumbnail_id'
				where wp.post_status = 'publish' and wp.post_type = 'post' $_limit " );			
				
				  
			return $_result_items;
		
		}
		
	}
	
}
new categoryPostTabWidget();