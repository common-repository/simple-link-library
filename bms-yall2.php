<?php
/*
Plugin Name: Simple Link Library
Plugin URI: http://www.bm-support.org
Description: Simple Link organizer for displaying links
Version: 1.3.4 
Author: Maikel Mardjan
Author URI: http://nocomplexity.com
License: GPLv3
*/

//(Yet Another Link Libarary- version2)
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// include show functions for links
require dirname( __FILE__ ) . '/bms-linksshow.php';

//register the bms-link custom post type
add_action( 'init', 'bms_yall2_create_link_post_type');
function bms_yall2_create_link_post_type() {
  register_post_type( 'bms_link',
    array(
        'supports'=> array(
                'title',
                        ),
            'labels' => array(
                'name' => 'Links',
                'singular_name' => 'Link',
                'all_items' => 'All Link Items',
                'view_item' => 'View Link Item',
                'add_new_item' => 'Add New Link Name',
                'add_new' => 'Add New Link',
                'edit_item' => 'Edit Link',
                'search_items' => 'Search Link',
                'not_found' => 'No Link found',
                'not_found_in_trash' => 'Link Not found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'query_var' => 'link',
        'can_export' => true,
        'public'     => true,
	'show_ui'    => true,
        'hierarchical'        => false,
        'show_in_rest'               => true,
        'rest_base'                  => 'linkcollection', 
        'rewrite'  => array(
            'slug' => 'link'
            ),
         )
  );
}

// Register Custom Taxonomy
function bms_yall2_create_link_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Link  Category', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Link Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Link Categories', 'text_domain' ),
		'all_items'                  => __( 'All Link Categories Defined Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Link Category Tag Item ', 'text_domain' ),
		'add_new_item'               => __( 'Add New Link Category Tag', 'text_domain' ),
		'edit_item'                  => __( 'Edit Link Category Tag Item', 'text_domain' ),
		'update_item'                => __( 'Update Link Tag Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Link Tag category items with commas', 'text_domain' ),
		'search_items'               => __( 'Search Link Tag Category Items', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Link Category items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used Link Categories', 'text_domain' ),
		'not_found'                  => __( 'Link category tag Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'link_category', array( 'bms_link' ), $args );

}

#remove_action('load-edit-link-categories.php');



// Hook into the 'init' action
add_action( 'init', 'bms_yall2_create_link_taxonomy', 0 );

//add meta box for link input screen
add_action( 'add_meta_boxes', 'bms_yall2_create_metabox' );

function bms_yall2_create_metabox(){
    add_meta_box('bms-meta_bms_link','BMS YALL2 Link Overview Card (Meta Box)','bms_yall2_input','bms_link','normal','high');
}

function bms_yall2_input($post){
    //retrieve currently stored meta data for requirement (if any)
    wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
    $bms_stored_meta = get_post_meta( $post->ID );
    
    $output='';
    $output.='<table>';
    $output.='<tr><td>Link URL </td>';
    $output.='<td><input type="text" size="100" name="bms_link_url" value="'; 
        if(isset($bms_stored_meta['_bms_link_url'])) $output.= esc_textarea($bms_stored_meta['_bms_link_url'][0]);
    $output.='" required></td></tr>';
    
    $output.='<tr><td>Link Description</td>';    
    $output.='<td><textarea name=bms_link_description cols=100 rows=5 required>';
        if(isset($bms_stored_meta['_bms_link_description'])) $output.= esc_textarea($bms_stored_meta['_bms_link_description'][0]);
    $output.='</textarea></td></tr>';
       
    $output.='</table>';
    
    echo $output;
}


//hook to save the meta data box data
add_action ('save_post','bms_yall2_save_meta');
function bms_yall2_save_meta ($post_id) {
    //verify the metadata is set
    if (isset($_POST['bms_link_url'])) {
        //save the meta data 
        update_post_meta($post_id, '_bms_link_url', strip_tags($_POST['bms_link_url']));
    }
    if (isset($_POST ['bms_link_description'])) {
        update_post_meta($post_id, '_bms_link_description', $_POST['bms_link_description']);
    }
}

//add column for description in admin overview field
//adding extra columns in the admin custom post list page (customer, status)
add_filter( 'manage_edit-bms_link_columns','bms_yall2_add_columns');
function bms_yall2_add_columns ( $columns){
    $columns['_bms_link_url'] = 'Link-URL';
    $columns['_bms_link_description'] = 'Link-Description' ;
    unset ($columns['comments']);
    return $columns ;
    
}

add_action ( 'manage_bms_link_posts_custom_column','bms_yall2_populate_columns');
function bms_yall2_populate_columns ($column) {
    if ('_bms_link_url' == $column){
        $urllink = esc_html ( get_post_meta (get_the_ID(),'_bms_link_url', true));
        echo $urllink ;
        
    } elseif ('_bms_link_description' == $column) {
        $desc = esc_html (get_post_meta(get_the_ID(),'_bms_link_description',true));
        echo $desc;
        
    }
    
}


//make colums link cat sortable
add_filter ('manage_edit-bms_link_sortable_columns', 'bms_yall2_column_sortable');
function bms_yall2_column_sortable ( $columns) {
    $columns['_bms_link_description'] = 'Link-Description';
    $columns['_bms_link_url'] = 'Link-URL';
    return $columns;
}


add_filter ('request', 'bms_yall2_column_ordering');
function bms_yall2_column_ordering ($vars){
    if (!is_admin() ) 
        return $vars;
    
    if (isset ( $vars['orderby']) && 
            'Link-Description'== $vars['orderby'] ) {
                $vars= array_merge($vars, array (
                    'meta_key' => '_bms_link_description',
                    'orderby'  => 'meta_value' ) );
                                
    } elseif (isset ($vars['orderby']) &&
            "Link-URL" == $vars['orderby'] ) {
        $vars= array_merge( $vars, array(
            'meta_key' => '_bms_link_url',
            'orderby'  => 'meta_value' ) ); //for numerical value use 'meta_value_num' !!
        
    }
    return $vars;
                
}

// Hook for adding sub menu to custom post type principle for export function(s)
add_action('admin_menu', 'bms_yall2_submenu_page');
function bms_yall2_submenu_page() {
add_submenu_page( 
         'edit.php?post_type=bms_link' , 
         'YALL Tools' ,
         'YALL Tools' ,
         'manage_options' ,
         'bms_yall2_options_page' ,
         'bms_yall2_options_page'
    );

}

function bms_yall2_brokenlink_form_show(){
    $output = '';
    $output.='<form method="post" action="">';
    $output.='<table class="form-table"  >';
    $output.='<th>Options for Links </th>';

    $output.='<tr><td> Start validating all links</td>';
    $output.='<td><input type="submit" name="linkcheck" value="linkcheck" class="button button-primary"  >&nbsp;&nbsp;</td>';
    $output.='</tr></table></form>';

    return $output;
        
}

function bms_yall2_options_page(){    
// simple form with simple options
    $output='';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab1';
if(isset($_GET['tab'])) $active_tab = $_GET['tab'];

?>
<h2 > Link Option Page </h2> 
<div class="wrap">
<h2 class="nav-tab-wrapper">
 <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab1" class="nav-tab <?php echo $active_tab == 'tab1' ? 'nav-tab-active' : ''; ?>">Broken Link Checker</a>
 <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab2" class="nav-tab <?php echo $active_tab == 'tab2' ? 'nav-tab-active' : ''; ?>">Export Options</a>
  <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab3" class="nav-tab <?php echo $active_tab == 'tab3' ? 'nav-tab-active' : ''; ?>">Help </a>
</h2><div class="tab_container">
<?php



if($active_tab == 'tab1') { 
    $output.= '<h2>Broken Link checker</h2>';
    $output.= 'Notice: Depending on your number of broken links be patient!! (It can take a moment)<br /><br />';
    $output.=bms_yall2_brokenlink_form_show();
  
    if (isset($_POST['linkcheck']) ){
     //link checker 
     if ($_POST['linkcheck']=='linkcheck'){   
        bms_yall2_link_check(); 
     }
    }
    
}

if($active_tab == 'tab2') {
    $output.= '<h2>Export Functions for Links</h2>';
    $output.= 'This function exports all collected links as plain html. So without any css. So you can use the export in any format you want. Check e.g. Pandoc (<a target="_blank" href="http://pandoc.org/">http://pandoc.org/</a>) for transforming plain html to docx,odf, pdf,rst, markdown and many other formats! <br /><br />';
    $output.= bms_yall2_export_page();

}

if($active_tab == 'tab3') { 
    $output.='<div class="tab_content" style="display:block;">';
    $output.=bms_yall2_help_text();
    $output.='</div>';
}
    
$output.='</div>';
    echo $output;
    
}

if (isset($_POST['yall2export']) ){
    add_action('wp_loaded', 'bms_yall2_export_to_html', 1);	
                function bms_yall2_export_to_html() {
		$filename ='ExportedLinks' . date( 'Y-m-d' ) . '.html';
	        header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename='. $filename );
                header('Content-Type: text/html; charset=ISO-8859-15');
		bms_yall2_export_html();
                exit();
            }
}



function bms_yall2_export_page(){
    $form_return='';
    $form_return.='<form name="yall2exportform" method="POST" action="">';
    $form_return.='<input type="submit" name="yall2export" value="Export To xHTML" class="button-primary">'; 
    $form_return.=wp_nonce_field( 'bms_yall2_download' );
    $form_return.='</form>';
    
    return $form_return;       
}

function bms_yall2_help_text(){
    $helptext='Just use the sort codes or adjust the code! <br /><br />'. 
              'shortcodes for usage:<br /><br /><b>[links cat="name of category"]</b> <br /><br />'. 
              '<b>[SHOWLINKS]  for displaying all your links </b><br /><br />'. 
              'You can use the WordPress REST API to export your links as JSON data. Use the route: <b> wp-json/wp/v2/linkcollection </b><br />' .
              '<br> A simple option to export links per category is possible using the overview screen: <br> Click <b>"All Link Items" </b> -> Click on the link Category you want to export -> [OPTIONAL, but recommended] Select <b>"Title"</b> to  alphabetize your export list -> Select <b>"Bulk actions"</b> and chose the <b>Export to xHTML</b> -> Click <b> "Apply" </b>  Now you can save you selection. Use pandoc.org to convert your selection to markdown or another format if needed.' .
              '<br><br>Note: The Broken check can take a while with a significant number of links. The screen stays blanc during progress. No progress bar is shown.<br>' .
              '<br><br>Feel free to build upon this plugin or create improvements! <br /><br />' .
              '<a target="_blank" href="https://wordpress.org/plugins/simple-link-library/" > Plugin information </a><br /><br />' . 
              '<a target="_blank" href="https://www.bm-support.org" > Contact information </a>' ;
    return $helptext;
}


function bms_yall2_link_check(){
    
    $output = '';
    $output.= '<table class="form-table" >';
    $output.= '<tr>';
    $output.='<th>Link Name</th><th>Broken Link URL (404) </th>';
    $output.= '</tr>';
    //we use the WP wp_remote function
         
    // WP_Query arguments
$args = array (
	'post_type'              => 'bms_link',
        'posts_per_page'=>-1 ,
);

// The Query
$query = new WP_Query( $args );

// The Loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $linkurl = get_post_meta(get_the_ID(), '_bms_link_url', true);

            $response = wp_remote_get($linkurl, array('timeout' => 1));
            $response_code = wp_remote_retrieve_response_code($response);

            if ($response_code == 404) {
                //link must be changed or is no longer valid
                $output.= '<tr><td>';
                $editstring = get_edit_post_link(get_the_ID());
                $output .= the_title('<a href="' . $editstring . '">', '</a>', false);
                $output.= '</td>';
                $output.= '<td>';
                $output.=$linkurl;
                $output.= '</td></tr>';
            }
        }
    } else {
        // no posts found
        $output.='<tr><td>No Broken Links found!</td></tr>';
    }
    $output.= '</table>';
// Restore original Post Data
    wp_reset_postdata();
    echo $output;
}

function bms_yall2_get_categories(){
    $args = array(
	'type'                     => 'bms_link',
	'child_of'                 => 0,
	'parent'                   => '',
	'orderby'                  => 'name',
	'order'                    => 'ASC',
	'hide_empty'               => 1,
	'hierarchical'             => 1,
	'exclude'                  => '',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'link_category',        
	'pad_counts'               => false ); 
     
     $categories=get_categories( $args );
     return $categories; //returns array of ids
    
}

function bms_yall2_export_html(){
    // The  PHP_EOL is end-of-line 
    check_admin_referer( 'bms_yall2_download'); //nonce check 
    
    $output = '';
    $output .='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    $output .='<!DOCTYPE html>';
    $output .='<html>';
    $output .='<head>';     
    $output .='</head><body>';              
    $output .= '<h1>Link Library Export </h1>';
        
    $categories=bms_yall2_get_categories(); 
    
    foreach($categories as $category) { 
        $output .= '<h2>' . $category->name . '</h2>';
        $cat = get_term_by('name', $category->name, 'link_category');
        $catid = $cat->term_id;
                            
        $loop = new WP_Query(
                array(
                'post_type' => 'bms_link',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'link_category',
                        'field' => 'term_id',
                        'terms' => "$catid",
                    ),
                ),                                    
                'orderby' => 'title',
                'order' => 'ASC',
                'posts_per_page' => -1,
                    )
            );
        
            /* Check if any links's were returned. */
            if ($loop->have_posts()) {

                /* Loop through (The Loop). */
                while ($loop->have_posts()) {
                    $loop->the_post();
                                            
                $url = get_post_meta( get_the_ID(), '_bms_link_url', true );
                $description=get_post_meta( get_the_ID(), '_bms_link_description', true );            
                // Display the SBB                    
                #$editstring= get_edit_post_link( get_the_ID());               
                $output.= '<section><p><a target="_blank" href="'. ent2ncr($url) .'">'. get_the_title() . '</a></p></section>';
                $output .= '<section><p>' . ent2ncr($description) . '</p></section><br />'  ;                
                        }

                /* Close the unordered list. */
                $output .= '<br />';
    
        } /* If no SBB items  were found. */ else {

            $output = 'No links have been published for this category.';
        }

        /* Return the SBB  list. */
  } // end for all categories

        $output .='</body></html>';
        echo $output;
        //echo wp_strip_all_tags($output);
    
}

/* Better and simpeler export using bulk select in admin menu , as xHTML only for good RSS output in rst (use pandoc) */

add_filter( 'bulk_actions-edit-bms_link', 'register_my_yall2_bulk_actions' );
function register_my_yall2_bulk_actions($bulk_actions) {
  $bulk_actions['export_xhtml'] = __( 'Export to xHTML', 'Export to xHTML');  
  return $bulk_actions;
}

add_filter( 'handle_bulk_actions-edit-bms_link', 'yall2_bulk_action_handler', 10, 3 );
function yall2_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
  if ( $doaction == 'export_xhtml' ) {
      bms_yall2_export_xhtml($post_ids);  
  } else {
    return $redirect_to;
  }

} // end function action_handler


function bms_yall2_export_xhtml($post_ids){
  $filename ='Exported_LINKS_' . date( 'Y-m-d' ) . '.html';
  header( 'Content-Description: File Transfer' );
  header( 'Content-Disposition: attachment; filename='. $filename );
  header('Content-Type: text/html; charset=ISO-8859-15');
       
        $output = '';
        $output .='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $output .='<!DOCTYPE html>';
        $output .='<html>';
        $output .='<head>';
        $output .='<title>Exported Links in xHTML (Simple Link Exporter)</title>';
        $output .='</head><body>';    

        foreach ( $post_ids as $post_id ) {
          // Perform action for each post.               
                $output .= '<h2>'. get_the_title($post_id) . '</h2>';                
                $output .='<br>';                         
                $linkurl = get_post_meta( $post_id , '_bms_link_url', true );                
                $posttags = get_the_terms( $post_id , 'link_category');                
                $linkdescription = get_post_meta( $post_id, '_bms_link_description', true );    
        
                if ($posttags && !is_wp_error( $posttags )) {
                    $tag_collection= array();
                    foreach ( $posttags as $tag ) {
                        $tag_collection[]= $tag->name;
                            }}
                            $tags=join (", ", $tag_collection);
                 // Display the LINK Feed URL meta info and tag(s)
                
                $output.= '<table>';                           
                $output.= '<tr><td><b>URL</b></td>';                                 
                $output.= '<td>' . '<a href="' . ent2ncr($linkurl) . '" target="_blank" >'. ent2ncr($linkurl) . '</a>' . '</td>' . '</tr>' ;                
                $output.= '<tr><td><b>Description</b></td>';                 
                $output.= '<td>' . '<p>' . ent2ncr($linkdescription) . '</p>' . '</td>'; 
                $output.= '</tr>';                 
                $output.= '<tr><td><b>Tag(s)</b></td>'; 
                $output.= '<td>' . $tags . '</td>' ;   
                $output.= '</tr>';                 
                $output.= '</table>'; 
                #write_log($linkdescription);
  }
  $output .='</body></html>';          
  echo $output;
  exit(); 

  $redirect_to = add_query_arg( 'exported_posts_xhtml', count( $post_ids ), $redirect_to );
  return $redirect_to;
}


?>