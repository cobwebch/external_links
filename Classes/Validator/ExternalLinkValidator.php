<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Validator;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class ExternalLinkValidator
 */
class ExternalLinkValidator
{

    /**
     * @param array $externalLink
     * @return bool
     */
    public function validate(array $externalLink): bool
    {
        return (bool)preg_match('/^(http(s?))\:\/\//', $externalLink['url']);
    }

}