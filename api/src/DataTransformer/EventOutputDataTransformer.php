<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\EventOutput;
use App\Dto\UserOutput;
use App\Entity\Event;
use Symfony\Component\Security\Core\Security;
use DateTime;

class EventOutputDataTransformer implements DataTransformerInterface
{

    /**
     * @param Event $event
     */
    public function transform($event, string $to, array $context = [])
    {
        $output = new EventOutput();
        $output->title = $event->getTitle();
        $output->date = $event->getDate();
        $output->bookingOpen = $event->getBookingOpen();
        $output->bookingClose = $event->getBookingClose();
        $output->venue = $event->getVenue();
        $output->description = $event->getDescription();
        $output->principalSpeaker = $event->getPrincipalSpeaker();
        $output->sponsor = $event->getSponsor();
        $output->ticketTypes = $event->getTicketTypes();
        $output->isBookingOpen = $this->isBookingOpen($event);
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Event && $to === EventOutput::class;
    }

    private function isBookingOpen(Event $event): bool
    {
        $now = new DateTime();
        return ($now >= $event->getBookingOpen()) && ($now <= $event->getBookingClose());
    }

}
