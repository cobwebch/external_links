<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\LinkHandler;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Domain\Repository\ExternalLinkRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Form\Element\InputLinkElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\Controller\AbstractLinkBrowserController;
use TYPO3\CMS\Recordlist\LinkHandler\AbstractLinkHandler;
use TYPO3\CMS\Recordlist\LinkHandler\LinkHandlerInterface;

/**
 * Link handler for email links
 */
class ExternalLinkHandler extends AbstractLinkHandler implements LinkHandlerInterface
{
    /**
     * Parts of the current link
     *
     * @var array
     */
    protected $linkParts = [];

    /**
     * Initialize the handler
     *
     * @param AbstractLinkBrowserController $linkBrowser
     * @param string $identifier
     * @param array $configuration Page TSconfig
     */
    public function initialize(AbstractLinkBrowserController $linkBrowser, $identifier, array $configuration)
    {
        parent::initialize($linkBrowser, $identifier, $configuration);
        $this->view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:external_links/Resources/Private/Templates/LinkBrowser')]);
        $this->view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:external_links/Resources/Private/Partials/LinkBrowser')]);
        $this->view->setLayoutRootPaths([GeneralUtility::getFileAbsFileName('EXT:external_links/Resources/Private/Layouts/LinkBrowser')]);
    }

    /**
     * Checks if this is the handler for the given link
     *
     * The handler may store this information locally for later usage.
     *
     * @param array $linkParts Link parts as returned from TypoLinkCodecService
     *
     * @return bool
     */
    public function canHandleLink(array $linkParts): bool
    {
        if ($linkParts['type'] === 'externalLink'
            && isset($linkParts['url']['uid'])
            && (int)$linkParts['url']['uid'] > 0) {
            $this->linkParts = $linkParts;
            return true;
        }
        return false;
    }

    /**
     * Format the current link for HTML output
     *
     * @return string
     */
    public function formatCurrentUrl(): string
    {
        $identifier = (int)$this->linkParts['url']['uid'];
        $record = $this->getExternalLinkRepository()->findByIdentifier($identifier);

        return empty($record)
            ? $this->linkParts['url']['url']
            : sprintf(
                '%s (%s)',
                $record['url'],
                $this->linkParts['url']['uid']
            );
    }

    /**
     * Render the link handler
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function render(ServerRequestInterface $request): string
    {

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $configuration['paths']['Cobweb/ExternalLinks'] = '../typo3conf/ext/external_links/Resources/Public/JavaScript';
        $pageRenderer->addRequireJsConfiguration($configuration);
        $pageRenderer->loadRequireJsModule('Cobweb/ExternalLinks/ExternalLinkHandler');

        $records = $this->getExternalLinkRepository()->findAll();
        $this->view->assign('records', $records);

        // fetch all links
        $this->view->assign('externalLink', !empty($this->linkParts) ? $this->linkParts['url']['uid'] : '');

        return $this->view->render('ExternalLink');
    }

    /**
     * @return string[] Array of body-tag attributes
     */
    public function getBodyTagAttributes(): array
    {
        return [];
    }

    /**
     * @return object|ExternalLinkRepository
     */
    protected function getExternalLinkRepository(): ExternalLinkRepository
    {
        return GeneralUtility::makeInstance(ExternalLinkRepository::class);
    }

}
