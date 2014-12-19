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
                    $html = minify_html_ci($html);
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
		if(strpos($buffer,'<pre>') !== false)
		{
			$replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\r/"                      => '',
                "/>\n</"                    => '><',
                "/>\s+\n</"					=> '><',
                "/>\n\s+</"					=> '><',
            );
		}
		else
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
		}
    	$buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
	}

    return $buffer;
}

	/**
	 * Minify
	 *
	 * Reduce excessive size of HTML/CSS/JavaScript content.
	 *
	 * @param	string	$output	Output to minify
	 * @param	string	$type	Output content MIME type
	 * @return	string	Minified output
	 */
	function minify_html_ci($output, $type = 'text/html')
	{
		switch ($type)
		{
			case 'text/html':

				if (($size_before = strlen($output)) === 0)
				{
					return '';
				}

				// Find all the <pre>,<code>,<textarea>, and <javascript> tags
				// We'll want to return them to this unprocessed state later.
				preg_match_all('{<pre.+</pre>}msU', $output, $pres_clean);
				preg_match_all('{<code.+</code>}msU', $output, $codes_clean);
				preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_clean);
				preg_match_all('{<script.+</script>}msU', $output, $javascript_clean);

				// Minify the CSS in all the <style> tags.
				preg_match_all('{<style.+</style>}msU', $output, $style_clean);
				foreach ($style_clean[0] as $s)
				{
					$output = str_replace($s, _minify_js_css($s, 'css', TRUE), $output);
				}

				// Minify the javascript in <script> tags.
				foreach ($javascript_clean[0] as $s)
				{
					$javascript_mini[] = _minify_js_css($s, 'js', TRUE);
				}

				// Replace multiple spaces with a single space.
				$output = preg_replace('!\s{2,}!', ' ', $output);

				// Remove comments (non-MSIE conditionals)
				$output = preg_replace('{\s*<!--[^\[<>].*(?<!!)-->\s*}msU', '', $output);

				// Remove spaces around block-level elements.
				$output = preg_replace('/\s*(<\/?(html|head|title|meta|script|link|style|body|table|thead|tbody|tfoot|tr|th|td|h[1-6]|div|p|br)[^>]*>)\s*/is', '$1', $output);

				// Replace mangled <pre> etc. tags with unprocessed ones.

				if ( ! empty($pres_clean))
				{
					preg_match_all('{<pre.+</pre>}msU', $output, $pres_messed);
					$output = str_replace($pres_messed[0], $pres_clean[0], $output);
				}

				if ( ! empty($codes_clean))
				{
					preg_match_all('{<code.+</code>}msU', $output, $codes_messed);
					$output = str_replace($codes_messed[0], $codes_clean[0], $output);
				}

				if ( ! empty($textareas_clean))
				{
					preg_match_all('{<textarea.+</textarea>}msU', $output, $textareas_messed);
					$output = str_replace($textareas_messed[0], $textareas_clean[0], $output);
				}

				if (isset($javascript_mini))
				{
					preg_match_all('{<script.+</script>}msU', $output, $javascript_messed);
					$output = str_replace($javascript_messed[0], $javascript_mini, $output);
				}

				$size_removed = $size_before - strlen($output);
				$savings_percent = round(($size_removed / $size_before * 100));
			break;

			case 'text/css':

				return _minify_js_css($output, 'css');

			case 'text/javascript':
			case 'application/javascript':
			case 'application/x-javascript':

				return _minify_js_css($output, 'js');

			default: break;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Minify JavaScript and CSS code
	 *
	 * Strips comments and excessive whitespace characters
	 *
	 * @param	string	$output
	 * @param	string	$type	'js' or 'css'
	 * @param	bool	$tags	Whether $output contains the 'script' or 'style' tag
	 * @return	string
	 */
	function _minify_js_css($output, $type, $tags = FALSE)
	{
		if ($tags === TRUE)
		{
			$tags = array('close' => strrchr($output, '<'));

			$open_length = strpos($output, '>') + 1;
			$tags['open'] = substr($output, 0, $open_length);

			$output = substr($output, $open_length, -strlen($tags['close']));

			// Strip spaces from the tags
			$tags = preg_replace('#\s{2,}#', ' ', $tags);
		}

		$output = trim($output);

		if ($type === 'js')
		{
			// Catch all string literals and comment blocks
			if (preg_match_all('#((?:((?<!\\\)\'|")|(/\*)|(//)).*(?(2)(?<!\\\)\2|(?(3)\*/|\n)))#msuUS', $output, $match, PREG_OFFSET_CAPTURE))
			{
				$js_literals = $js_code = array();
				for ($match = $match[0], $c = count($match), $i = $pos = $offset = 0; $i < $c; $i++)
				{
					$js_code[$pos++] = trim(substr($output, $offset, $match[$i][1] - $offset));
					$offset = $match[$i][1] + strlen($match[$i][0]);

					// Save only if we haven't matched a comment block
					if ($match[$i][0][0] !== '/')
					{
						$js_literals[$pos++] = array_shift($match[$i]);
					}
				}
				$js_code[$pos] = substr($output, $offset);

				// $match might be quite large, so free it up together with other vars that we no longer need
				unset($match, $offset, $pos);
			}
			else
			{
				$js_code = array($output);
				$js_literals = array();
			}

			$varname = 'js_code';
		}
		else
		{
			$varname = 'output';
		}

		// Standartize new lines
		$$varname = str_replace(array("\r\n", "\r"), "\n", $$varname);

		if ($type === 'js')
		{
			$patterns = array(
				'#\s*([!\#%&()*+,\-./:;<=>?@\[\]^`{|}~])\s*#'	=> '$1',	// Remove spaces following and preceeding JS-wise non-special & non-word characters
				'#\s{2,}#'					=> ' '		// Reduce the remaining multiple whitespace characters to a single space
			);
		}
		else
		{
			$patterns = array(
				'#/\*.*(?=\*/)\*/#s'	=> '',		// Remove /* block comments */
				'#\n?//[^\n]*#'		=> '',		// Remove // line comments
				'#\s*([^\w.\#%])\s*#U'	=> '$1',	// Remove spaces following and preceeding non-word characters, excluding dots, hashes and the percent sign
				'#\s{2,}#'		=> ' '		// Reduce the remaining multiple space characters to a single space
			);
		}

		$$varname = preg_replace(array_keys($patterns), array_values($patterns), $$varname);

		// Glue back JS quoted strings
		if ($type === 'js')
		{
			$js_code += $js_literals;
			ksort($js_code);
			$output = implode($js_code);
			unset($js_code, $js_literals, $varname, $patterns);
		}

		return is_array($tags)
			? $tags['open'].$output.$tags['close']
			: $output;
	}