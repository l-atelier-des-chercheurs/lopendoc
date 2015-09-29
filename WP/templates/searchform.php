<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="sr-only"><?php _e('Search for:', 'roots'); ?></label>
  <div class="input-group">
    <input type="search" value="<?php echo get_search_query(); ?>" name="s" class="" placeholder="<?php _e('Recherche', 'opendoc'); ?>">
    <span class="">
      <button type="submit" class=""><?php _e('Rechercher sur l\'opendoc', 'roots'); ?></button>
    </span>
  </div>
</form>
