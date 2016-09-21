<?php
/**
 * UIX header
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * A generic holder for multiple controls. this panel type does not handle saving, but forms part of the data object tree.
 *
 * @since 1.0.0
 * @see \uix\uix
 */
class header extends section {

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'header';


    /**
     * Render the complete section
     *
     * @since 1.0.0
     * @access public
     * @return string|null HTML of rendered notice
     */
    public function render(){

        $output = '<h1 class="uix-title">';

        $output .= $this->label();

        $output .= $this->description();

        $output .= $this->render_template();

        if( !empty( $this->child ) )
            $output .= $this->render_section();



        $output .= '</h1>';


        return $output;
    }

    /**
     * Render the panels label
     *
     * @since 1.0.0
     * @access public
     * @return string|null rendered html of label
     */
    public function label(){
        $output = null;
        if( !empty( $this->struct['label'] ) )
            $output .= '<span class="uix-text">' . esc_html( $this->struct['label'] ) . '</span>';

        return $output;
    }

    /**
     * Render the panels Description
     *
     * @since 1.0.0
     * @access public
     * @return string|null HTML of rendered description
     */
    public function description(){
        $output = null;
        if( !empty( $this->struct['description'] ) )
            $output .= ' <small>' . esc_html( $this->struct['description'] ) . '</small>';

        return $output;
    }

    /**
     * checks if the current section is active
     *
     * @since 1.0.0
     * @access public
     */
    public function is_active(){
        return $this->parent->is_active();
    }

}