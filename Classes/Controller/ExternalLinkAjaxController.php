<?php
declare(strict_types=1);
namespace Cobweb\ExternalLinks\Controller;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Cobweb\ExternalLinks\Builder\ExternalLinkBuilder;
use Cobweb\ExternalLinks\Domain\Repository\ExternalLinkRepository;
use Cobweb\ExternalLinks\Formatter\RecordsFormatter;
use Cobweb\ExternalLinks\Validator\ExternalLinkValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExternalLinkAjaxController
 */
class ExternalLinkAjaxController
{

    /**
     * @param ServerRequestInterface $request the current request
     * @param ResponseInterface $response the current response
     * @return ResponseInterface the finished response with the content
     */
    public function createAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = false;
        $externalLink = $this->getExternalLinkBuilder()->build($request->getParsedBody());

        if ($this->getExternalLinkValidator()->validate($externalLink)) {
            $result = $this->getExternalLinkRepository()->create($externalLink)
                ? $externalLink
                : false;
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request the current request
     * @param ResponseInterface $response the current response
     * @return ResponseInterface the finished response with the content
     */
    public function updateAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = false;
        $externalLink = $this->getExternalLinkBuilder()->build($request->getParsedBody());

        if (isset($externalLink['uid']) && $externalLink['uid'] > 0) {
            $existingExternalLink = $this->getExternalLinkRepository()->findByIdentifier($externalLink['uid']);
            if ($existingExternalLink && $this->getExternalLinkValidator()->validate($externalLink)) {
                $result = $this->getExternalLinkRepository()->update($externalLink)
                    ? $externalLink
                    : false;
            }
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request the current request
     * @param ResponseInterface $response the current response
     * @return ResponseInterface the finished response with the content
     */
    public function listAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $records = $this->getExternalLinkRepository()->findAll();

        $data = [
            'recordsTotal' => count($records),
            'data' => $this->getRecordsFormatter()->format($records)
        ];
        $response->getBody()->write(
            json_encode($data)
        );
        return $response;
    }

    /**
     * @param ServerRequestInterface $request the current request
     * @param ResponseInterface $response the current response
     * @return ResponseInterface the finished response with the content
     */
    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $result = false;
        if (isset($data['uid']) && (int)$data['uid'] > 0) {

            $externalLink = $this->getExternalLinkRepository()->findByIdentifier((int)$data['uid']);
            if ($externalLink) {
                $result = $this->getExternalLinkRepository()->delete($externalLink);
            }
        }

        $response->getBody()->write(
            json_encode($result)
        );
        return $response;
    }

    /**
     * @return object|ExternalLinkRepository
     */
    protected function getExternalLinkRepository(): ExternalLinkRepository
    {
        return GeneralUtility::makeInstance(ExternalLinkRepository::class);
    }

    /**
     * @return object|RecordsFormatter
     */
    protected function getRecordsFormatter(): RecordsFormatter
    {
        return GeneralUtility::makeInstance(RecordsFormatter::class);
    }

    /**
     * @return object|ExternalLinkValidator
     */
    protected function getExternalLinkValidator(): ExternalLinkValidator
    {
        return GeneralUtility::makeInstance(ExternalLinkValidator::class);
    }

    /**
     * @return object|ExternalLinkBuilder
     */
    protected function getExternalLinkBuilder(): ExternalLinkBuilder
    {
        return GeneralUtility::makeInstance(ExternalLinkBuilder::class);
    }

}
