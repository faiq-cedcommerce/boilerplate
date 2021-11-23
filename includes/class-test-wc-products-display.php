<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
class Test_Wc_Products_Display extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
        'singular'  =>  'Product', 
        'plural'    =>  'Products', 
        'ajax' => false,         
        ] );    
    }
    
    public function get_products( $per_page = 5, $page_number = 1 ) {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE `post_type`='product' and `post_status`='publish' ";
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }        
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );        
        return $result;
    }
    
    public function delete_product( $id ) {
        global $wpdb;
        // $wpdb->delete("{$wpdb->prefix}posts" ,array('ID' => $id ));
        if (!empty($id)) {
            $wpdb->query("DELETE FROM `wp_posts` WHERE id = $id)");
        }
    }
    
    public function record_count() {
        global $wpdb;    
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE `post_type`='product' and `post_status`='publish' ";
        return $wpdb->get_var( $sql );
    }
    
    public function no_items() {
        print_r('No Products are available');
    }
    


    function column_post_title($item){
        $delete_nonce = wp_create_nonce( 'sp_delete_product' );
        $actions = array(
                  'edit'        => sprintf('<a href="post.php?action=%s&post=%s">Edit</a>','edit',$item['ID']),
                  'delete'      => sprintf('<a href="post.php?action=%s&post=%s&_wpnonce=%s&page=%s">Delete</a>','delete', $item['ID'], $delete_nonce, 'woo-products-list' ),
        );  
      
        return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions) );
      }

    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
                case 'post_title':
                return $item[ $column_name ];
                default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }   

    function column_cb( $item ) {
        return sprintf(
               '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }
    
    function get_columns() {
        $columns = [
                'cb' => '<input type="checkbox" />',
                'post_title' => 'Product Name',
        ];        
        return $columns;
    }

    
    public function get_sortable_columns() {
        $sortable_columns = array(
            'post_title' => array( 'post_title', true ),
        );
        return $sortable_columns;
    }  
    
    public function get_bulk_actions() {
        $actions = [
           'bulk-delete' => 'Delete'
        ];
        return $actions;
    }  
    public function prepare_items() {

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = $this->record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,
        ) );
        $this->items = $this->get_products( $per_page, $current_page );
       
    }    
    
    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
        // In our file that handles the request, verify the nonce.
        $nonce = esc_attr( $_REQUEST['_wpnonce'] );
        if ( ! wp_verify_nonce( $nonce, 'sp_delete_product' ) ) {
            die( 'Go get a life script kiddies' );
        }else {
            $this->delete_product( $_GET['post'] );                  
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
            $delete_ids = $_POST['bulk-delete'];
            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                $this->delete_product( $id );
            }
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
        else if(isset($_GET['action'] ) && $_GET['action'] == 'delete' &&  isset($_GET['post']) && $_GET['post']  != null ){
            $this->delete_product( $_GET['post'] );
        }  
    }    
}           
?>