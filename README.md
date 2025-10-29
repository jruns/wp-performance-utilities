# wp-performance-utilities
Performance Utilities is a WordPress plugin to improve the performance of a WordPress website. It includes several utilities that can be activated and configured in order to improve various aspects of your website.

**Utilities included:**  
- Disable jQuery Migrate
- Remove versions from external scripts and styles
- Replace YouTube iframes with a placeholder image (facade)
- Move scripts or styles to the page footer on specific pages
- Remove scripts or styles from specific pages
- Delay execution of scripts and styles until the page has loaded or the user has interacted with the page.
- Preload images on specific pages to improve Largest Contentful Paint (LCP).

## Utilities Documentation

### Disable jQuery Migrate
This utility removes the jQuery Migrate javascript file from the frontend. It does not remove it from the admin section.  

Add this to your wp-config.php file to activate the utility:
```php
define( 'PERFUTILS_DISABLE_JQUERY_MIGRATE', true );
```
Or activate the utility on the Performance Utilities wp-admin options page.

### Remove Versions
This utility removes versions from the source urls of external scripts and styles on the frontend. This can improve browser and CDN caching. 

Add this to your wp-config.php file to activate the utility:
```php
define( 'PERFUTILS_REMOVE_VERSIONS', true );
```
Or activate the utility on the Performance Utilities wp-admin options page.

### Enable YouTube Facade
This utility replaces YouTube iframes with an image placeholder (facade) on any frontend posts or pages. This delays initializing the video until the user clicks on the placeholder image, at which time the video will load and autoplay. The image placeholder is lazy loaded by default. And if the user has javascript disabled, clicking on the image will open the video in a new tab.

Add this to your wp-config.php file to activate the utility:  
```php
define( 'PERFUTILS_ENABLE_YOUTUBE_FACADE', true );
```
Or activate the utility on the Performance Utilities wp-admin options page.  

### Move Scripts and Styles to Footer
This utility moves specified javascript scripts and css styles to the page footer on the frontend. It does not move them in the admin section.  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'PERFUTILS_MOVE_SCRIPTS_AND_STYLES_TO_FOOTER', true );
```

Then specify scripts and styles to be moved with the `perfutils_scripts_and_styles_to_move_to_footer` filter by adding something like the following to your functions.php file:  
```php
function move_scripts_and_styles_to_footer( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => 'path_sample-page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'perfutils_scripts_and_styles_to_move_to_footer', 'move_scripts_and_styles_to_footer', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

#### Filter settings options for style and link tags:
Add individual configurations by adding a new array to the `styles` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `href`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

### Remove Scripts and Styles
This utility removes specified javascript scripts and css styles from the frontend. It does not remove them from the admin section.  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'PERFUTILS_REMOVE_SCRIPTS_AND_STYLES', true );
```

Then specify scripts and styles to be removed with the `perfutils_scripts_and_styles_to_remove` filter by adding something like the following to your functions.php file:  
```php
function remove_scripts_and_styles( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => array( 'path_sample-page', 'is_page' )
    );
    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'perfutils_scripts_and_styles_to_remove', 'remove_scripts_and_styles', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

#### Filter settings options for style and link tags:
Add individual configurations by adding a new array to the `styles` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `href`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

### Delay Scripts and Stylesheets
This utility allows you to selectively delay inline and external javascript on your site, and asynchronously load external stylesheets. For scripts you can choose from a default delay which will add `defer` to `<script>` tags, a page loaded delay that will delay a script from executing until the page has loaded, or a user interaction delay that will delay a script from executing until the user has interacted with the page. There is a default autoload delay for the user interaction delay that you can modify. For external stylesheets there is only one type of delay which modifies the stylesheet to load asynchronously.   

If you have any script configured to use the user_interaction delay you can also take advantage of the `DOMUserInteraction` browser event that we attach to the browser document.  

Any script tag with one of the following attributes will be excluded from being delayed: `nodelay`, `nowprocket`, `data-pagespeed-no-defer`  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'PERFUTILS_DELAY_SCRIPTS_AND_STYLES', true );
```
Or activate the utility on the Performance Utilities wp-admin options page.  

To modify the default user interaction autoload delay of 15000 milliseconds (15s), you can add this to your wp-config.php file:  
```php
define( 'PERFUTILS_DELAY_SCRIPTS_AND_STYLES_AUTOLOAD_DELAY', 15000 );
```
Or override the default value in the Performance Utilities wp-admin options page.  
The value entered should be in milliseconds. This is a failsafe that will ensure that a script will still load even if the user takes longer to interact with the page.  

Then specify the scripts or stylesheets to be delayed with the `perfutils_scripts_and_styles_to_delay` filter by adding something like the following to your functions.php file:  
```php
function delay_scripts_and_styles( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => array( 'wc-add-to', 'js-cookie' )
    );
    
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'add-to-cart.min.js',
        'args'      => array(
            'operation'     => 'user_interaction'
        )
    );

    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'wpemojiSettings',
        'where'     => 'is_page',
        'args'      => array(
            'operation'     => 'page_loaded',
            'delay'         => '5000'
        )
    );

    $settings['styles'][] = array( 
        'match'     => 'id',
        'find'      => 'dashicons-css'
    );

    return $settings;
}
add_filter( 'perfutils_scripts_and_styles_to_delay', 'delay_scripts_and_styles', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |
| args      | Optional | Array. Available keys: `operation` and `delay`. `delay` must be in milliseconds.<br/>Available operations: `page_loaded` and `user_interaction` |

#### Filter settings options for stylesheet link tags:
Add individual configurations by adding a new array to the `styles` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `href` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

#### How to use the new DOMUserInteraction event:
You must first have at least one script being delayed on a specified page with the `perfutils_scripts_to_delay` filter. Then the new event will be available on that page.  
```php
document.addEventListener( "DOMUserInteraction", () => {
  console.log( "User has interacted with the page" );
});
```

### Preload Images
This utility allows you to preload specific images on the frontend. This can improve Largest Contentful Paint (LCP) when used selectively.

Add this to your wp-config.php file to enable the utility:  
```php
define( 'PERFUTILS_PRELOAD_IMAGES', true );
```

Then specify images to be preloaded with the `perfutils_images_to_preload` filter by adding something like the following to your functions.php file:  
```php
function images_to_preload( $settings ) {
    $settings['images'][] = array(
        'url'       => '/img/page-hero.png',
        'where'     => 'is_single'
    );

    $settings['images'][] = array(
        'url'       => '/img/tablet-hero.jpg',
        'where'     => 'is_front_page',
        'args'      => array(
            'media'     => '(width <= 1024px)'
        )
    );

    $settings['images'][] = array(
        'url'       => '/img/desktop-hero.jpg',
        'where'     => 'is_front_page',
        'args'      => array(
            'media'     => '(width > 1024px)'
        )
    );

    return $settings;
}
add_filter( 'perfutils_images_to_preload', 'images_to_preload', 10, 1 );
```

#### Filter settings options for images:
Add individual configurations by adding a new array to the `images` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| url       | Required | String. Absolute or relative path to the url for the image to be preloaded. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |
| args      | Optional | Array. Available keys: `media`.<br/><br/>`media` must be a String and a valid media query. Because responsive preload has no notion of "order" or "first match", you'll need to translate breakpoints into queries that can stand on their own.<br/>Examples: `(width <= 600px)`, `(width > 600px)`, `(max-width: 400px)`, `(min-width: 400.1px) and (max-width: 800px)`, `(min-width: 800.1px)` |
