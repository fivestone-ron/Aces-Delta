<?php

/*
 * Force URLs in srcset attributes into HTTPS scheme.
 * This is particularly useful when you're running a Flexible SSL frontend like Cloudflare
 */
function ssl_srcset($sources)
{
    if (!empty($sources)) {
        foreach ($sources as &$source) {
            $source['url'] = set_url_scheme($source['url']);
        }
    }

    return $sources;
}

add_filter('wp_calculate_image_srcset', 'ssl_srcset');


add_filter('image_send_to_editor', 'image_to_relative', 5, 8);

function image_to_relative($html, $id, $caption, $title, $align, $url, $size, $alt)
{
    $sp = strpos($html, "src=") + 5;
    $ep = strpos($html, "\"", $sp);

    $imageurl = substr($html, $sp, $ep - $sp);

    $relativeurl = str_replace("http://", "", $imageurl);
    $sp = strpos($relativeurl, "/");
    $relativeurl = substr($relativeurl, $sp);

    $html = str_replace($imageurl, $relativeurl, $html);

    return $html;
}
