<?php

declare(strict_types=1);

namespace Talav\Component\Media\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Talav\Component\Media\Event\MediaEvent;
use Talav\Component\Media\Event\MediaEventEnum;

final class ThumbsCreatorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            MediaEventEnum::MEDIA_ADD_NEW => 'addThumbnails',
        ];
    }

    public function onImplicitLogin(MediaEvent $event): void
    {
        $this->updateUserLastLogin($event->getUser());
    }
}
