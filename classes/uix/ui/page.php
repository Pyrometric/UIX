<?php
/**
 * UIX Pages
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX Page class for creating admin/settings pages.
 * @package uix\ui
 * @author  David Cramer
 */
class page extends box implements \uix\data\save{

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'page';

    /**
     * Holds the option screen prefix
     *
     * @since 1.0.0
     * @access public
     * @var      array
     */
    public $screen_hook_suffix = array();


    /**
     * setup actions and hooks to add settings pate and save settings
     *
     * @since 1.0.0
     * @access protected
     */
    protected function actions() {
        // run parent actions ( keep 'admin_head' hook )
        parent::actions();

        if( empty( $this->struct[ 'page_title' ] ) || empty( $this->struct['menu_title'] ) )
            return;


        $this->setup_pages();

    }

    /**
     * Adds settings page to adminh
     *
     * @since 1.0.0
     * @access private
     */
    private function setup_pages(){

        $args = array(
            'capability'    => 'manage_options',
            'icon'          =>  null,
            'position'      => null
        );
        $this->struct['args'] = array_merge( $args, $this->struct );

        if( !isset( $this->struct['parent'] ) ) {
            add_action('admin_menu', array($this, 'add_settings_page'), 9);
        }else{
            add_action('admin_menu', array($this, 'add_sub_page'), 9);
        }

    }


    /**
     * Define core page styles
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {

        $this->assets['style']['page'] =  $this->url . 'assets/css/page' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }


    /**
     * get a UIX config store key
     * @since 1.0.0
     * @access public
     * @return string $store_key the defiuned option name for this UIX object
     */
    public function store_key(){

        if( !empty( $this->struct['store_key'] ) ){
            $store_key = $this->struct['store_key'];
        }else{
            $store_key = 'uix-' . $this->type . '-' . sanitize_text_field( $this->slug );
        }

        return $store_key;
    }

    /**
     * Determin if a page is to be loaded and set it active
     * @since 1.0.0
     * @access public
     */
    public function is_active(){
        if( !is_admin() ){ return false; }

        // check that the screen object is valid to be safe.
        $screen = get_current_screen();
        return $screen->base === $this->screen_hook_suffix;

    }

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){

        ?><style type="text/css">
            .contextual-help-tabs .active {
                border-left: 6px solid <?php echo $this->base_color(); ?> !important;
            }
            #page-<?php echo $this->id(); ?> h1{
                box-shadow: 0 0 2px rgba(0, 2, 0, 0.1),11px 0 0 <?php echo $this->base_color(); ?> inset;
            }
             #page-<?php echo $this->id(); ?> .page-title-action:hover{
                background: <?php echo $this->base_color(); ?>;
                border-color: rgba(0,0,0,0.1);
            }
             #page-<?php echo $this->id(); ?> .page-title-action:focus{
                box-shadow: 0 0 2px <?php echo $this->base_color(); ?>;
                border-color: <?php echo $this->base_color(); ?>;
            }
            <?php if( count( (array) $this->child ) > 1 ){ ?>
            #page-<?php echo $this->id(); ?> h1{
                box-shadow: 0 0px 13px 12px <?php echo $this->base_color(); ?>, 11px 0 0 <?php echo $this->base_color(); ?> inset;
            }
            <?php } ?>
        </style>
        <?php

    }

    /**
     * Add the settings page
     *
     * @since 1.0.0
     * @access public
     * @uses "admin_menu" hook
     */
    public function add_settings_page(){

        $this->screen_hook_suffix = add_menu_page(
            $this->struct['args'][ 'page_title' ],
            $this->struct['args'][ 'menu_title' ],
            $this->struct['args'][ 'capability' ],
            $this->slug,
            array( $this, 'create_page' ),
            $this->struct['args'][ 'icon' ],
            $this->struct['args'][ 'position' ]
        );

    }

    /**
     * Add the sub settings page
     *
     * @since 1.0.0
     * @access public
     * @uses "admin_menu" hook
     */
    public function add_sub_page(){

        $this->screen_hook_suffix = add_submenu_page(
            $this->struct['args'][ 'parent' ],
            $this->struct['args'][ 'page_title' ],
            $this->struct['args'][ 'menu_title' ],
            $this->struct['args'][ 'capability' ],
            $this->slug,
            array( $this, 'create_page' )
        );

    }

    /**
     * create the admin page
     *
     * @since 1.0.0
     * @access public
     */
    public function create_page(){
        echo $this->render();
    }

    /**
     * Render the page
     *
     * @since 1.0.0
     * @access public
     * @return string HMTL of rendered page
     */
    public function render(){
        $output = null;

        $output .= '<form enctype="multipart/form-data" method="post" class="wrap uix-page" data-uix="' . esc_attr( $this->slug ) . '" id="page-' . esc_attr( $this->id() ) . '">';
        $output .= '<h1 class="uix-title">' . esc_html( $this->struct['page_title'] );
        if( !empty( $this->struct['version'] ) )
            $output .= '<small>' . esc_html( $this->struct['version'] ) . '</small>';

        if( !empty( $this->struct['save_button'] ) )
            $output .= '<button type="submit" class="page-title-action">' . esc_html( $this->struct['save_button'] ) . '</button>';

        $output .= '</h1>';
        $output .= parent::render();

        $output .= '</form>';

        render $output;
    }
    
}