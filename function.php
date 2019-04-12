/** Add rel nofollow to all links */
add_filter('the_content', 'my_nofollow');
add_filter('the_excerpt', 'my_nofollow');
function my_nofollow($content) {
    return preg_replace_callback('/<a[^>]+/', 'my_nofollow_callback', $content);
}
function my_nofollow_callback($matches) {
    $link = $matches[0];
    $site_link = get_bloginfo('url');
    if (strpos($link, 'rel') === false) {
        $link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow" $1', $link);
    } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
        $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
    }
    return $link;
}
/** Disable XML-RPC for security */
add_filter('xmlrpc_enabled', '__return_false');

/** Add class=js-imageZoom to all images */
function add_image_responsive_class($content) {
   global $post;
   $pattern ="/<img(.*?)class=\"(.*?)\"(.*?)>/i";
   $replacement = '<img$1class="$2 js-imageZoom"$3>';
   $content = preg_replace($pattern, $replacement, $content);
   return $content;
}
add_filter('the_content', 'add_image_responsive_class');

/** Добавляем Google Analytics */

function ns_google_analytics() { ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=CODE"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-56618694-1');
</script>

<?php
}
add_action( 'wp_head', 'ns_google_analytics', 10 );

