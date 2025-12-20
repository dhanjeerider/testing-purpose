</section>
    
  <?php 
  $theme_opts = get_option('dhanjee_options', array(
      'footer_code' => '',
      'custom_js' => '',
  )); 
  ?>

    <?php if(!empty($theme_opts['footer_code'])): ?>
      <div class="header-adcode">  
        <?php echo wp_kses_post($theme_opts['footer_code']); ?>
      </div>  
    <?php endif; ?>

    <footer class="primay-footer"><div class="container"><div class="col-sm-6 footer-brand"><p> Copyright &copy; <?php echo date('Y'); ?> <span><?php echo esc_html(ucwords(parse_url(get_site_url(), PHP_URL_HOST))); ?></span> All Rights Reserved.</p></div><div class="col-sm-6 text-right"><div class="menu-secondary-container">
    <?php 
    if(has_nav_menu('footer')){
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'container' => false,
            'menu_class' => 'list-inline',
            'fallback_cb' => false,
        ));
    } else {
    ?>
    <ul id="menu-secondary" class="list-inline"><li id="menu-item-44364" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44364"><a href="<?php echo home_url('/how-to-download-movies/'); ?>">How to Download</a></li><li id="menu-item-44365" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44365"><a href="<?php echo home_url('/about-us/'); ?>">About Us</a></li><li id="menu-item-44366" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44366"><a href="<?php echo home_url('/disclaimer/'); ?>">Disclaimer</a></li><li id="menu-item-44367" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44367"><a href="<?php echo home_url('/dmca/'); ?>">DMCA</a></li><li id="menu-item-46538" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-46538"><a href="<?php echo home_url('/contact-us/'); ?>">Contact Us</a></li></ul>
    <?php } ?>
    </div></div><div class="clearfix"></div></div></footer>

<?php wp_footer(); 

// custom js
if(!empty($theme_opts['custom_js'])){
    echo '<script>';
        echo $theme_opts['custom_js'];
    echo '</script>';
}

?>

</section>
</body>
</html>