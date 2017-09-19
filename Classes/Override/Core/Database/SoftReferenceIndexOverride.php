<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Override\Core\Database;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\SoftReferenceIndex;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SoftReferenceIndexOverride
 *
 * To handle "external link".
 */
class SoftReferenceIndexOverride extends SoftReferenceIndex
{
    /**
     * TypoLink tag processing.
     * Will search for <link ...> and <a> tags in the content string and process any found.
     *
     * @param string $content The input content to analyse
     * @param array $spParams Parameters set for the softref parser key in TCA/columns
     * @return array Result array on positive matches, see description above. Otherwise FALSE
     * @see \TYPO3\CMS\Frontend\ContentObject::typolink(), getTypoLinkParts()
     */
    public function findRef_typolink_tag($content, $spParams)
    {
        $resultArray = $firstResultArray = parent::findRef_typolink_tag($content, $spParams);

        // We want to ensure $resultArray is an array
        if (!is_array($resultArray)) {
            $resultArray = [
                'content' => $content,
                'elements' => []
            ];
        }

        // Parse string for special TYPO3 <link> tag:
        $htmlParser = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Html\HtmlParser::class);
        $linkService = GeneralUtility::makeInstance(LinkService::class);
        $linkTags = $htmlParser->splitTags('a', $resultArray['content']);

        // Traverse result:
        $elements = [];

        foreach ($linkTags as $key => $foundValue) {
            if ($key % 2) {
                if (preg_match('/href="([^"]+)"/', $foundValue, $matches)) {
                    try {
                        $linkDetails = $linkService->resolve($matches[1]);
                        if ($linkDetails['type'] === 'externalLink') {
                            $token = $this->makeTokenID($key);
                            $linkTags[$key] = str_replace($matches[1], '{softref:' . $token . '}', $linkTags[$key]);
                            $elements[$key] = $linkDetails;
                            $elements[$key]['subst'] = [
                                'type' => 'string',
                                'tokenID' => $token,
                                'tokenValue' => $matches[1]
                            ];

                        }
                    } catch (\Exception $e) {
                        // skip invalid links
                    }
                }
            }
        }

        // Combine a new array if we have found some new "externalLink" elements along the way.
        if (!empty($elements)) {
            $resultArray = [
                'content' => implode('', $linkTags),
                'elements' => array_merge($resultArray['elements'], $elements),
            ];
            return $resultArray;
        }
        return $firstResultArray; // return the original array
    }

}
