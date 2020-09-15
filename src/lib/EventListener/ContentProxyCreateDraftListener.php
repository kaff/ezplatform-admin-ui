<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ContentProxyCreateDraftListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        RouterInterface $router
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentProxyCreateEvent::class => 'createDraft',
        ];
    }

    public function createDraft(ContentProxyCreateEvent $event)
    {
        $createContentTypeStuct = $this->contentService->newContentCreateStruct(
            $event->getContentType(),
            $event->getLanguageCode()
        );

        $contentDraft = $this->contentService->createContent(
            $createContentTypeStuct,
            [
                $this->locationService->newLocationCreateStruct($event->getParentLocationId())
            ],
            []
        );

        $response = new RedirectResponse(
            $this->router->generate('ezplatform.content.draft.edit', [
                'contentId' => $contentDraft->id,
                'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                'language' => $event->getLanguageCode(),
            ])
        );

        $event->setResponse($response);
    }
}
