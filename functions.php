<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function site_files(){
    wp_enqueue_script('umbrella-js', get_template_directory_uri() . 'umbrella.min.js', array(), '1.0.0', true);
    wp_enqueue_script('custom-js', get_theme_file_uri('/build/index.js'), array('umbrella-js'), '1.0', true);
    wp_enqueue_style('main', get_stylesheet_directory_uri() . '/css/main.css', false, '', 'all');
    wp_enqueue_style('singles', get_stylesheet_directory_uri() . '/css/singles.css', false, '', 'all');
    wp_enqueue_style('checkout', get_stylesheet_directory_uri() . '/css/checkout.css', false, '', 'all');
    wp_enqueue_style('myspace', get_stylesheet_directory_uri() . '/css/myspace.css', false, '', 'all');
    wp_enqueue_style('product', get_stylesheet_directory_uri() . '/css/product.css', false, '', 'all');
    wp_localize_script('custom-js', 'customData', array(
        'root_url' => get_site_url(),        
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts','site_files');
/* Disable WordPress Admin Bar for all users */
add_filter( 'show_admin_bar', '__return_false' );
// custom post types
function story_custom_post_types() {
    register_post_type('story', array(
        'supports' => array('title', 'editor', 'excerpt'),
        'has_archive' => false,
        'rewrite' => array(
            'slug' => 'stories'
        ),
        'public' => true,
        'menu_position' => 3,
        'labels' => array(
            'name' => 'Stories',
            'add_new' => 'Add New Story',
            'edit_item' => 'Edit Stories',
            'all_items' => 'All Stories',
            'singular_item' > 'Story'
        ),
        'menu_icon' => 'dashicons-text-page'
    ));
}
add_action('init', 'story_custom_post_types');
//file types------------------------------------------------------------------------------
function jozie_mime_types($mime_types){
    $mime_types['epub'] = 'application/epub+zip'; 
    unset($mime_types['png']); 
    return $mime_types;
}
add_filter('upload_mimes', 'jozie_mime_types', 1, 1);
// woocommerce support
add_action( 'after_setup_theme', 'jozie_enable_woocommerce_support' );
function jozie_enable_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

function jozie_marketplace_remove_product_tabs( $tabs ) {
    unset($tabs['additional_information']); 
    unset($tabs['description']);
    unset($tabs['reviews']);
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'jozie_marketplace_remove_product_tabs', 98 );
// custom product filter------------------------------------------------------------------
function jozie_product_formats(){
    $product_id = get_the_ID(); // Get the current product's ID
    $cats = wp_get_post_terms( $product_id, 'product_cat');
    if ($cats){
        $cat = $cats[0];
        echo '<p class="single-product-format">This is book is in ' .  $cat->name . ' format.';        
        $alternates = get_post_meta($product_id, 'other_format', true);
        if ($alternates){
            ?><div class="alternate-section">
                <p class="centered-text">It's also available in 
                    <?php for ($i = 0; $i < count($alternates); $i++){
                        $alternate = $alternates[$i];
                        $alternate_cats = wp_get_post_terms( $alternate, 'product_cat');
                        if (count($alternate_cats) > 0){
                            $alternate_cat = $alternate_cats[0];
                            echo '<a href="' . get_the_permalink($alternate) . '">' . $alternate_cat->name . '</a>';
                            if (count($alternates) > 2 && $i == count($alternates) - 2){
                                echo ', and ';
                            } else if (count($alternates) == 2 && $i < count($alternates) - 1){
                                echo ' and ';
                            }
                        }
                    }
                ?></p>
            </div>
        <?php }
    }
}
add_action( 'woocommerce_single_product_summary', 'jozie_product_formats', 7);

function jozie_read_sample(){
    $product_id = get_the_ID();
    $sample = get_post_meta($product_id, 'book_excerpt', true);
    if ($sample){
        echo '<p class="sample-link">read a sample</p>';
    } 
}
add_action( 'woocommerce_single_product_summary', 'jozie_read_sample', 6);

function jozie_heat_and_triggers(){
    $product_id = get_the_ID();
    $trigger_warning = get_post_meta($product_id, 'trigger_warning', true);
    $heat_level = get_post_meta($product_id, 'heat_level', true);
    if ($trigger_warning || $heat_level){
        echo '<div class="heat-and-triggers-section">';
        if ($trigger_warning){
            echo '<div class="triggers-section">';
            echo '<p>Warning! This book contains content that may be triggering. </p><p class="view-trigger-warning-link">view triggers</p>';
            echo '</div>';
            echo '<div id="trigger-warning-content" class="hidden single-product--preview-overlay"><div class="preview-overlay--close" id="close-trigger-warning"><p>close</p></div><div class="preview-overlay--preview-wrapper"><div class="preview-overlay--preview"><h2>Trigger Warning</h2>' . $trigger_warning . '</div></div></div>';
        }
        if ($heat_level){
            echo '<div class="heat-section">';
            echo '<p>Heat level: ' . $heat_level . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
}
add_action( 'woocommerce_single_product_summary', 'jozie_heat_and_triggers', 31);

function jozie_open_purchase_section_div(){
    echo '<div class="single-product-purchase-section-wrapper"><div class="single-product-purchase-section">';
}
add_action( 'woocommerce_single_product_summary', 'jozie_open_purchase_section_div', 9);

function jozie_other_retailers(){
    $product_id = get_the_ID();
    $amazon_link = get_post_meta($product_id, 'amazon_link', true);
    $barnes_and_noble_link = get_post_meta($product_id, 'barnes_and_noble_link', true);
    $kobo_link = get_post_meta($product_id, 'kobo_link', true);
    $bookshop_link = get_post_meta($product_id, 'bookshop_link', true);
    if ($amazon_link != '' || $barnes_and_noble_link != '' || $barnes_and_noble_link != '' || $kobo_link != ''){
        echo '<div id="other-retailers-section">';
        echo '<p>Also available from the following retailers:</p>';
        echo '<div id="other-retailers">';
        if ($bookshop_link != ''){
            echo '<span><a target="_blank" href="<?php echo $bookshop_link; ?>">Bookshop.org</a></span>';
        }
        if ($amazon_link != ''){
            echo '<span><a target="_blank" href="<?php echo $amazon_link; ?>">Amazon</a></span>';
        }
        if ($barnes_and_noble_link != ''){
            echo '<span><a target="_blank" href="<?php echo $barnes_and_noble_link ?>">Barnes & Noble</a></span>';
        }
        if ($kobo_link != ''){
            echo '<span><a target="_blank" href="<?php echo $kobo_link; ?>">Kobo</a></s>';
        }
        echo '</div></div>';
    }
    echo '</div>'; //closing the single-product-purchase-section div
    echo '</div>'; //closing the wrapper div
}
add_action( 'woocommerce_single_product_summary', 'jozie_other_retailers', 30);

function jozie_shelf_it(){
    echo '<div class="shelf"></div>';
}
add_action( 'woocommerce_single_product_summary', 'jozie_shelf_it', 3);

function jozie_open_book_details_div(){
    echo '<div class="book-title-details-wrapper">';
    echo '<div class="book-title-details">';
}
add_action( 'woocommerce_single_product_summary', 'jozie_open_book_details_div', 4);

function jozie_close_book_details_div(){
    echo '</div>';
    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'jozie_close_book_details_div', 8);
