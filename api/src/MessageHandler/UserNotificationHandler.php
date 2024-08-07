<?php

namespace App\MessageHandler;

use App\Entity\UserNotification;
use App\Entity\BulkNotification;
use App\Message\UserNotificationMessage as UserNotificationMessage;
use App\Repository\UserNotificationRepository;
use App\Repository\UserSubscriptionRepository;
use App\Service\NotifyClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Service\PlaceholderReplacer;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserNotificationHandler implements MessageHandlerInterface
{
    private $userNotificationRepository;
    private $notifyClient;
    private $replyTo;
    private $router;
    private $logger;
    private $placeholderReplacer;
    private $userSubscriptionRepository;

    public function __construct(UserNotificationRepository $userNotificationRepository, UserSubscriptionRepository $userSubscriptionRepository, PlaceholderReplacer $placeholderReplacer, NotifyClient $notifyClient, RouterInterface $router, LoggerInterface $logger) 
    {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
        $this->notifyClient = $notifyClient->client;
        $this->replyTo = $notifyClient->replyTos['secretary'];
        $this->router = $router;
        $this->logger = $logger;
        $this->placeholderReplacer = $placeholderReplacer;
    }

    public function __invoke(UserNotificationMessage $message)
    {
        $userNotification = $this->userNotificationRepository->find($message->getUserNotificationId());
        
        if (!$userNotification) {
            $this->logger->error('UserNotificationHandler: UserNotification not found. '.$message->getUserNotificationId());
            return;
        }

        $user = $userNotification->getUser();
        
        $unsubscribeLink = null;

        $bulkNotification = $userNotification->getBulkNotification();
        if ($bulkNotification instanceof BulkNotification) {
            $subscription = $bulkNotification->getSubscription();
            if ($subscription->isOptout()) {
                $userSubscription = $this->userSubscriptionRepository->findOneByUserAndSubscription($user, $subscription);
                if ($userSubscription) {
                    $unsubscribeLink = $this->router->generate('unsubscribe', ['subscriptionUUID' => $userSubscription->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL);
                }
            }
        }  
        
        try {
            $data = $this->replacePlaceholdersInData($userNotification->getData(), $user);
            $this->notifyClient->sendEmail(
                $user->getEmail(),
                $userNotification->getTemplateId(),
                $data,
                $userNotification->getId(),
                $this->replyTo,
                $unsubscribeLink
            );

            // Update the 'sent' status to true
            $userNotification->setSent(true);
            $this->userNotificationRepository->save($userNotification);
            $this->logger->info('UserNotificationHandler: Email sent and status updated for UserNotification ID: ' . $userNotification->getId());
        } catch (\Exception $e) {
            $this->logger->error('UserNotificationHandler: Failed to send email for UserNotification ID: ' . $userNotification->getId() . '. Error: ' . $e->getMessage());
        }
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
