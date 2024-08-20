<?php
/*
 * Plugin Name:       RA Related Posts
 * Description:       This is a Plugin for showing latest posts of a post
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Razel Ahmed
 * Author URI:        https://razelahmed.com
 */

 if ( ! defined('ABSPATH') ) {
    exit;
 }
  //  echo plugin_dir_url(__FILE__);
 // returning plugin path
 define('RALP_PLUGIN_URL', plugin_dir_url(__FILE__));

class Ra_Related_Posts {

    // initializing the hook
    public function __construct() {
        add_action('init', array( $this,'init') );
    }

    // Hook into the_content to append related posts
    public function init() {
        add_filter('the_content', [$this, 'display_related_posts']);
        // enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
    }

    // enqueue frontend css
    public function enqueue_frontend_styles($hook) { 
        // Define the base URL for the plugin
        $plugin_url = plugin_dir_url(__FILE__);
        // Custom css
        wp_enqueue_style('ra-latestpost-custom-css', $plugin_url . 'css/custom.css', array(), '1.0.0', 'all');
    }

    // Function to append related posts to the post content
    public function display_related_posts($content) {
        // Only apply on single post pages
        if (is_single()) {
            $related_posts = $this->get_related_posts();

            // Check if there are related posts
            if (!empty($related_posts)) {
                ob_start();
                ?>
                <div class="related-posts" id="ra-related-post">
                    <h3>Related Posts</h3>
                    <div class="related_post_main">
                        <?php foreach ($related_posts as $post) : ?>
                            <div class="related_post_all">
                                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                        <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                                    </a>
                                <div class="related_post_all_inner">
                                    
                                    <p>By <?php echo get_the_author_meta('display_name', $post->post_author); ?></p>
                                    <h5>
                                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                            <?php echo esc_html($post->post_title); ?>
                                        </a>
                                    </h5>
                                </div>
                                
                                <br>
                                <div class="date_box">
                                    Published on: <?php echo get_the_date('F j, Y', $post->ID); ?> 
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                // Append related posts HTML to the post content
                $content .= ob_get_clean();
            }
        }
        return $content;
    }

    // Function to retrieve related posts based on categories
    private function get_related_posts() {
        global $post;

        $categories = wp_get_post_categories($post->ID);
        if (empty($categories)) {
            return [];
        }

        // Query to fetch related posts
        $args = [
            'category__in'   => $categories,
            'post__not_in'   => [$post->ID],
            'posts_per_page' => 5,
            'orderby'        => 'rand'
        ];

        return get_posts($args);
    }

}
// Instantiate the plugin class
new Ra_Related_Posts();