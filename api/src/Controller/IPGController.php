<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\IPGReturn;
use App\Repository\TransactionRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InvalidIPGHashHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ApiPlatform\Core\Api\IriConverterInterface;

/**
 * SecurityController for /login.  This runs when a user has been correctly logged in via json_login.
 * This also gets called when the content-type is the wrong format, but is caught by checking IS_AUTHENTICATED_FULLY.
 * Returns a Location header to user IRI if the user is logged in.
 *
 * @package App\Controller
 */
class IPGController extends AbstractController
{

    private $transactionRepository;
    private $validator;
    private $entityManager;
    private $iriConverter;

    public function __construct(TransactionRepository $transactionRepository, ValidatorInterface $validator, EntityManagerInterface $entityManager, IriConverterInterface $iriConverter) {
        $this->transactionRepository = $transactionRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->iriConverter = $iriConverter;
    } 

    /**
     * Runs after login event.
     *
     * @Route("/ipg", name="app_ipg", methods={"POST", "GET"})
     */
    public function ipg(RequestStack $request)
    {
        $this->saveIpgReturn($request);
        
        return new Response(null, 204);
    }

    /**
     * @Route("/ipg/client", name="ipg_client", methods={"POST"})
     */
    public function ipgClient(RequestStack $request): Response
    {
        //$ipgReturn = $this->saveIpgReturn($request, true);
        $currentRequest = $request->getCurrentRequest();

        // Construct the redirect URL
        $transaction = $this->transactionRepository->find($currentRequest->get('oid'));
        $eventIri = $this->iriConverter->getIriFromItem($transaction->getBasket()->getEvent());
        $tail = $currentRequest->get('approval_code')[0] === "Y" ? "success" : "fail";
 
        $redirectUrl = sprintf('https://%s%s/%s', $_SERVER['HTTP_HOST'], $eventIri, $tail);

        // Perform the redirect
        return new RedirectResponse($redirectUrl);
    }

    private function saveIpgReturn($request, $client = false) {
        $currentRequest = $request->getCurrentRequest();
        
        $transaction = $this->transactionRepository->find($currentRequest->get('oid'));
        $txndate = $currentRequest->get('txndatetime') !== null ? date_create_from_format("Y:m:d-H:i:s", $currentRequest->get('txndatetime')) : null;

        $hash = $client ? $currentRequest->get('response_hash') : $currentRequest->get('notification_hash');
        
        $ipgReturn = new IPGReturn();
        $ipgReturn->setTransaction($transaction)
            ->setTxndate($txndate)
            ->setApprovalCode($currentRequest->get('approval_code'))
            ->setNotificationHash($hash)
            ->setStatus($currentRequest->get('status'))
            ->setEndpointTransactionId($currentRequest->get('endpointTransactionId'))
            ->setIpgTransactionId($currentRequest->get('ipgTransactionId'))
            ->setClientReturn($client)
            ->setCurrency($currentRequest->get('currency'))
            ->setTotal($currentRequest->get('chargetotal'))
            ->setFailReason($currentRequest->get('fail_reason'));

        $errors = $this->validator->validate($ipgReturn);
        if($errors->count() > 0) {
          throw new InvalidIPGHashHttpException();
        }
        
        $this->entityManager->persist($ipgReturn);
        $this->entityManager->flush();

        return $ipgReturn;
    }

}

