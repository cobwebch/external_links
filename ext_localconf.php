<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// LinkHandler: register the keyword for the "LinkHandler" popup i.e. t3://externalLink?uid=17
$GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler']['externalLink'] = \Cobweb\ExternalLinks\LinkHandling\ExternalLinkHandler::class;

// FormEngine: allows to resolve the link for field having the wizard "link".
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['linkHandler']['externalLink'] = \Cobweb\ExternalLinks\FormEngine\ExternalLinkElement::class;

// Frontend: register the link resolver for the BE.
$GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder']['externalLink'] = \Cobweb\ExternalLinks\TypoLink\ExternalLinkBuilder::class;

// LinkHandler: load some Page TSConfig to register a new tab within the LinkHandler
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:external_links/Configuration/TSConfig/page.tsconfig">'
);

if (TYPO3_MODE === 'BE') {

    // Link Validator: register the link validator.
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['linkvalidator']['checkLinks']['externalLink'] = \Cobweb\ExternalLinks\LinkType\ExternalLinkType::class;

    // Override classes for the Object Manager.
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Database\SoftReferenceIndex::class] = [
        'className' => \Cobweb\ExternalLinks\Override\Core\Database\SoftReferenceIndexOverride::class
    ];
}

