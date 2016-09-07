<?php
/**
 * UIX tempalte
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * UIX sections class.
 *
 * @since 2.0.0
 * @see \uix2\uix
 */
class template extends uix {

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'template';

    /**
     * render footer templates
     *
     * @since 2.0.0
     * @access protected
     */
    protected function actions() {
        parent::actions();

        add_action( 'admin_footer', array( $this, 'render' ) );
    }

    /**
     * set metabox scripts
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public
     */
    public function uix_scripts() {
        $scripts = array(
            'template'      =>  array(
                'src'           =>  $this->url . 'assets/js/uix-template' . $this->debug_scripts . '.js',
                'deps'          => array( 'jquery' ),
                'in_footer'     => true
            )
        );
        $this->scripts( $scripts );
    }

    /**
     * Render the template
     *
     * @since 2.0.0
     * @access public
     */
    public function render(){        
        foreach ( $this->child as $child ) {
            echo '<script type="text/html" id="tmpl-' . esc_attr( $child->id() ) . '">';
                $child->render();
            echo '</script>';
        }

    }

    /**
     * Templates are alwys active.
     * @since 2.0.0
     * @access public
     */
    public function is_active(){
        return true;
    }

}