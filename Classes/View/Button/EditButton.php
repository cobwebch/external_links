<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\View\Button;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\View\AbstractComponentView;
use TYPO3\CMS\Core\Imaging\Icon;

/**
 * View which renders a "edit" button to be placed in the grid.
 */
class EditButton extends AbstractComponentView
{

    /**
     * @param array $externalLink
     * @return string
     */
    public function render(array $externalLink) : string
    {
        return $this->makeLinkButton()
            ->setHref('#')
            ->setDataAttributes([
                'uid' => $externalLink['uid'],
                'toggle' => 'tooltip',
                'url' => $externalLink['url'],
                'note' => $externalLink['note'],
            ])
            ->setClasses('btn-edit')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/locallang_mod_web_list.xlf:edit'))
            ->setIcon($this->getIconFactory()->getIcon('actions-document-open', Icon::SIZE_SMALL))
            ->render();
    }

}
