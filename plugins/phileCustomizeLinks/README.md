# Phile Customize Links plugin

A plugin for PhileCMS to customize links (`a` tags).
Now available:

 * adding `rel="nofollow"`
 * adding `target="_blank"`

for external links (which don't contain domain name and don't start with `/` or `#`).

### Installation

* Install [Phile](https://github.com/PhileCMS/Phile)
* Clone this repo into `plugins/phileCustomizeLinks`
* add `$config['plugins']['phileCustomizeLinks'] = array('active' => true);` to your `config.php`

### Usage

Just after you install and activate plugin, all links will be updated (be sure to update cache).

### Customize
You can set some defaults in the plugin's config file (`plugins/phileCustomizeLinks/config.php`),
available keys:

 * `target` -- which text to put into `target=""` attribute (`_blank` by default)
 * `rel` -- which text to put into `rel=""` attribute (`nofollow` by default)
 * `encoding` -- encoding of your text/`md`-file (needed for correct work; `UTF-8` by default)

**Note**: Don't remove any line in plugin's config file! Just edit the values.
