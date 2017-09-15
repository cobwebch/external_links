<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\TypoLink;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Domain\Repository\ExternalLinkRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

/**
 * Builds a TypoLink to an external URL
 */
class ExternalLinkBuilder extends AbstractTypolinkBuilder
{
    /**
     * @inheritdoc
     */
    public function build(array &$linkDetails, string $linkText, string $target, array $conf): array
    {
        $linkIdentifier = isset($linkDetails['uid'])
            ? (int)$linkDetails['uid']
            : 0;

        $externalLink = $this->getExternalLinkRepository()->findByIdentifier($linkIdentifier);
        $url = empty($externalLink)
            ? ''
            : $externalLink['url'];

        return [
            $url,
            $this->parseFallbackLinkTextIfLinkTextIsEmpty($linkText, $url),
            $target ?: $this->resolveTargetAttribute($conf, 'extTarget', true, $this->getTypoScriptFrontendController()->extTarget)
        ];
    }

    /**
     * @return object|ExternalLinkRepository
     */
    protected function getExternalLinkRepository(): ExternalLinkRepository
    {
        return GeneralUtility::makeInstance(ExternalLinkRepository::class);
    }

}
