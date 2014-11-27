<?php

/*
    Author: Gulch
    Updated: 2014-06-01

    This is a redis caching system for wordpress.
    see more here: www.gulch.tk/worpress-redis-cache-by-gulch

    Originally written by Jeedo Aquino but improved by Gulch.

    !!! use this script at your own risk. i currently use this albeit a slightly modified version
    to display a redis badge whenever a cache is displayed.

*/

// change vars here
$cf = 1;			// set to 1 if you are using cloudflare
$debug = 0;			// set to 1 if you wish to see execution time and cache actions

$start = microtime(1);   // start timing page exec

// if cloudflare is enabled
if ($cf) 
{
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
    {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
}

// from wp
define('WP_USE_THEMES', true);

// init predis
include_once("/var/www/default/theta-redis/vendor/autoload.php");
$redis = new Predis\Client();

// init vars
$domain = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('reset_cache=all', '', $uri);
$uri = str_replace('reset_cache=page', '', $uri);

$suffix = url_slug($domain);
$suffix = $suffix.':';
$ukey = url_slug($uri);
if(!$ukey) $ukey = '__index';

// check if page isn't a comment submission
(isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') ? $submit = 1 : $submit = 0;

// check if logged in to wp
$cookie = var_export($_COOKIE, true);
$loggedin = preg_match("/wordpress_logged_in/", $cookie);

// check if a cache of the page exists
if ($redis->exists($suffix.$ukey) && !$loggedin && !$submit && !strpos($uri, '/feed/'))
{
    echo $redis->get($suffix.$ukey);
    $msg = 'get the page from cache';

    // if a comment was submitted or clear page cache request was made delete cache of page
}
elseif ($submit || strpos($_SERVER['REQUEST_URI'], 'reset_cache=page')) 
    {
        require('./wp-blog-header.php');
        $redis->del($suffix.$ukey);
        $msg = 'cache of page deleted';

    // delete entire cache, works only if logged in
    }
    elseif ($loggedin && strpos($_SERVER['REQUEST_URI'], 'reset_cache=all'))
        {
            require('./wp-blog-header.php');
            $keys = $redis->keys($suffix.'*');
            if (sizeof($keys))
            {
                foreach ($keys as $key) 
                {
                  $redis->del($key);
                }
                $msg = 'domain cache flushed';
            }
            else 
            {
                $msg = 'no cache to flush';
            }
        // if logged in don't cache anything
        }
        elseif ($loggedin)
            {
                require('./wp-blog-header.php');
                $msg = 'not cached';

            // cache the page
            }
            else 
            {
                // turn on output buffering
                ob_start();

                require('./wp-blog-header.php');

                // get contents of output buffer
                $html = ob_get_contents();

                // clean output buffer
                ob_end_clean();
                echo $html;

                // Store to cache only if the page exist and is not a search result.
                if (!is_404() && !is_search()) 
                {
                    // store html contents to redis cache
                    $html = minify_html($html);
                    $redis->set($suffix.$ukey, $html);
                    $msg = 'cache is set';
                }
            }

$end = microtime(1); // get end execution time

// show messages if debug is enabled
if ($debug) 
{
    echo '<!-- '.$msg.': '.round($end - $start,6).' seconds -->';
}

function url_slug($str)
{	
	// convert case to lower
	$str = strtolower($str);
	// remove special characters
	$str = preg_replace('/[^a-zA-Z0-9]/i',' ', $str);
	// remove white space characters from both side
	$str = trim($str);
	// remove double or more space repeats between words chunk
	$str = preg_replace('/\s+/', ' ', $str);
	// fill spaces with hyphens
	$str = preg_replace('/\s+/', '-', $str);
	return $str;
}

function minify_html($buffer)
{
	if($buffer)
	{
		$replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                "/ +/"                      => ' ',
            );
    	$buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
	}

    return $buffer;
}