<?php
/**
 * UIX Modal
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * Same as the Box type, however it renders a button control that loads the modal via a template
 * 
 * @package uix\ui
 * @author  David Cramer
 */
class modal extends box{

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'modal';

    /**
     * footer object
     *
     * @since 1.0.0
     * @access public
     * @var      footer
     */
    public $footer;

    /**
     * Sets the controls data
     *
     * @since 1.0.0
     * @see \uix\uix
     * @access public
     */
    public function init(){
        // run parents to setup sanitization filters
        parent::init();
        $data = uix()->request_vars('post');
        if (isset($data['uixNonce_' . $this->id()]) && wp_verify_nonce($data['uixNonce_' . $this->id()], $this->id())){
            wp_send_json_success( $this->get_data() );
        }
    }
        /**
     * Render the footer template
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function set_footers(){
        if( !empty( $this->child ) ){
            foreach ($this->child as $child_slug=>$child){
                if ( in_array($child->type, array('footer') ) ){
                    $this->footer = $child;
                    $this->attributes['data-footer'] = '#' . $this->id() . '-footer-tmpl';
                }
            }
        }
    }
    /**
     * Sets the wrappers attributes
     *
     * @since 1.0.0
     * @access public
     */
    public function set_attributes(){

        $this->attributes = array(
            'data-modal'    =>  $this->id(),
            'data-content'  =>  '#' . $this->id() . '-tmpl',
            'data-title'    =>  $this->struct['label'],
            'data-margin'  =>  12,
            'data-element'  =>  'form',
            'class'         =>  'button',
        );

        parent::set_attributes();

    }

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        echo '<style>h3#' . $this->id() . '_uixModalLable { background: ' . $this->base_color() . '; }</style>';
        parent::enqueue_active_assets();
    }

    /**
     * set metabox styles
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     */
    public function set_assets() {

        $this->assets['script']['baldrick'] = array(
            'src' => $this->url . 'assets/js/jquery.baldrick' . UIX_ASSET_DEBUG . '.js',
            'deps' => array( 'jquery' ),
        );
        $this->assets['script']['modals'] = array(
            'src' => $this->url . 'assets/js/modals' . UIX_ASSET_DEBUG . '.js',
            'deps' => array( 'baldrick' ),
        );
        $this->assets['style']['modals'] = $this->url . 'assets/css/modals' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }


    /**
     * Render the Control
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render(){

        $this->set_footers();

        add_action( 'admin_footer', array( $this, 'render_modal_template' ) );
        add_action( 'wp_footer', array( $this, 'render_modal_template' ) );

        $output = '<button ' . $this->build_attributes() . '>' . $this->struct['label'] . '</button>';

        return $output;
    }

    /**
     * Render the Control
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render_modal_template(){
        unset( $this->struct['label'] );
        $output = '<script type="text/html" id="' . esc_attr( $this->id() ) . '-tmpl">';
        $output .= parent::render();
        $output .= '</script>';
        $output .= $this->render_footer_template();
        echo $output;
    }


    /**
     * Render the footer template
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render_footer_template(){
        $output = null;
        if ( !empty( $this->footer ) ){
            $output .= '<script type="text/html" id="' . esc_attr( $this->id() ) . '-footer-tmpl">';
            $output .= $this->footer->render();
            $output .= '</script>';
        }

        echo $output;
    }

}