<!--
Title: How to customize markdown link syntax?
Description: Short article about 2 ways of modifying markdown syntax (by php-plugin and by editing php-parser) for adding extra syntaz in html a-tags.
Tags: php, markdown, hacks
Date: 2013/11/01
-->

On my blog I have a lot of links on other sites. But it's good to use `rel="nofollow"`
if you add a link on untrusted site. Since I use MarkDown, I find a way to customize
MarkDown's link syntax to allow this extra attributes for html `a` tags<!--cut-here-->.



## Method #1 (php only, prefered)

If you use some flatfile CMS, like [Pico][pico-git] or [Phile][phile-git] or anything else,
it's a good practice to write a plugin. Since it's easy to realize with clean php, let's do it.



### Idea

The idea is deadly simple - just loop through all links, and if it is external one,
add `rel="nofollow" target="_blank"`. Which one is external? You can decide by yourself.
My variant is below.

Link **is not** external, if `href`:

* starts with `/` (for relative links)
* starts with `#` (for anchors links)
* contain base site url (like `site.com`)


### Code it

So, my plugin for Phile is on [github][phileCustomizeLinks], and here is some demo-code:

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
 * * contain 'domain' name
 **/
// ...
$content  = "get here your page's html-content";
$domain   = "site.com";  // your domain
$encoding = "UTF-8";     // encoding of your files and texts

$dom = new DOMDocument();
// oh.. bugging DomDocument
$rightEncodingHtml = mb_convert_encoding($content,
			'HTML-ENTITIES',
			encoding);
$dom->loadHTML( $rightEncodingHtml );

foreach ( $dom->getElementsByTagName("a") as $a_tag )
{
	if ( !$a_tag->hasAttribute("href"))
		continue;

	$href_url = $a_tag->getAttribute("href");
	$start_with_slash = ($href_url[0] == '/') ? true : false;
	$start_with_hash  = ($href_url[0] == '#') ? true : false;
	// case sensetive...
	$contain_domain = (substr_count($href_url, $domain) >= 1) ? true : false;

	if ($start_with_slash || $contain_domain || $start_with_hash)
		continue;

	// echo "external url: " . $href_url . "\n<br>";
	$a_tag->setAttribute("target", "_blank");
	$a_tag->setAttribute("rel",    "nofollow");
}

// profit:
$content = $dom->saveHTML();
// ...
```

* * *



## Method #2 (edit php-markdown parser)

If you cannot write a plugin, but can/want to modify a php-markdown parser... let's do it :)



### Result

The result first. If you write a link like

    [some-site](http://some-site.com)
    //or
    [some-site][1]
    [1]:http://some-site.com


the resulting html would be:

	<a rel="nofollow" target="_blank" href="some-site.com">some-site</a>

**But**, if you type:

```markdown
[article1][1]
[1]:!http://my-site.com/article1
# Important: '!' -- means URL is trusted
# You could swap behaviour if you want
```

the result would be just:

    <a href="http://my-site.com/article1">article1</a>



### Code it

Well, let's look at [php-markdown][2] library
(if you use another parser for MarkDown - idea is the same):

* we need to have one extra check, that if URL starts with `!`, then do nothing
* otherwise - add `rel="nofollow"` and `target="_blank"` attributes to `<a>` tag

There're two functions:

* `_doAnchors_reference_callback($matches)`
* `_doAnchors_inline_callback($matches)`

for links-reference and inline-links respectively.

Since I use php-MarkDownExtra, I edited [this][3] and [this][4] one function.

Resulting commits look like:

<pre><code class="diff">
<a target="_blank" rel="nofollow" title="commit: Modify MarkDown URL syntax: add rel=nofolow and target=_blank for..." href="https://github.com/Jecomire/php-markdown/commit/6d68c963cf2e76b54becbcd2004c20e76a254009">see this on github</a>
@@ -2278,8 +2278,20 @@ protected function _doAnchors_reference_callback($matches) {
      if (isset($this-&gt;urls[$link_id])) {
            $url = $this-&gt;urls[$link_id];
            $url = $this-&gt;encodeAttribute($url);
-                                   
-      $result = &quot;&lt;a href=\&quot;$url\&quot;&quot;;
+
+      # if it trusted URL (starts with '!'): just crop the '!'
+      # otherwise it's outside link: add rel=&quot;nofollow&quot; and target=&quot;_blank&quot;
+      $result = &quot;&lt;a &quot;;
+      if ('!' == $url[0])
+      {
+          # trusted URL
+          $url = substr($url, 1);
+      }
+      else {
+          $result .= &quot;rel=\&quot;nofollow\&quot; target=\&quot;_blank\&quot;&quot;;
+      }
+
+      $result .= &quot; href=\&quot;$url\&quot;&quot;;
       if ( isset( $this-&gt;titles[$link_id] ) ) {
           $title = $this-&gt;titles[$link_id];
           $title = $this-&gt;encodeAttribute($title);
</code></pre>

<pre><code class="diff"><a target="_blank" rel="nofollow" title="commit: Add same feature for inline links..." href="https://github.com/Jecomire/php-markdown/commit/acdb7dcc9e0e628caf15a1a9e66e7cb4d43688ab">see this on github</a>
@@ -2319,7 +2319,19 @@ protected function _doAnchors_inline_callback($matches) {

      $url = $this-&gt;encodeAttribute($url);

-    $result = &quot;&lt;a href=\&quot;$url\&quot;&quot;;
+    # if it trusted URL (starts with '!'): just cut the '!'
+    # otherwise it's outside link: add rel=&quot;nofollow&quot; and target=&quot;_blank&quot;
+    $result = &quot;&lt;a &quot;;
+    if ('!' == $url[0])
+    {
+        # trusted URL
+        $url = substr($url, 1);
+    }
+    else {
+        $result .= &quot;rel=\&quot;nofollow\&quot; target=\&quot;_blank\&quot;&quot;;
+    }
+
+    $result .= &quot; href=\&quot;$url\&quot;&quot;;
     if (isset($title)) {
        $title = $this-&gt;encodeAttribute($title);
        $result .=  &quot; title=\&quot;$title\&quot;&quot;;
</code></pre>


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

[phileCustomizeLinks]:https://github.com/Jecomire/phileCustomizeLinks/blob/master/plugin.php

[pico-git]:https://github.com/gilbitron/Pico

[phile-git]:https://github.com/PhileCMS/Phile
