<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\LinkHandling;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\LinkHandling\LinkHandlingInterface;

/**
 * Class ExternalLinkHandler
 */
class ExternalLinkHandler implements LinkHandlingInterface
{
    /**
     * The Base URN for this link handling to act on
     *
     * @var string
     */
    protected $baseUrn = 't3://externalLink';

    /**
     * Returns all valid parameters for linking to a TYPO3 page as a string
     *
     * @param array $parameters
     * @return string
     * @throws \InvalidArgumentException
     */
    public function asString(array $parameters): string
    {
        if (empty($parameters['uid'])) {
            throw new \InvalidArgumentException('The ExternalLinkHandler expects uid $parameter configuration.', 1505407020);
        }

        return sprintf('%s?uid=%s',
            $this->baseUrn,
            $parameters['uid']
        );
    }

    /**
     * Returns all relevant information built in the link to a page (see asString())
     *
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function resolveHandlerData(array $data): array
    {
        if (empty($data['uid'])) {
            throw new \InvalidArgumentException('The ExternalLinkHandler expects uid as $data configuration', 1505407021);
        }

        return $data;
    }
}
