<!--
Title: How to customize markdown link syntax?
Description: Short article about 2 ways of modifying markdown syntax (by php-plugin and by editing php-parser) for adding extra syntaz in html a-tags.
Tags: php, markdown, hacks, development
Date: 2013/11/01
-->

On my blog I have a lot of links on other sites. But it's good practice to use `rel="nofollow"`
if you add a link on untrusted site. Since I write articles in markdown, I find a way to customize
markdown's parser behevioure to allow this extra attributes for html `<a>` tags<!--cut-here-->.


## Idea

The idea is deadly simple -- just loop through all links, and if it is external one,
add `rel="nofollow" target="_blank"`. Which one is external? You can decide it for yourself.
For example, change syntax by adding `!` before URLs and check for that  
(`[1]:!http://my-site.com/article1`) and so on. My variant is below.

Link **is** external, if it is not relative/local :) So I do nothing if `href`:

* starts with `/` (relative links)
* starts with `#` (anchors links)

In result I have at least two ways to do that.


## Method #1 (php only)

If you use some flatfile CMS, like [Pico][pico-git] or [Phile][phile-git] or anything else,
it's a good practice to write a plugin. Since it's easy to realize with clean php, let's do it.



### Code it

My resulting plugin for PhileCMS is on [github][phileCustomizeLinks], and here is some demo-code:

```php
/**
 * Customize links in document.
 *
 * Use it, if you want to add extra params to external links.
 * Now available: rel="nofollow" and target="_blank".
 *
 * Modify <a> tags, if find any.
 * Don't edit, if "href":
 * * starts with '/', '#'
 **/
// ...
$content  = 'your page content';
$content_ecoding = '<YOUR CONTENT ENCODING>'; // like 'UTF-8'

$dom = new DOMDocument();
// convert to neutral 'html-entities' encoding first
$rightEncodingHtml = mb_convert_encoding($content, 'HTML-ENTITIES', $content_ecoding);
$dom->loadHTML( $rightEncodingHtml );

foreach ( $dom->getElementsByTagName("a") as $a_tag )
{
	if ( !$a_tag->hasAttribute("href"))
		continue;

	$href_url = $a_tag->getAttribute("href");
	$start_with_slash = ($href_url[0] == '/') ? true : false;
	$start_with_hash  = ($href_url[0] == '#') ? true : false;

	if ($start_with_slash || $start_with_hash)
		continue;

	$a_tag->setAttribute("target", "_blank");
	$a_tag->setAttribute("rel",    "nofollow");
}

$tmp  = preg_replace('/^<!DOCTYPE.+?>/'
	, ''
	, str_replace(array('<html>', '</html>', '<body>', '</body>')
			, array('', '', '', '')
	, $dom->saveHTML()));

// convert encoding back
$content = mb_convert_encoding($tmp, $content_ecoding, 'HTML-ENTITIES');
// ...
```

#### Prons

```diff
+ Need not to edit cms/parser core
```

#### Cons

```diff
- `saveHTML()` method will complement your code to *correct* html, with DOCTYPE, all closed tags and so on. Is it desired behaviour for you?
- If you use not-latin text, you should encode it explicitly into html-enteties and then back. If you use some html-code examples encoded into 'html-enteties', they will become back just html
```

* * *



## Method #2 (edit php-markdown parser)

If you cannot write a plugin or cons of method#1 is critical for you, let's do it deeper and simpler
-- during parsing `.md` and generating `html`-code.


### Code it

If you look at [php-markdown][2] library
(if you use another markdown-parser -- idea is the same):

There are two functions:

* `_doAnchors_reference_callback($matches)`
* `_doAnchors_inline_callback($matches)`

for links-reference and inline-links respectively.

> *NOTE*: actually, there are 4 functins, 2 for php-markdown and 2 for php-markdownExtra. Edit the one you are using.

Since I use php-markdownExtra, I edited [this][3] and [this][4] one function.

Resulting commits look like (on [github][my-github-commit]):

```diff
---
 Michelf/Markdown.php | 24 ++++++++++++++++++++++--
 1 file changed, 22 insertions(+), 2 deletions(-)

diff --git a/Michelf/Markdown.php b/Michelf/Markdown.php
index 088b7cd..1210fc0 100644
--- a/Michelf/Markdown.php
+++ b/Michelf/Markdown.php
@@ -2300,7 +2300,17 @@ protected function _doAnchors_reference_callback($matches) {
 		}
 		if (isset($this->ref_attr[$link_id]))
 			$result .= $this->ref_attr[$link_id];
+
+		/* check $url, if it external(absolute) or local(relative), so
+		 * do nothing, if $url is local:
+		 * starts with '/' or '#'
+		 **/
+		if ( $url[0] != '/' && $url[0] != '#' )
+		{
+			$result .= 'rel="nofollow" target="_blank"';
+		}
+
 		$link_text = $this->runSpanGamut($link_text);
 		$result .= ">$link_text</a>";
 		$result = $this->hashPart($result);

@@ -2326,7 +2336,17 @@ protected function _doAnchors_inline_callback($matches) {
 			$result .=  " title=\"$title\"";
 		}
 		$result .= $attr;
+
+		/* check $url, if it external(absolute) or local(relative), so
+		 * do nothing, if $url is local:
+		 * starts with '/' or '#'
+		 **/
+		if ( $url[0] != '/' && $url[0] != '#' )
+		{
+			$result .= 'rel="nofollow" target="_blank"';
+		}
+
 		$link_text = $this->runSpanGamut($link_text);
 		$result .= ">$link_text</a>";
-- 
```

#### Pros

```diff
+ easy to implement
+ no troubles with encodings
+ easy to cache result, if you use some cache-engine for resulting html output
```

#### Cons

```diff
- need to edit parser core
- cannot use full URL for your site inside articles (just relative, `/content...` )
```

---
Inspired by [this][1] answer on SO.


[1]:http://stackoverflow.com/a/11789091
(How to customize markdown link syntax)

[2]:http://michelf.ca/projects/php-markdown/extra/
(PHP Markdown Extra)

[3]:https://github.com/michelf/php-markdown/blob/efcf20752db06533b04b450e1ad117c25fd4e41a/Michelf/Markdown.php#L2277
(function _doAnchors_reference_callback, line: 2277)

[4]:https://github.com/michelf/php-markdown/blob/efcf20752db06533b04b450e1ad117c25fd4e41a/Michelf/Markdown.php#L2313
(function _doAnchors_inline_callback, line: 2313)

[phileCustomizeLinks]:%github%/phileCustomizeLinks/blob/master/plugin.php

[pico-git]:https://github.com/gilbitron/Pico

[phile-git]:https://github.com/PhileCMS/Phile

[my-github-commit]:%github%/php-markdown/commit/66d3da7c2c95aab1ff97b6d114d36eceaf9ff44f
