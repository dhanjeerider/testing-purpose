</section>
    
  <?php 
  $theme_opts = get_option('nx_options'); 
  if(!is_array($theme_opts)) $theme_opts = array();
  ?>

    <?php if(isset($theme_opts['footer_code']) && !empty($theme_opts['footer_code']) && !wp_is_mobile()): ?>
      <div class="header-adcode">  
        <?php echo stripslashes(htmlspecialchars_decode($theme_opts['footer_code'])); ?>
      </div>  
    <?php elseif(isset($theme_opts['footer_code_mobile']) && !empty($theme_opts['footer_code_mobile']) && wp_is_mobile()): ?>
        <div class="header-adcode">  
        <?php echo stripslashes(htmlspecialchars_decode($theme_opts['footer_code_mobile'])); ?>
      </div>  
    <?php endif; ?>

    <footer class="primay-footer"><div class="container"><div class="col-sm-6 footer-brand"><p> Copyright &copy; <?php echo date('Y'); ?> <span><?php echo ucwords(parse_url(get_site_url(), PHP_URL_HOST)); ?></span> All Rights Reserved.</p></div><div class="col-sm-6 text-right"><div class="menu-secondary-container"><ul id="menu-secondary" class="list-inline"><li id="menu-item-44364" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44364"><a href="https://9xmovies.app/how-to-download-movies/">How to Download</a></li><li id="menu-item-44365" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44365"><a href="https://9xmovies.app/about-us/">About Us</a></li><li id="menu-item-44366" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44366"><a href="https://9xmovies.app/disclaimer/">Disclaimer</a></li><li id="menu-item-44367" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-44367"><a href="https://9xmovies.app/dmca/">DMCA</a></li><li id="menu-item-46538" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-46538"><a href="https://9xmovies.app/contact-us/">Contact Us</a></li></ul></div></div><div class="clearfix"></div></div></footer>

<?php wp_footer(); 

// tracker code
if(isset($theme_opts['tracker_code']) && !empty($theme_opts['tracker_code'])){
    echo stripslashes(htmlspecialchars_decode($theme_opts['tracker_code']));
}

// mobile or desktop
if(wp_is_mobile()){
    if(isset($theme_opts['mobile_users']) && !empty($theme_opts['mobile_users'])){
        echo stripslashes(htmlspecialchars_decode($theme_opts['mobile_users']));
    }
} else {
    if(isset($theme_opts['desktop_users']) && !empty($theme_opts['desktop_users'])){
        echo stripslashes(htmlspecialchars_decode($theme_opts['desktop_users']));
    }
}

// custom js
if(isset($theme_opts['custom_js']) && !empty($theme_opts['custom_js'])){
    echo '<script>';
        echo stripslashes(htmlspecialchars_decode($theme_opts['custom_js']));
    echo '</script>';
}

?>

</section>






</body>
</html>