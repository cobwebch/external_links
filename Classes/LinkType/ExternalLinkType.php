<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\LinkType;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Domain\Repository\ExternalLinkRepository;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\TooManyRedirectsException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Linkvalidator\Linktype\AbstractLinktype;

/**
 * This class provides Check Link Handler plugin implementation
 */
class ExternalLinkType extends AbstractLinktype
{
    /**
     * Cached list of the URLs, which were already checked for the current processing
     *
     * @var array $urlReports
     */
    protected $urlReports = [];

    /**
     * Cached list of all error parameters of the URLs, which were already checked for the current processing
     *
     * @var array $urlErrorParams
     */
    protected $urlErrorParams = [];

    /**
     * List of headers to be used for matching an URL for the current processing
     *
     * @var array $additionalHeaders
     */
    protected $additionalHeaders = [];

    public function __construct()
    {
        // Just add some more labels into the global scope since labels are pretty much hardcoded in
        // EXT:linkvalidator/Classes/Report/LinkValidatorReport.php
        // @see $this->getLanguageService()->getLL('hooks.' . $type) ?: $type;
        $GLOBALS['LOCAL_LANG']['default']['hooks.externalLink'][] = [
            'source' => $this->getLanguageService()->sL('LLL:EXT:external_links/Resources/Private/Language/locallang.xlf:hooks.externalLink'),
            'target' => $this->getLanguageService()->sL('LLL:EXT:external_links/Resources/Private/Language/locallang.xlf:hooks.externalLink'),
        ];
    }

    /**
     * @param string $uri
     * @return string
     */
    public function resolveUrl($uri)
    {
        $url = '';
        preg_match('/uid=([0-9]*)/is', $uri, $matches);
        if (isset($matches[1])) {

            $linkIdentifier = (int)$matches[1];

            $externalLink = $this->getExternalLinkRepository()->findByIdentifier($linkIdentifier);
            if ($externalLink) {
                $url = $externalLink['url'];
            }
        }
        return $url;
    }

    /**
     * Checks a given URL for validity
     *
     * @param string $uri The URL to check e.g. t3://externalLink?uid=123
     * @param array $softRefEntry The soft reference entry which builds the context of that URL
     * @param \TYPO3\CMS\Linkvalidator\LinkAnalyzer $reference Parent instance
     * @return bool TRUE on success or FALSE on error
     */
    public function checkLink($uri, $softRefEntry, $reference)
    {
        $errorParams = [];
        $url = $this->resolveUrl($uri);

        if (!$url) {
            $errorParams['errorType'] = 'missing_record';
            $errorParams['message'] = 'External Link could not be found';
            $errorParams['location'] = $uri;
            $this->setErrorParams($errorParams);
            return false; // early return
        }


        $isValidUrl = true;
        if (isset($this->urlReports[$url])) {
            if (!$this->urlReports[$url]) {
                if (is_array($this->urlErrorParams[$url])) {
                    $this->setErrorParams($this->urlErrorParams[$url]);
                }
            }
            return $this->urlReports[$url];
        }

        $options = [
            'cookies' => GeneralUtility::makeInstance(CookieJar::class),
            'allow_redirects' => ['strict' => true]
        ];

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        try {
            $response = $requestFactory->request($url, 'HEAD', $options);
            // HEAD was not allowed or threw an error, now trying GET
            if ($response->getStatusCode() >= 400) {
                $options['headers']['Range'] = 'bytes = 0 - 4048';
                $response = $requestFactory->request($url, 'GET', $options);
            }
            if ($response->getStatusCode() >= 300) {
                $isValidUrl = false;
                $errorParams['errorType'] = $response->getStatusCode();
                $errorParams['message'] = $response->getReasonPhrase();
            }
        } catch (TooManyRedirectsException $e) {
            $lastRequest = $e->getRequest();
            $response = $e->getResponse();
            $errorParams['errorType'] = 'loop';
            $errorParams['location'] = (string)$lastRequest->getUri();
            $errorParams['errorCode'] = $response->getStatusCode();
        } catch (\Exception $e) {
            $isValidUrl = false;
            $errorParams['errorType'] = 'exception';
            $errorParams['message'] = $e->getMessage();
        }
        if (!$isValidUrl) {
            $this->setErrorParams($errorParams);
        }
        $this->urlReports[$url] = $isValidUrl;
        $this->urlErrorParams[$url] = $errorParams;
        return $isValidUrl;
    }

    /**
     * Generate the localized error message from the error params saved from the parsing
     *
     * @param array $errorParams All parameters needed for the rendering of the error message
     * @return string Validation error message
     */
    public function getErrorMessage($errorParams)
    {
        $lang = $this->getLanguageService();
        $errorType = $errorParams['errorType'];
        switch ($errorType) {
            case 300:
                $response = sprintf($lang->getLL('list.report.externalerror'), $errorType);
                break;
            case 403:
                $response = $lang->getLL('list.report.pageforbidden403');
                break;
            case 404:
                $response = $lang->getLL('list.report.pagenotfound404');
                break;
            case 500:
                $response = $lang->getLL('list.report.internalerror500');
                break;
            case 'loop':
                $response = sprintf($lang->getLL('list.report.redirectloop'), $errorParams['errorCode'], $errorParams['location']);
                break;
            case 'exception':
                $response = sprintf($lang->getLL('list.report.httpexception'), $errorParams['message']);
                break;
            default:
                $response = sprintf($lang->getLL('list.report.otherhttpcode'), $errorType, $errorParams['message']);
        }
        return $response;
    }

    /**
     * Type fetching method, based on the type that softRefParserObj returns
     *
     * @param array $value Reference properties
     * @param string $type Current type
     * @param string $key Validator hook name
     * @return string fetched type
     */
    public function fetchType($value, $type, $key)
    {
        if ($value['type'] === 'string' && strpos($value['tokenValue'], 't3://externalLink') === 0) {
            $type = 'externalLink';
        }
        return $type;
    }

    /**
     * @return object|ExternalLinkRepository
     */
    protected function getExternalLinkRepository(): ExternalLinkRepository
    {
        return GeneralUtility::makeInstance(ExternalLinkRepository::class);
    }

}
