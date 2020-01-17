<?php
declare(strict_types=1);

namespace Cobweb\ExternalLinks\Domain\Repository;

/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * The repository for Links.
 */
class ExternalLinkRepository implements SingletonInterface
{

	/**
	 * @var string
	 */
	protected $tableName = 'tx_externallinks_domain_model_externallink';

	/**
	 * @var DataHandler
	 */
	protected $dataHandler;

	/**
	 * @param int $identifier
	 * @return array
	 */
	public function findByIdentifier(int $identifier): array
	{
		$record = [];
		if ($this->isAllowed()) {

			$queryBuilder = $this->getQueryBuilderForTable();

			// do not use enabled fields here
			$queryBuilder->getRestrictions()->removeAll();

			// Add enabled fields restriction
			/** @var DeletedRestriction $deleteRestriction */
			$deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
			$queryBuilder->getRestrictions()->add($deleteRestriction);

			// set table and where clause
			$queryBuilder
				->select(...GeneralUtility::trimExplode(',', '*', true))
				->from($this->tableName)
				->where($queryBuilder->expr()->eq(
					'uid',
					$queryBuilder->createNamedParameter($identifier, \PDO::PARAM_INT))
				);

			$record = $queryBuilder->execute()->fetch();
		}
		return $record ?: [];
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function findByUrl($url): array
	{
		$queryBuilder = $this->getQueryBuilderForTable();

		// do not use enabled fields here
		$queryBuilder->getRestrictions()->removeAll();

		// Add enabled fields restriction
		/** @var DeletedRestriction $deleteRestriction */
		$deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
		$queryBuilder->getRestrictions()->add($deleteRestriction);

		// set table and where clause
		$queryBuilder
			->select('*')
			->from($this->tableName)
			->where($queryBuilder->expr()->eq(
				'url',
				$queryBuilder->createNamedParameter($url))
			);

		$record = $queryBuilder->execute()->fetch();
		return $record ?: [];
	}

	/**
	 * Returns a single link by URL
	 *
	 * @param string $url
	 * @return array|false
	 */
	public function findOneByUrl(string $url)
	{
		$queryBuilder = $this->getQueryBuilderForTable();

		$result = $queryBuilder->select('*')
			->from($this->tableName)
			->where(
				$queryBuilder->expr()->eq('url', '"' . $url . '"')
			)
			->setMaxResults(1)
			->execute()
			->fetchAll();
		return $result[0] ?? false;
	}

	/**
	 * Only used in the BE context
	 *
	 * @return array
	 */
	public function findAll(): array
	{
		$records = [];
		if ($this->isAllowed()) {

			$queryBuilder = $this->getQueryBuilderForTable();

			// do not use enabled fields here
			$queryBuilder->getRestrictions()->removeAll();

			// Add enabled fields restriction
			/** @var DeletedRestriction $deleteRestriction */
			$deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
			$queryBuilder->getRestrictions()->add($deleteRestriction);

			// set table and where clause
			$queryBuilder
				->select(...GeneralUtility::trimExplode(',', '*', true))
				->from($this->tableName);

			$records = $queryBuilder->execute()->fetchAll();
		}
		return $records ?: [];
	}

	/**
	 * Only used in the BE context
	 *
	 * @param string $term
	 * @return array
	 */
	public function search($term): array
	{
		$records = [];
		if ($this->isAllowed() && !empty($term) && strlen($term) > 2) {

			$queryBuilder = $this->getQueryBuilderForTable();

			// do not use enabled fields here
			$queryBuilder->getRestrictions()->removeAll();

			// Add enabled fields restriction
			/** @var DeletedRestriction $deleteRestriction */
			$deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
			$queryBuilder->getRestrictions()->add($deleteRestriction);

			// set table and where clause
			$queryBuilder
				->select(...GeneralUtility::trimExplode(',', '*', true))
				->from($this->tableName)
				->where(
					$queryBuilder->expr()->like('url', $queryBuilder->quote('%' . $term . '%'))
				)
				->orWhere(
					$queryBuilder->expr()->like('note', $queryBuilder->quote('%' . $term . '%'))
				)
				->orWhere(
					$queryBuilder->expr()->like('note', $queryBuilder->quote($term . '%'))
				)
				->orWhere(
					$queryBuilder->expr()->like('note', $queryBuilder->quote('%' . $term))
				)
				->orWhere(
					$queryBuilder->expr()->like('url', $queryBuilder->quote('%' . $term))
				)
				->orWhere(
					$queryBuilder->expr()->like('url', $queryBuilder->quote($term . '%'))
				)
				->setMaxResults(200);

			$records = $queryBuilder->execute()->fetchAll();
		}
		return $records ?: [];
	}

	/**
	 * @return bool
	 */
	protected function isAllowed(): bool
	{
		$isAllowed = false;
		if ($this->isFrontendMode()) {
			$isAllowed = true;
		} elseif ($this->isBackendMode()) {
			$isAllowed = $this->getBackendUser()->check('tables_select', $this->tableName);
		}
		return $isAllowed;
	}

	/**
	 * @return QueryBuilder
	 */
	protected function getQueryBuilderForTable(): QueryBuilder
	{
		return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->tableName);
	}

	/**
	 * Update an external link by the use of the DataHandler which will respect BE permission.
	 *
	 * @param array $record
	 * @return bool
	 */
	public function update(array $record): bool
	{
		// Build command
		$data[$this->tableName][$record['uid']] = $record;

		/** @var $dataHandler DataHandler */
		$dataHandler = $this->getDataHandler();
		$dataHandler->start($data, []);
		$dataHandler->process_datamap();
		$this->errorMessages = $dataHandler->errorLog;

		// Returns true is log does not contain errors.
		return empty($dataHandler->errorLog);
	}

	/**
	 * @param array $record
	 * @return bool
	 */
	public function create(array $record): bool
	{
		// Build command
		$record['pid'] = $this->getDefaultPid();
		$data[$this->tableName][uniqid('NEW', false)] = $record;

		/** @var $dataHandler DataHandler */
		$dataHandler = $this->getDataHandler();
		$dataHandler->stripslashes_values = 0;
		$dataHandler->start($data, []);
		$dataHandler->process_datamap();
		$this->errorMessages = $dataHandler->errorLog;

		// Returns true is log does not contain errors.
		return empty($dataHandler->errorLog);
	}

	/**
	 * @param array $record
	 * @return bool
	 */
	public function delete(array $record): bool
	{
		$cmd[$this->tableName][$record['uid']]['delete'] = 1;

		/** @var $dataHandler DataHandler */
		$dataHandler = $this->getDataHandler();
		$dataHandler->start([], $cmd);
		$dataHandler->process_cmdmap();
		$this->errorMessages = $dataHandler->errorLog;

		// Returns true is log does not contain errors.
		return empty($dataHandler->errorLog);
	}

	/**
	 * @return DataHandler
	 */
	protected function getDataHandler(): DataHandler
	{
		if (!$this->dataHandler) {
			$this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
		}
		return $this->dataHandler;
	}

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
	 * @return int
	 */
	protected function getDefaultPid(): int
	{
		return (int)$this->getConfiguration()['default_pid']['value'];
	}

	/**
	 * @return array
	 */
	protected function getConfiguration(): array
	{
		/** @var ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);

		/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
		$configurationUtility = $objectManager->get(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility::class);
		return $configurationUtility->getCurrentConfiguration('external_links');
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @return bool
	 */
	protected function isFrontendMode(): bool
	{
		return TYPO3_MODE === 'FE';
	}

	/**
	 * @return object|ObjectManager
	 * @throws \InvalidArgumentException
	 */
	protected function getObjectManager()
	{
		return GeneralUtility::makeInstance(ObjectManager::class);
	}

	/**
	 * Returns whether the current mode is Backend
	 *
	 * @return bool
	 */
	protected function isBackendMode(): bool
	{
		return TYPO3_MODE === 'BE';
	}

}