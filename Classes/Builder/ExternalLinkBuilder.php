<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Builder;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class ExternalLinkBuilder
 */
class ExternalLinkBuilder
{

    /**
     * @param array $data
     * @return array
     */
    public function build(array $data): array
    {
        $externalLink = [];

        if (isset($data['uid']) && (int)$data['uid'] > 0) {
            $externalLink['uid'] = (int)$data['uid'];
        }

        $externalLink['url'] = isset($data['url'])
            ? (string)$data['url']
            : '';

        $externalLink['note'] = isset($data['note'])
            ? (string)$data['note']
            : '';

        return $externalLink;
    }

}
