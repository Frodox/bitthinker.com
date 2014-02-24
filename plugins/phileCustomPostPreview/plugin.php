<?php

/**
 * Phile Custom Post Preview Plugin
 *
 * Use it, if you want to get rendered html-text,
 * breaked at some point (aka improved page.custom_post_preview) for blog.
 * See README.md for more info
 * 
 * @author Christian Evans <Christian@bitthinker.com>
 * @version 1.0
 * @link https://github.com/Jecomire/phileCustomPostPreview
 * @license http://opensource.org/licenses/gpl-3.0.html
 */

class PhileCustomPostPreview extends \Phile\Plugin\AbstractPlugin implements \Phile\EventObserverInterface
{
	public function __construct() {
		\Phile\Event::registerEvent('template_engine_registered', $this);
	}

	public function on($eventKey, $data = null)
	{
		if ($eventKey == 'template_engine_registered')
		{
			$custom_post_preview = new Twig_SimpleFilter('custom_post_preview', function ($string) 
			{
				$pos = 0;
				$pos = stripos ( $string, $this->settings["read_more_tag"] );
				$cutted = $string;

				if ($pos !== false) {
					/* if we find $read_more_tag in the content(parsed one) */
					/* NEED: php 5.3.0+ */
					$cutted = stristr($string, $this->settings["read_more_tag"], true);
					$cutted .= $this->settings["read_more_text"];
				}

				return $cutted;
			});
			$data['engine']->addFilter($custom_post_preview);
		}
	}
}
