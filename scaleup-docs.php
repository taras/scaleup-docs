<?php
/**
 * Plugin Name: ScaleUp Docs Addon
 * Description: This addon can be added to an app to automatically provide documentation about your project.
 */

function scaleup_docs_addon_init() {

  class ScaleUp_Docs_Addon extends ScaleUp_Addon {

    function init() {

      $this->add( 'view',
        array(
          'name' => 'feature_docs',
          'url'  => '/feature/{feature_type}',
        ));

      $this->register( 'template',
        array(
          'name'     => 'feature_docs',
          'path'     => dirname( __FILE__ ) . '/templates',
          'template' => '/docs/feature.php',
        ));

    }

    function strip_off_asterisks( $text ) {

      $lines = explode( "\n", $text );
      foreach ( $lines as $line ) {
        $line = trim( $line );

      }

      return $text;
    }

    /**
     * Return an easy to work with object with feature type information
     *
     * @param $feature_type
     * @return stdClass|false
     */
    function _get_doc_object( $feature_type ) {

      $feature = false;

      $feature_args = ScaleUp::get_feature_type( $feature_type );
      if ( !isset( $feature_args[ 'exclude_docs' ] ) ) {
        $feature      = new stdClass();
        $feature_view = $this->get_feature( 'view', 'feature_docs' );

        $reflection           = new ReflectionClass( $feature_args[ '__CLASS__' ] );
        $feature->name        = ucwords( $feature_type );
        $feature->type        = $feature_type;
        $feature->properties  = $reflection->getProperties();

        $comment = preg_replace(array('#^/\*\*\s*#', '#\s*\*/$#', '#^\s*\*#m'), '', trim($reflection->getDocComment()));
        $comment = "\n".preg_replace('/(\r\n|\r)/', "\n", $comment);
        $feature->description = $comment;

        $feature->url         = $feature_view->get_url( array( 'feature_type' => $feature_type ) );
      }

      return $feature;
    }

    /**
     * Return array containing all registered feature types as easy to work with objects
     *
     * @return array
     */
    function _get_all_features() {

      $features   = array();
      $registered = ScaleUp::get_feature_types();
      foreach ( $registered as $feature_type => $feature_args ) {
        $features[] = $this->_get_doc_object( $feature_type );
      }

      return $features;
    }

    /**
     * Callback function for GET /feature/{feature_type} request
     *
     * @param $args
     * @return bool
     */
    function get_feature_docs( $args ) {

      $loaded = false;

      if ( isset( $args[ 'feature_type' ] ) && !empty( $args[ 'feature_type' ] ) ) {

        $feature_type = $args[ 'feature_type' ];

        if ( ScaleUp::is_registered_feature_type( $feature_type ) && $this->_get_doc_object( $feature_type ) ) {
          $feature      = $this->_get_doc_object( $feature_type );
          $menu         = $this->_get_all_features();
          $template     = $this->get_feature( 'template', 'feature_docs' );
          $template->set( 'feature', $feature );
          $template->set( 'menu', $menu );
          get_template_part( '/docs/feature.php' );
          $loaded = true;
        }

      }

      return $loaded;
    }

  }

  ScaleUp::register( 'addon',
    array(
      'name'      => 'docs',
      'url'       => '/docs',
      '__CLASS__' => 'ScaleUp_Docs_Addon'
    ));
}

add_action( 'scaleup_app_init', 'scaleup_docs_addon_init' );