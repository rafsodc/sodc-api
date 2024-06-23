<?php

namespace App\EventSubscriber;

use App\Entity\NotifyMessage;
use App\Entity\NotifyMessageUser;
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

final class NotifyMessageSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $placeholderReplacer;

    public function __construct(EntityManagerInterface $entityManager, PlaceholderReplacer $placeholderReplacer)
    {
        $this->entityManager = $entityManager;
        $this->placeholderReplacer = $placeholderReplacer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onNotifyMessagePost', EventPriorities::POST_WRITE],
        ];
    }

    public function onNotifyMessagePost(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();

        if (!$entity instanceof NotifyMessage) {
            return;
        }

        $notifyMessage = $entity;
        $roles = $notifyMessage->getRoles();
        $dataTemplate = $notifyMessage->getData();
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $userRoles = $user->getRoles();
            if ($user->hasAnyRoleWithExclusions($roles)) {
                $userData = $this->replacePlaceholdersInData($dataTemplate, $user);
                $notifyMessageUser = new NotifyMessageUser();
                $notifyMessageUser->setOwner($user);
                $notifyMessageUser->setNotifyMessage($notifyMessage);
                $notifyMessageUser->setData($userData);
                $notifyMessageUser->setSent(false);

                $this->entityManager->persist($notifyMessageUser);
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
