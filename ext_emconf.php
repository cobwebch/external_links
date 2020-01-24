<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'External Links',
	'description' => 'Manage external links',
	'category' => 'plugin',
	'author' => 'Fabien Udriot',
	'author_email' => 'fabien.udriot@cobweb.ch',
	'state' => 'stable',
	'clearCacheOnLoad' => 0,
	'version' => '1.0.2',
    'autoload' => [
        'psr-4' => ['Cobweb\\ExternalLinks\\' => 'Classes']
    ],
	'constraints' => [
		'depends' => [
			'typo3' => '8.7.0-8.7.99',
        ],
		'conflicts' => [
        ],
		'suggests' => [
        ],
    ],
];
