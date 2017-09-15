<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Traits;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Trait LocalizationTrait
 */
trait LocalizationTrait
{

    /**
     * Returns an instance of the current Backend User.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return object|LanguageService
     * @throws \InvalidArgumentException
     */
    protected function getLanguageService(): LanguageService
    {
        return GeneralUtility::makeInstance(LanguageService::class, $this->getBackendUser()->uc['lang']);
    }

}
