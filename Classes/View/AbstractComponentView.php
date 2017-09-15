<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\View;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Traits\LocalizationTrait;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract Component View.
 */
abstract class AbstractComponentView implements ViewComponentInterface
{
    use LocalizationTrait;

    /**
     * @return object|IconFactory
     * @throws \InvalidArgumentException
     */
    protected function getIconFactory(): IconFactory
    {
        return GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * @return object|LinkButton
     * @throws \InvalidArgumentException
     */
    protected function makeLinkButton(): LinkButton
    {
        return GeneralUtility::makeInstance(LinkButton::class);
    }

}
