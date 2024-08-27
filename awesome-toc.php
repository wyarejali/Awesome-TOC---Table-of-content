<?php
/**
 * Plugin Name: Awesome TOC
 * Description: Adds a collapsible Table of Contents (TOC) to blog posts with an open/close feature.
 * Version: 1.0
 * Author: Wyarej Ali
 */

// Enqueue necessary scripts and styles
function toc_toggle_plugin_enqueue_scripts() {
    wp_enqueue_style( 'awesome-toc-style', plugin_dir_url( __FILE__ ) . 'css/awesome-toc-style.css' );
    wp_enqueue_script( 'awesome-toc-script', plugin_dir_url( __FILE__ ) . 'js/awesome-toc-script.js', array( 'jquery' ), null, true );
}

add_action( 'wp_enqueue_scripts', 'toc_toggle_plugin_enqueue_scripts' );

// Generate the TOC and place it after the first paragraph
function generate_toc_after_first_paragraph( $content ) {
    if ( is_singular( 'post' ) && is_main_query() ) {
        $toc = '<div class="awesome_toc-wrapper awesome_toc-open"><h3 class="awesome_toc-title">Table of Contents <span class="awesome_toc-toggle"></span></h3><ul class="awesome_toc-list">';

        preg_match_all( '/<h([2-6])[^>]*>(.*?)<\/h[2-6]>/', $content, $matches, PREG_SET_ORDER );
        $counters = array( 0, 0, 0, 0, 0 ); // For h2 to h6

        if ( !empty( $matches ) ) {
            foreach ( $matches as $match ) {
                $level = intval( $match[1] ) - 2;
                $counters[$level]++;

                // Reset deeper levels
                for ( $i = $level + 1; $i < 5; $i++ ) {
                    $counters[$i] = 0;
                }

                // Generate the numbering
                $numbering = $counters[0];
                for ( $i = 1; $i <= $level; $i++ ) {
                    if ( $counters[$i] > 0 ) {
                        $numbering .= '.' . $counters[$i];
                    }
                }

                // Create heading ID if not exists
                if ( !preg_match( '/id="[^"]+"/', $match[0] ) ) {
                    $heading_id      = sanitize_title( $match[2] );
                    $heading_with_id = sprintf( '<h%s id="%s">%s</h%s>', $match[1], $heading_id, $match[2], $match[1] );
                    $content         = str_replace( $match[0], $heading_with_id, $content );
                } else {
                    preg_match( '/id="([^"]+)"/', $match[0], $id_match );
                    $heading_id = $id_match[1];
                }

                // Add to TOC
                $toc .= sprintf( '<li class="awesome_toc-item awesome_toc-level-%s"><span class="awesome_toc-numbering">%s</span> <a href="#%s">%s</a></li>', $match[1], $numbering, $heading_id, $match[2] );
            }

            $toc .= '</ul></div>';

            // Insert the TOC after the first paragraph
            $content = insert_toc_after_first_paragraph( $content, $toc );
        }
    }

    return $content;
}

add_filter( 'the_content', 'generate_toc_after_first_paragraph' );

// Function to insert TOC after the first paragraph
function insert_toc_after_first_paragraph( $content, $toc ) {
    // Find the first paragraph in the content
    $split_content = explode( '</p>', $content );

    if ( isset( $split_content[1] ) ) {
        // Insert the TOC after the first paragraph
        $split_content[0] .= '</p>' . $toc;
        $content = implode( '</p>', $split_content );
    }

    return $content;
}
