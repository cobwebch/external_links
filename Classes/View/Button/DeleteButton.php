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
 * View which renders a "delete" button to be placed in the grid.
 */
class DeleteButton extends AbstractComponentView
{

	/**
	 * Renders a "delete" button to be placed in the grid.
	 *
	 * @param array $externalLink
	 * @return string
	 */
	public function render(array $externalLink) : string
	{
		$title = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('LLL:EXT:lang/Resources/Private/Language/locallang_mod_web_list.xlf:delete','lang');
		return $this->makeLinkButton()
			->setHref('#')
			->setDataAttributes([
				'uid' => $externalLink['uid'],
				'toggle' => 'tooltip',
				'url' => $externalLink['url'],
			])
			->setClasses('btn-delete')
			->setTitle($title)
			->setIcon($this->getIconFactory()->getIcon('actions-edit-delete', Icon::SIZE_SMALL))
			->render();
	}
}