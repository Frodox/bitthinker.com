<?php

// use this config file to overwrite the defaults from default_config.php
// or to make local config changes.
$config = array();

$config['encryptionKey'] = 'a;fskjasl;fjdasfhjsda;fsdf21f32sda1f32sd1f!@#$%^&*()';
$config['site_title'] = 'Zen Notes';		// Site title
$config['theme'] = 'zen'; 			// Set the theme
//$config['theme'] = 'blog'; 			// Set the theme
$config['date_format'] = 'jS M Y'; 		// Set the PHP date format
$config['pages_order_by'] = 'meta:date';	// Order pages by "title" (alpha) or "date"
$config['pages_order'] = 'desc'; 		// Order pages "asc" or "desc"

$config['plugins'] = array(
        'phileDemoPlugin' => array('active' => false),
        'philePhpFastCache' => array('active' => false), // the default cache engine
        'phileSimpleFileDataPersistence' => array('active' => true), // the default data storage engine
	);



















// it is important to return the $config array!
return $config;
