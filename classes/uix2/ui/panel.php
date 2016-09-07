<?php
/**
 * UIX Panel
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * UIX Page class
 * @package uix2\ui
 * @author  David Cramer
 */
class panel extends \uix2\data\data{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'panel';

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        ?><style type="text/css">
        #<?php echo $this->id(); ?> > .uix-panel-tabs > li[aria-selected="true"] a {
            box-shadow: 3px 0 0 <?php echo $this->base_color(); ?> inset;
        }
        #<?php echo $this->id(); ?>.uix-top-tabs > .uix-panel-tabs > li[aria-selected="true"] a {
            box-shadow: 0 3px 0 <?php echo $this->base_color(); ?> inset;
        }
        
        </style>
        <?php
    }

    /**
     * Define core panel styles
     *
     * @since 2.0.0
     * @access public
     */
    public function uix_styles() {
        $styles = array(
            'panel'    =>  $this->url . 'assets/css/uix-panel' . $this->debug_styles . '.css',
        );
        $this->styles( $styles );
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
            'panel'        =>  $this->url . 'assets/js/uix-panel' . $this->debug_scripts . '.js'
        );
        $this->scripts( $scripts );
    }

    /**
     * Get Data from all controls of this section
     *
     * @since 2.0.0
     * @see \uix2\load
     * @param string $slug Slug of the section to get data for
     * @return array $data Array of sections data structured by the controls
     */
    public function get_data(){

            if( !empty( $this->child ) ){
                $data = array();
                foreach( $this->child as $child ) {
                    while( $child->has_data() ){
                        $this->the_data();
                        $data[ $this->index ][ $child->slug ] = $child->get_data();
                    }
                    $child->reset_index();
                }
            }
        return null;
    }

    /**
     * Sets the data for all children
     *
     * @since 2.0.0
     * @access public
     */    
    public function set_data( $data ){

        if( empty( $this->child ) ){ return; }

        foreach( (array) $data as $data_instance ){

            foreach( $this->child as $child ){
                // reset the child index.            
                if( isset( $data_instance[ $child->slug ] ) ){
                    $child->reset();
                    foreach( $data_instance[ $child->slug ] as $value ){
                        $child->the_data();
                        $child->set_data( $value );
                    }
                }
            }

        }
        // set to this data
        $this->data[ $this->index ] = $data;

    }

    /**
     * Render the panel
     *
     * @since 2.0.0
     * @access public
     */
    public function render(){
        
        if( empty( $this->child ) ){ return; }
        $tabs_class = '';
        if( count( $this->child ) > 1 )
            $tabs_class = ' uix-has-tabs';
        if( !empty( $this->struct['top_tabs'] ) )
            $tabs_class .= ' uix-top-tabs';

        echo '<div id="' . esc_attr( $this->id() ) . '" class="uix-' . esc_attr( $this->type ) . '-inside uix-panel-inside ' . $tabs_class . '">';
        
        if( count( $this->child ) > 1 ){
                echo '<ul class="uix-' . esc_attr( $this->type ) . '-tabs uix-panel-tabs">';
                $active = 'true';
                foreach( $this->child as $child ){
                    
                    $label = esc_html( $child->struct['label'] );

                    if( !empty( $child->struct['icon'] ) ){
                        $label = '<i class="dashicons ' . $child->struct['icon'] . '"></i><span class="label">' . esc_html( $child->struct['label'] ) . '</span>';
                    }
                    echo '<li aria-selected="' . esc_attr( $active ) . '">';
                        echo '<a href="#' . esc_attr( $child->id() ) . '" data-parent="' . esc_attr( $this->id() ) . '" class="uix-tab-trigger">' . $label . '</a>';
                    echo '</li>';

                    $active = 'false';
                }
                echo '</ul>';
        }

            echo '<div class="uix-' . esc_attr( $this->type ) . '-sections uix-sections">';
                $hidden = 'false';
                foreach( $this->child as $child ){
                    $child->struct['active'] = $hidden;
                    $child->render();
                    $hidden = 'true';
                }
            echo '</div>';

        echo '</div>';

        // render if repeat
        $this->render_repeater();

    }

    
}