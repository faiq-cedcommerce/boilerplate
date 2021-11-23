<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cedcoss.com/
 * @since      1.0.0
 *
 * @package    Test_Wc
 * @subpackage Test_Wc/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Test_Wc
 * @subpackage Test_Wc/admin
 * @author     Faiq Masood <faiqmasood@cedcoss.com>
 */
class Test_Wc_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Test_Wc_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Test_Wc_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/test-wc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Test_Wc_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Test_Wc_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/test-wc-admin.js', array( 'jquery' ), $this->version, false );

	}
	
    public function pluginAdminMenu(){
        add_menu_page( 'Woo Products', 'Woo Products', 'manage_options', 'woo-products-list', array($this,'displayProducts'), 'dashicons-editor-unlink');
		
	}
 
    public function displayProducts(){
        echo '<h1>My Plugin Settings</h1>';
		include WP_PLUGIN_DIR. '/test-wc/includes/class-test-wc-products-display.php';
		?><div class="wrap">
			<h2>WP_List_Table Class Example</h2>
			<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
			<div class="meta-box-sortables ui-sortable">
			<form method="post">
			<?php
			$twpd = new Test_Wc_Products_Display();
			$twpd->prepare_items();
			$twpd->display(); 
			?>
			</form>
			</div>
			</div>
			</div>
			<br class="clear">
			</div>
			</div>
		<?php
	}

	// Adding a custom Meta container to admin products pages	
	public function create_custom_meta_box(){
			add_meta_box( 'custom_product_meta_box','Additional Product Information', array($this, 'add_custom_content_meta_box'),'product','normal','default');
		}
	//  Custom metabox content in admin product pages
	public	function add_custom_content_meta_box( $post ){
			$brand = get_post_meta($post->ID, 'brand_wysiwyg', true) ? get_post_meta($post->ID,'brand_wysiwyg', true) : '';
			$args['textarea_rows'] = 6;
			echo '<p> Brand </p>';
			wp_editor( $brand, 'brand_wysiwyg', $args );
			echo '<input type="hidden" name="custom_product_field_nonce" value="' . wp_create_nonce() . '">';
	}
	//Save the data of the Meta field
	public function save_custom_content_meta_box( $post_id ) {
			
			if ( ! isset( $_POST[ 'custom_product_field_nonce' ] ) ) {
				return $post_id;
			}
			$nonce = $_REQUEST[ 'custom_product_field_nonce' ];
			//Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce ) ) {
				return $post_id;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			if ( 'product' == $_POST[ 'post_type' ] ){
				if ( ! current_user_can( 'edit_product', $post_id ) )
					return $post_id;
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}
			// Sanitize user input and update the meta field in the database.
			update_post_meta( $post_id, 'brand_wysiwyg', wp_kses_post($_POST[ 'brand_wysiwyg' ]) );
		}

	
	// Displaying the product meta on frontpage
	function my_custom_checkout_field() {
			global $post;
			$product_brand = get_post_meta( $post->ID, 'brand_wysiwyg', true );
			if ( ! empty( $product_brand ) ) {
				echo '<h6>Brand Name</h6>';
				echo apply_filters( 'the_content', $product_brand );
			}
	}
}
