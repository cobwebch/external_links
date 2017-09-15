<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// LinkHandler: register the keyword for the "LinkHandler" popup i.e. t3://externalLink?uid=17
$GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler']['externalLink'] = Cobweb\ExternalLinks\LinkHandling\ExternalLinkHandler::class;

// FormEngine: allows to resolve the link for field having the wizard "link".
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['linkHandler']['externalLink'] = Cobweb\ExternalLinks\FormEngine\ExternalLinkElement::class;

// Frontend: register the link resolver for the BE.
$GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder']['externalLink'] = Cobweb\ExternalLinks\TypoLink\ExternalLinkBuilder::class;

// LinkHandler: load some Page TSConfig to register a new tab within the LinkHandler
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:external_links/Configuration/TSConfig/page.tsconfig">'
);