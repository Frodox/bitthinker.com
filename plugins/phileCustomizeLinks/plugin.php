<?php

/**
 * Phile Customize Links plugin
 *
 * Use it, if you want to add extra params to external links.
 * Now available: rel="nofollow" and target="_blank" (both enabled by default)
 * 
 * @author Christian Evans <Christian@bitthinker.com>
 * @version 1.0
 * @link https://github.com/Jecomire/phileCustomizeLinks
 * @license http://opensource.org/licenses/MIT
 */

class PhileCustomizeLinks extends \Phile\Plugin\AbstractPlugin implements \Phile\EventObserverInterface
{

	public function __construct() {
		\Phile\Event::registerEvent('after_parse_content', $this);
	}

	public function on($eventKey, $data = null)
	{
		if ($eventKey == 'after_parse_content')
		{
			$content  = $data['content'];

			$config   = \Phile\Registry::get('Phile_Settings');
			$base_url = $config['base_url'];
			$domain   = $this->get_domain($base_url);


			/* modify <a> tags, if find any
			 * don't edit, if "href":
			 * * starts with '/', '#'
			 * * contain 'domain' name
			 **/

			$dom = new DOMDocument();
			// oh.. bugging DomDocument
			$rightEncodingHtml = mb_convert_encoding($content, 'HTML-ENTITIES', $this->settings["encoding"]);
			$dom->loadHTML( $rightEncodingHtml );

			foreach ( $dom->getElementsByTagName("a") as $a_tag )
			{
				if ( !$a_tag->hasAttribute("href"))
					continue;

				$href_url = $a_tag->getAttribute("href");
				$start_with_slash = ($href_url[0] == '/') ? true : false;
				$start_with_hash  = ($href_url[0] == '#') ? true : false;
				// case sensetive...
				$contain_domain = (substr_count($href_url, $domain) >=1 ) ? true : false;

				if ($start_with_slash || $contain_domain || $start_with_hash)
					continue;

				//echo "external url: " . $href_url . "\n<br>";
				$a_tag->setAttribute("target", $this->settings["target"]);
				$a_tag->setAttribute("rel",    $this->settings["rel"]);
			}

			$data['content'] = $dom->saveHTML();
		}
	}


/**
 * Get domain name from URL.
 * Seems to return *with* subdomain.
 *
 * @return
 * 	domain_name, if success
 * 	false, otherwise
 **/
	private function get_domain($url = NULL)
	{
		if (is_null($url))
			return false;

		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : false;

		return $domain;
	}
}
