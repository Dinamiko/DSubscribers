<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class DSubscribers_Table {

  private static $_instance = null;
  public $parent = null;

  public function __construct ( $parent ) {

      $this->parent = $parent;
   
      add_action( 'admin_menu', array( $this, 'register_dsubscribers_menu_page' ) );

      add_action('init', array( $this, 'dsubscribers_update' ) );
      add_action('init', array( $this, 'dsubscribers_delete' ) );

      add_action('init', array( $this, 'dsubscribers_export' ) );

  }

  public function register_dsubscribers_menu_page (){

      add_menu_page( 'DSubscribers', 'DSubscribers', 'manage_options', 'dsubscribers', array( $this, 'dsubscribers_menu_page' ), 'dashicons-groups' ); 

  }

  public function dsubscribers_menu_page () { ?>

      <div class="wrap">

        <h2 style="position:relative;width:100%;float:left;margin-bottom:15px;">DSubscribers

          <a style="position:absolute;top:10px;right:15px;" class="button-primary" href="admin.php?page=dsubscribers&action=export">Export (.csv)</a>

        </h2>

      <?php if( isset($_GET['dsubscribers']) && $_GET['action'] == 'edit' ) {

          $id = $_GET['dsubscribers'];

          global $wpdb;
          $table_name = $wpdb->prefix . "dsubscribers";

          $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id=$id"); ?>

          <form id="dsubscribers-form" method="post">

            <label><?php _e( 'Email' , 'dsubscribers' );?>:</label>
            <input type="text" name="email" id="email" value="<?php echo $row->email; ?>" />

            <div style="float:left; width:100%;margin-top:20px;">
              <input type="hidden" name="dsubscribers_id" value="<?php echo $row->id; ?>" />
              <input type="submit" class="button-primary" value="Save"></input>
            </div>

          </form>         

      <?php } ?>

      <?php require_once( 'class-dsubscribers-list-table.php' );

      $wp_list_table = new DSubscribers_List_Table();

      if( isset( $_POST['s'] ) ){

          $wp_list_table->prepare_items( $_POST['s'] );

      } else {

          $wp_list_table->prepare_items();

      } 

      ?>



      <form method="post">

          <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

          <?php $wp_list_table->search_box('Search', 'dsubscribers-id'); ?>

      </form>


 
      <?php $wp_list_table->display(); ?>

      </div>

  <?php }

  public function dsubscribers_update () {

    if( isset( $_POST['dsubscribers_id'] ) ) {

      $id = $_POST['dsubscribers_id'];
      $email = $_POST['email'];

      global $wpdb;
      $table_name = $wpdb->prefix . "dsubscribers";

      $wpdb->update( 
        $table_name,  
        array( 'email' => $email ), 
        array( 'ID' => $id ), 
        array( '%s' ), 
        array( '%d' ) 
      );

      $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
      header("Location:admin.php?page=dsubscribers&paged=$paged");

    }

  }
 
  public function dsubscribers_delete () {

    if( isset($_GET['dsubscribers']) && $_GET['action'] == 'delete' ) {

        //echo $_GET['dsubscribers'];

        global $wpdb;
        $id = $_GET['dsubscribers'];
        $table_name = $wpdb->prefix . "dsubscribers";

        $wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' ) );

        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        header("Location:admin.php?page=dsubscribers&paged=$paged");

    }

  }

  /**
  * Export database data to .csv file
  * based on: https://wordpress.org/plugins/export-users-to-csv/
  */
  public function dsubscribers_export () {

      if( isset($_GET['action']) && $_GET['action'] == 'export' ) {

        $filename = 'dsubscribers-' . date( 'Y-m-d' ) . '.csv';

        header( 'Content-Description: File Transfer' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

        echo "email" . "\n";

        global $wpdb;
        $table_name = $wpdb->prefix . "dsubscribers";

        $emails = $wpdb->get_results( "SELECT * FROM $table_name" );

        foreach ( $emails as $email ) {

          echo $email->email . "\n";

        }

        exit;

      }

  }

  public static function instance ( $parent ) {

    if ( is_null( self::$_instance ) ) {

      self::$_instance = new self( $parent );

    }

    return self::$_instance;

  }

  public function __clone () {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
  }

  public function __wakeup () {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
  } 

}