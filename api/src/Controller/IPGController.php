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

/**
 * SecurityController for /login.  This runs when a user has been correctly logged in via json_login.
 * This also gets called when the content-type is the wrong format, but is caught by checking IS_AUTHENTICATED_FULLY.
 * Returns a Location header to user IRI if the user is logged in.
 *
 * @package App\Controller
 */
class IPGController extends AbstractController
{

    /**
     * Runs after login event.
     *
     * @Route("/ipg", name="app_ipg", methods={"POST", "GET"})
     */
    public function ipg(RequestStack $request, TransactionRepository $transactionRepository,  ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $currentRequest = $request->getCurrentRequest();
        
        //dd($transaction);
        //dd($request->getCurrentRequest());
        
        $transaction = $transactionRepository->find($currentRequest->get('oid'));
        $txndate = $currentRequest->get('txndatetime') !== null ? date_create_from_format("Y:m:d-H:i:s", $currentRequest->get('txndatetime')) : null;
        
        $ipgReturn = new IPGReturn();
        $ipgReturn->setTransaction($transaction)
            ->setTxndate($txndate)
            ->setApprovalCode($currentRequest->get('approval_code'))
            ->setNotificationHash($currentRequest->get('notification_hash'))
            ->setStatus($currentRequest->get('status'))
            ->setEndpointTransactionId($currentRequest->get('endpointTransactionId'))
            ->setIpgTransactionId($currentRequest->get('ipgTransactionId'))
            ->setCurrency($currentRequest->get('currency'))
            ->setTotal($currentRequest->get('chargetotal'))
            ->setFailReason($currentRequest->get('fail_reason'));

        $errors = $validator->validate($ipgReturn);
        if($errors->count() > 0) {
          throw new InvalidIPGHashHttpException();
        }
        
        $entityManager->persist($ipgReturn);
        $entityManager->flush();
        
        return new Response(null, 204);
    }

}

