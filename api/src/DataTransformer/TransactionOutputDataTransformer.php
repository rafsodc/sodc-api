<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\EventOutput;
use App\Dto\TransactionOutput;
use App\Dto\UserOutput;
use App\Entity\Event;
use App\Entity\Transaction;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;
use DateTime;
use DateTimeZone;

class TransactionOutputDataTransformer implements DataTransformerInterface
{

    private $params;
    private $security;

    public function __construct(ParameterBagInterface $params, Security $security)
    {
        $this->params = $params;
        $this->security = $security;
    }

    /**
     * @param Transaction $transaction
     */
    public function transform($transaction, string $to, array $context = [])
    {
        $output = new TransactionOutput();
        $output->id = $transaction->getId();
        $output->status = $transaction->getStatus();
        $output->isExpired = $transaction->getIsExpired();
        $output->basket = $transaction->getBasket();
        $output->ipg = $this->getIpgObject($transaction);
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Transaction && $to === TransactionOutput::class;
    }

    /**
     * @param Transaction $transaction
     */
    private function getIpgObject($transaction) {
        $dateTime = $transaction->getCreatedAt();
        //$dateTime->setTimezone(new DateTimeZone('Europe/London'));
        $amount = number_format($transaction->getBasket()->getAmount(), 2);
        $currency = 826;
        $user = $this->security->getUser();
        return [
            'action' => $this->params->get('ipg_store_url'),
            'checkoutoption' => "simpleform",
            'hostURI' => 'https://www.sodc.net',
            'txntype' => 'sale',
            'timezone' => $dateTime->getTimezone(),
            'txndatetime' => $dateTime->format("Y:m:d-H:i:s"),
            'hash_algorithm' => 'SHA256',
            'hash' => $this->createHash($amount, $currency, $dateTime),
            'storename' => $this->params->get('ipg_store_id'),
            'chargetotal' => $amount,
            'currency' => $currency,
            'mode' => 'payonly',
            'oid' => $transaction->getId(),
            'email' => $user->getEmail(),
            'bname' => $user->getFirstName() . " " .$user->getLastName()
            //More fields - email, basketitems(?), chargetotal, currrency
        ];
    }

    private function createHash($amount, $currency, $dateTime) {
        $stringToHash = $this->params->get('ipg_store_id') . $dateTime->format("Y:m:d-H:i:s") . $amount. $currency . $this->params->get('ipg_secret_key');
        $ascii = bin2hex($stringToHash);
        return hash('sha256',$ascii);
    }
}
