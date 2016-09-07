<?php

/**
 * Base data interface
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\data;

abstract class data extends \uix2\ui\uix{

    /**
     * object data
     *
     * @since 2.0.0
     * @access private
     * @var     array
     */
    private $data;

    /**
     * The current instance
     *
     * @since       2.0.0
     * @access public
     * @var         int
     */
    public $index = -1;

    /**
     * repeater template
     *
     * @since       2.0.0
     * @access public
     * @var         uix/template
     */
    public $template;

    /**
     * Sets the objects sanitization filter
     *
     * @since 2.0.0
     * @access public
     * @see \uix2\uix
     */
    public function setup() {

        foreach ( $this->struct as $struct_key=>$sub_struct ){
            if( is_array( $sub_struct ) && uix2()->get_register_callback( $struct_key ) ){
                foreach( $sub_struct as $sub_slug => $sub_structure ){
                    $this->{$struct_key}( $sub_slug, $sub_structure );
                    if( !empty( $sub_structure['repeat'] ) ){         
                        $sub_structure['repeat'] = false;
                        uix2()->add( 'template', $this->id(), array() )->{$struct_key}( $sub_slug, $sub_structure );
                    }
                }
            }
        }

        if( !empty( $this->struct['sanitize_callback'] ) )
            add_filter( 'uix_' . $this->slug . '_sanitize_' . $this->type, $this->struct['sanitize_callback'] );



    }

    /**
     * uix object id
     *
     * @since 2.0.0
     * @access public
     * @return string The object ID
     */
    public function id(){
        $id = parent::id();
        if( !empty( $this->parent ) )
            $id = $this->parent->id() . '-' . $id;
        if( empty( $this->child ) )
            $id .= '_' . $this->index ;
        return $id;
    }


    /**
     * set the object's data
     * @since 2.0.0
     * @access public
     * @param mixed $data the data to be set
     */
    public function set_data( $data ){
        if( $this->index <= -1 )
            $this->the_data();

        $this->data[ $this->index ] = apply_filters( 'uix_' . $this->slug . '_sanitize_' . $this->type, $data, $this );
    }


    /**
     * get the object's data
     * @since 2.0.0
     * @access public
     * @return mixed $data
     */
    public function get_data(){
        if( $this->has_data( $this->data[ $this->index ] ) )
            return $this->data[ $this->index ];
        return null;
    }


    /**
     * has data
     * @since 2.0.0
     * @access public
     * @return mixed $data
     */
    public function has_data(){
        if( $this->index <= -1 ){
            return true;
        }
        return isset( $this->data[ $this->index ] );
    }

    /**
     * has data
     * @since 2.0.0
     * @access public
     * @return mixed $data
     */
    public function the_data(){
        $this->index++;
    }

    /**
     * has data
     * @since 2.0.0
     * @access public
     * @return mixed $data
     */
    public function reset_index(){
        $this->index = -1;
    }



    /**
     * Render repeatable input structs
     *
     * @since 2.0.0
     * @access public
     */
    public function render_repeater(){
        if( !empty( $this->struct['repeat'] ) )
            echo '<button class="button uix-repeatable" data-for="' . esc_attr( $this->id() ) . '" data-slug="' . esc_attr( $this->slug ) . '" type="button">Add Another</button>';
    }

}
