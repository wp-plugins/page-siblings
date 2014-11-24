<?php
/*
Plugin Name: Page Siblings
Plugin URI: http://wordpress.org/extend/plugins/page-siblings
Description: Add a metabox with all page edit (and any other hierarchal post types) that display an edit link to its siblings.
Author: Ionuț Staicu
Version: 1.0.4
Author URI: http://iamntz.com
*/

class Ntz_Page_Siblings{
  function __construct(){
    if( !is_admin() ){ return; }
    add_action( 'admin_init', array( &$this, 'add_metabox' ), 999 );
  }


  public function add_metabox(){
    $all_post_types = get_post_types( array(
      'show_ui'  => true,
    ) );

    foreach( (array) $all_post_types as $post_type ) {
      if( is_post_type_hierarchical( $post_type ) ){
        add_meta_box(
          'ntz_page_siblings', 'Page Siblings',
          array( &$this, 'the_metabox' ),
          $post_type, 'side'
        );

        add_action('restrict_manage_posts', array( &$this, 'add_column_filters' ) );
        add_filter( 'parse_query', array( &$this, 'alter_query' ) );
      }
    }
  } // add_metabox


  public function add_column_filters(){
    $current_setting = isset( $_GET['display_only_parents'] ) ? (int) $_GET['display_only_parents'] : 0;
    ?>
    <select name="display_only_parents">
      <option value="all" <?php selected( $current_setting, 1 ); ?>>Show Parents And Children</option>
      <option value="parents_only" <?php selected( $current_setting, 2 ); ?>>Show Only Parents</option>
    </select>
    <?php
  } // add_column_filters


  public function alter_query( $query ){
    if ( isset( $_GET['display_only_parents'] ) && $_GET['display_only_parents'] == 'parents_only' ) {
      $query_vars = &$query->query_vars;
      $query_vars['post_parent'] = 0;
    }
  } // alter_query


  public function the_metabox( $post_data, $meta_info ){
    $post_type = $post_data->post_type;
    $post_id = $post_data->ID;
    ?>
    <style type="text/css" media="screen">
      #ntz_page_siblings ul {
        margin-left:1em;
      }
    </style>
    <ul>
    <?php

    if( $post_data->post_parent > 0 ){
      $ancestors = get_post_ancestors( $post_data->ID );
      $root      = count( $ancestors )-1;
      $parent_id = $ancestors[$root];
    } else {
      $parent_id = $post_data->ID;
    }
    $parent = get_post( $parent_id );

    if( $parent ){
      $this->print_child( $parent );
      $this->loop_through_children( $parent_id, $post_type );
    }else {
      echo "<li>This entry doesn't have any siblings</li>";
    }
    echo "</ul>";
  }// the_metabox


  /**
   * Recursive function that parse all children and display a list of them.
   * @param  int    $post_id   the id of the page that need to be parsed
   * @param  string $post_type post type
   * @param  string $prefix    in case you need to add a different prefix (useful for a tree-like structure)
   * @return void
   */
  private function loop_through_children( $post_id, $post_type, $prefix = '&mdash;' ){
    $children = $this->get_children( $post_id, $post_type );

    foreach( (array) $children->posts as $child ) {
      $this->print_child( $child, $prefix );

      if( count( $sub_children = $this->get_children( $child->ID, $post_type ) ) > 0 ){
        $this->loop_through_children( $child->ID, $post_type, $prefix . "&mdash;" );
      }

    }
  } // loop_through_children


  /**
   * Print a list element with each page.
   * @param  object $child  the $post object
   * @param  string $prefix if the element should have a prefix (a child of a child)
   * @return void
   */
  private function print_child( $child, $prefix = "" ){
    printf( '<li>%s <a href="%s" style="%s">%s</a>',
      ( !empty( $prefix ) ? "<span class='pipe'>|</span>{$prefix}" : '' ),
      get_edit_post_link( $child->ID ),
      ( isset( $_GET['post'] ) && $child->ID == $_GET['post'] ? 'font-weight:700;' : '' ),
      esc_attr( $child->post_title )
    );

  } // print_child


  /**
   * Fetch all children for a post
   * @param  int      $post_id    the ID of the post
   * @param  string   $post_type  post type
   * @return object               the children results
   */
  private function get_children( $post_id, $post_type ){
    $children = new WP_Query( array(
      'posts_per_page' => -1,
      'post_type'      => $post_type,
      'post_parent'    => $post_id,
      'orderby'        => 'menu_order',
      'order'          => 'ASC'
    ) );

    return $children;
  } // get_children

}//Ntz_Page_Siblings

new Ntz_Page_Siblings();