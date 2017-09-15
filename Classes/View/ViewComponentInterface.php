<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\View;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Abstract Component View.
 */
interface ViewComponentInterface
{

    /**
     * Renders something to be printed to the browser.
     *
     * @param array $externalLink
     * @return string
     */
    public function render(array $externalLink): string;

}
