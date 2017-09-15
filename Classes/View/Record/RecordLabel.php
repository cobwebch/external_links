<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\View\Record;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\View\AbstractComponentView;
use TYPO3\CMS\Core\Imaging\Icon;

/**
 * Class RecordLabel
 */
class RecordLabel extends AbstractComponentView
{

    /**
     * @param array $externalLink
     * @return string
     */
    public function render(array $externalLink) : string
    {
        return sprintf(
            '
<span class="container-external-link-label"
    data-table="tx_externallinks_domain_model_externallink" 
    data-title="test int link" >
        <a href="#" data-close="0" title="%s" data-uid="%s">
            <span class="t3js-icon icon icon-size-small icon-state-default icon-actions-add" data-identifier="actions-add">
                <span class="icon-markup">
                    <img src="/typo3/sysext/core/Resources/Public/Icons/T3Icons/actions/actions-add.svg" width="16" height="16">
                </span>
            </span>
        </a>
        <a href="#" data-close="1" title="%s" data-uid="%s">%s</a>
</span>',
            $this->getLanguageService()->sL('LLL:EXT:external_links/Resources/Private/Language/locallang.xlf:action.new'),
            $externalLink['uid'],
            $this->getLanguageService()->sL('LLL:EXT:external_links/Resources/Private/Language/locallang.xlf:action.new'),
            $externalLink['uid'],
            $externalLink['url']
        );
    }
}
