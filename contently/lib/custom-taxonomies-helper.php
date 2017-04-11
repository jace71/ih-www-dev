<?php
/**
 * Class Contently_Custom_Taxonomies
 *
 * This is a library-helper for working with custom taxonomies
 * Version: 1.0
 * Author: Contently
 * Author URI: http://www.contently.com
 * License: GPL2
 *
 */


Class Contently_Custom_Taxonomies {

    /**
     * Get existing wordpress custom taxonomies
     *
     * @param string $type
     * @return array
     */
    public function cl_get_wp_custom_taxonomies($type = 'post'){
        $taxonomies = array();

        $wp_taxonomies = get_taxonomies(
            array(
                //'public' 		=> true,
                '_builtin' 		=> false,
                'object_type' 	=> array( $type )
            ),
            'objects'
        );

        foreach ( $wp_taxonomies as $key => $taxonomy ) {
            if ( is_integer( $key ) ) {
                $key = $taxonomy->name;
            }
            $taxonomies['wp_ct_' . $key] = $taxonomy->label;
        }

        unset( $wp_taxonomies );

        return $taxonomies;
    }

}