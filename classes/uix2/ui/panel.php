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
        #<?php echo 'panel-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ); ?> > .uix-panel-tabs > li[aria-selected="true"] a {
            box-shadow: 3px 0 0 <?php echo $this->base_color(); ?> inset;
        }
        #<?php echo 'panel-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ); ?>.uix-top-tabs > .uix-panel-tabs > li[aria-selected="true"] a {
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
        $data = array();
        if( !empty( $this->child ) ){
            foreach( $this->child as $child ) {
                $data[ $child->slug ] = $child->get_data();
            }
        }

        return $data;
    }

    /**
     * Sets the data for all children
     *
     * @since 2.0.0
     * @access public
     */    
    public function set_data( $data ){
        if( empty( $this->child ) ){ return; }

        foreach( $this->child as $child ){
            if( isset( $data[ $child->slug ] ) )
                $child->set_data( $data[ $child->slug ] );
        }

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

        echo '<div id="panel-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ) . '" class="uix-' . esc_attr( $this->type ) . '-inside uix-panel-inside ' . $tabs_class . '">';
        
        if( count( $this->child ) > 1 ){
                echo '<ul class="uix-' . esc_attr( $this->type ) . '-tabs uix-panel-tabs">';
                $active = 'true';
                foreach( $this->child as $section ){
                    
                    $label = esc_html( $section->struct['label'] );

                    if( !empty( $section->struct['icon'] ) ){
                        $label = '<i class="dashicons ' . $section->struct['icon'] . '"></i><span class="label">' . esc_html( $section->struct['label'] ) . '</span>';
                    }
                    echo '<li aria-selected="' . esc_attr( $active ) . '">';
                        echo '<a href="#' . esc_attr( $section->slug . '-' . $this->slug ) . '" data-parent="panel-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ) . '" class="uix-tab-trigger">' . $label . '</a>';
                    echo '</li>';

                    $active = 'false';
                }
                echo '</ul>';
        }

            echo '<div class="uix-' . esc_attr( $this->type ) . '-sections uix-sections">';
                $hidden = 'false';
                foreach( $this->child as $section ){
                    $section->struct['active'] = $hidden;
                    $section->render();
                    $hidden = 'true';
                }
            echo '</div>';
        echo '</div>';
    }

    
}