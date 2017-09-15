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
 * Class RecordIcon
 */
class RecordIcon extends AbstractComponentView
{

    /**
     * @param array $externalLink
     * @return string
     */
    public function render(array $externalLink) : string
    {
        return $this->getIconFactory()->getIconForRecord('tx_externallinks_domain_model_externallink', $externalLink, Icon::SIZE_SMALL)->render();
    }

}
