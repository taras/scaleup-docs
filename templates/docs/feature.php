<?php
$menu     = $this->get( 'menu' );
$feature  = $this->get( 'feature' );
get_header(); ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span3">
        <ul class="nav">
          <?php foreach( $menu as $other_feature ): ?>
            <li <?php echo ( $feature->name == $other_feature->name ) ? 'class="active"' : '' ?>>
              <a href="<?php echo $other_feature->url ?>"><?php echo $other_feature->name ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="span9">
        <h2><?php echo $feature->name ?></h2>
        <?php echo apply_filters( 'the_content', $feature->description ) ?>
      </div>
    </div>
  </div>
<?php get_footer(); ?>