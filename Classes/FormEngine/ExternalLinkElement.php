<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\FormEngine;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Domain\Repository\ExternalLinkRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExternalLinkElement
 */
class ExternalLinkElement
{

    /**
     * @param array $linkData
     * @return array
     */
    public function getFormData(array $linkData): array
    {
        $record = $records = $this->getExternalLinkRepository()->findByIdentifier((int)$linkData['uid']);

        $text = $record
            ? sprintf('%s (%s)', $record['url'], $record['uid'])
            : 'Error loading external link with id ' . (int)$linkData['value'];

        return [
            'text' => $text,
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
