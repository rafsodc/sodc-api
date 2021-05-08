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

class TransactionOutputDataTransformer implements DataTransformerInterface
{

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @param Transaction $transaction
     */
    public function transform($transaction, string $to, array $context = [])
    {
        $output = new TransactionOutput();
        $output->id = $transaction->getId();
        $output->owner = $transaction->getOwner();
        $output->event = $transaction->getEvent();
        $output->tickets = $transaction->getTickets();
        $output->amount = $transaction->getAmount();
        $output->paid = $transaction->getPaid();
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
        $currency = 826;
        return [
            'action' => 'https://test.ipg-online.com/connect/gateway/processing',
            'txntype' => 'sale',
            'timezone' => 'txndatetime',
            'hash_alogorithm' => 'SHA256',
            'hash' => $this->createHash($transaction, $currency),
            'storename' => $this->params->get('ipg_store_id'),
            'mode' => '',
            'oid' => $transaction->getId(),
            'email' => $transaction->getOwner()->getEmail(),
            'chargetotal' => $transaction->getAmount(),
            'currency' => $currency
            //More fields - email, basketitems(?), chargetotal, currrency
        ];
    }

    private function createHash($transaction, $currency) {
        $dateTime = new DateTime();

        $stringToHash = $this->params->get('ipg_store_id') . $dateTime->format("Y:m:d-H:i:s") . $transaction->getAmount() . $currency . $this->params->get('ipg_secret_key');
        $ascii = bin2hex($stringToHash);

        return hash('sha256',$ascii);
    }
}
