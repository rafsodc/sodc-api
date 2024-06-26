<?php

namespace App\EventSubscriber;

use App\Entity\BulkNotification;
use App\Entity\UserNotification;
use App\Entity\User;
use App\Service\PlaceholderReplacer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\UserNotificationEvent;

final class BulkNotificationSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $placeholderReplacer;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, PlaceholderReplacer $placeholderReplacer, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->placeholderReplacer = $placeholderReplacer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onBulkNotificationPost', EventPriorities::POST_WRITE],
        ];
    }

    public function onBulkNotificationPost(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();

        if (!$entity instanceof BulkNotification) {
            return;
        }

        $bulkNotification = $entity;
        $roles = $bulkNotification->getRoles();
        $dataTemplate = $bulkNotification->getData();
        $templateId = $bulkNotification->getTemplateId();
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $userRoles = $user->getRoles();
            if ($user->hasAnyRoleWithExclusions($roles)) {
                $userData = $this->replacePlaceholdersInData($dataTemplate, $user);
                $userNotification = new UserNotification();
                $userNotification->setOwner($user);
                $userNotification->setBulkNotification($bulkNotification);
                $userNotification->setData($userData);
                $userNotification->setTemplateId($templateId);
                $userNotification->setSent(false);
                $this->entityManager->persist($userNotification);
                $this->eventDispatcher->dispatch(new UserNotificationEvent($userNotification));
            }
        }

        $this->entityManager->flush();
    }

    private function replacePlaceholdersInData(array $data, User $user): array
    {
        $replacedData = [];
        foreach ($data as $key => $value) {
            $replacedData[$key] = $this->placeholderReplacer->replacePlaceholders(['user' => $user], $value);
        }
        return $replacedData;
    }
}
