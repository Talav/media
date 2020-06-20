<?php

declare(strict_types=1);

namespace Talav\Component\Media\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\MediaProviderInterface;
use Talav\Component\Media\Provider\ProviderPool;

class MediaEventSubscriber implements EventSubscriber
{
    /** @var ProviderPool */
    protected $providerPool;

    public function __construct(ProviderPool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postUpdate,
            Events::postRemove,
            Events::postPersist,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->postUpdate($args->getObject());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->postRemove($args->getObject());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->postPersist($args->getObject());
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->preUpdate($args->getObject());
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->preRemove($args->getObject());
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if (!($args->getObject() instanceof MediaInterface)) {
            return;
        }
        $this->getProvider($args)->prePersist($args->getObject());
    }

    /**
     * @return MediaProviderInterface
     */
    protected function getProvider(LifecycleEventArgs $args)
    {
        return $this->providerPool->getProvider($args->getObject()->getProviderName());
    }
}
