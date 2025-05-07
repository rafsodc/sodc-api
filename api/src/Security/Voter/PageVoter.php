<?php

namespace App\Security\Voter;

use App\Entity\Page;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use Psr\Log\LoggerInterface;

class PageVoter extends Voter
{
    public const VIEW = 'PAGE_VIEW';
    public const EDIT = 'PAGE_EDIT';

    private $security;
    private $logger;

    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $this->logger->debug('PageVoter::supports', [
            'attribute' => $attribute,
            'subject' => $subject,
            'isPage' => $subject instanceof Page
        ]);

        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Page;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $this->logger->debug('PageVoter::voteOnAttribute', [
            'attribute' => $attribute,
            'subject' => $subject,
            'user' => $token->getUser(),
            'isAuthenticated' => $token instanceof \Symfony\Component\Security\Core\Authentication\Token\NullToken ? false : $token->isAuthenticated(),
            'roles' => $token->getRoleNames()
        ]);

        // Always allow anonymous access for viewing
        if ($attribute === self::VIEW) {
            $this->logger->debug('PageVoter: Allowing anonymous view access');
            return true;
        }

        // Only admins can edit
        if ($attribute === self::EDIT) {
            $this->logger->debug('PageVoter: Checking admin access');
            return $this->security->isGranted('ROLE_ADMIN');
        }

        $this->logger->debug('PageVoter: Access denied');
        return false;
    }
} 