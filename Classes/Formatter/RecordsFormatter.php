<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Formatter;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\View\Button\DeleteButton;
use Cobweb\ExternalLinks\View\Button\EditButton;
use Cobweb\ExternalLinks\View\Record\RecordIcon;
use Cobweb\ExternalLinks\View\Record\RecordLabel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Format a record to be displayed in the Grid in the LinkHandler popup.
 *
 * Class RecordsFormatter
 */
class RecordsFormatter
{
    /**
     * @param array $externalLinks
     * @return array
     */
    public function format(array $externalLinks): array
    {
        $formattedRecords = [];
        foreach ($externalLinks as $externalLink) {
            $formattedRecords[] = $this->formatOne($externalLink);
        }

        return $formattedRecords;
    }

    /**
     * @param array $externalLink
     * @return array
     */
    public function formatOne(array $externalLink): array
    {
        $externalLink['icon'] = $this->getRecordIcon()->render($externalLink);
        $externalLink['commands'] = sprintf(
            '<div class="btn-toolbar pull-right" role="toolbar" aria-label=""><div class="btn-group" role="group" aria-label="">%s %s</div></div>',
            $this->getDeleteButton()->render($externalLink),
            $this->getEditButton()->render($externalLink)
        );
        $externalLink['label'] = $this->getRecordLabel()->render($externalLink);
        return $externalLink;
    }

    /**
     * @return object|EditButton
     */
    protected function getEditButton()
    {
        return GeneralUtility::makeInstance(EditButton::class);
    }

    /**
     * @return object|DeleteButton
     */
    protected function getDeleteButton()
    {
        return GeneralUtility::makeInstance(DeleteButton::class);
    }

    /**
     * @return object|RecordLabel
     */
    protected function getRecordLabel()
    {
        return GeneralUtility::makeInstance(RecordLabel::class);
    }

    /**
     * @return object|RecordIcon
     */
    protected function getRecordIcon()
    {
        return GeneralUtility::makeInstance(RecordIcon::class);
    }

}
