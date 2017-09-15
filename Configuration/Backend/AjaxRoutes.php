<?php

return [
    'external_link_action_create' => [
        'path' => '/external/link/create',
        'target' => \Cobweb\ExternalLinks\Controller\ExternalLinkAjaxController::class . '::createAction'
    ],
    'external_link_action_list' => [
        'path' => '/external/link/list',
        'target' => \Cobweb\ExternalLinks\Controller\ExternalLinkAjaxController::class . '::listAction'
    ],
    'external_link_action_delete' => [
        'path' => '/external/link/delete',
        'target' => \Cobweb\ExternalLinks\Controller\ExternalLinkAjaxController::class . '::deleteAction'
    ],
    'external_link_action_update' => [
        'path' => '/external/link/update',
        'target' => \Cobweb\ExternalLinks\Controller\ExternalLinkAjaxController::class . '::updateAction'
    ],
];