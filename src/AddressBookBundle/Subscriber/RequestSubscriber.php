<?php declare(strict_types=1);
/**
 * This file is part of the blogolite Blog System.
 * Author: Thomas Georg Wunner <thomas@wunner-software.de>
 * Creation Date: 18.04.20 17:08
 * License: MIT
 */
namespace AddressBookBundle\Subscriber;

use AddressBookBundle\Entity\Addresses;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(FilterControllerEvent $eventArgs)
    {
        $this->lazyInstallSchema();
    }

    /**
     * Install or update profile table
     */
    private function lazyInstallSchema()
    {
        // TODO: check if table exists:
        $tool = new SchemaTool($this->em);
        $tool->updateSchema([$this->em->getClassMetadata(Addresses::class)], true);
    }
}
