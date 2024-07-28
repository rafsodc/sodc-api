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

    public function __construct(TransactionRepository $transactionRepository, ValidatorInterface $validator, EntityManagerInterface $entityManager) {
        $this->transactionRepository = $transactionRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
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
        $ipgReturn = $this->saveIpgReturn($request, true);

        // Construct the redirect URL
        $eventIri = $this->iriConverter->getIriFromItem($ipgReturn->getTransaction()->getBasket()->getEvent());
        $tail = $ipgReturn->isApproved() ? "success" : "fail";
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

    // approval_code|chargetotal|currency|txndatetime|storename
    // chargetotal|currency|txndatetime|storename|approval_code

//     txndate_processed: 28/07/24 18:34:59
// ccbin: 525303
// timezone: UTC
// oid: 690
// cccountry: GBR
// expmonth: 11
// hash_algorithm: SHA256
// action: https://www.ipg-online.com/connect/gateway/processing
// endpointTransactionId: 1016
// currency: 826
// processor_response_code: 00
// chargetotal: 1.00
// email: jack@jackdipper.com
// terminal_id: 32801589
// approval_code: Y:T57037:4528170318:PPXM:1016
// expyear: 2024
// response_hash: 24e1f8027d570ed92721e3e9e9d81274d8cea5043daf585ae4787cdf932e7171
// response_code_3dsecure: 1
// transactionNotificationURL: https://www.sodc.net/ipg
// schemeTransactionId: MCCMRP8480728
// tdate: 1722184499
// installments_interest: false
// bname: Jack Dipper
// ccbrand: MASTERCARD
// refnumber: T57037
// txntype: sale
// paymentMethod: M
// txndatetime: 2024:07:28-16:34:43
// cardnumber: (MASTERCARD) ... 6792
// ipgTransactionId: 104528170318
// status: APPROVED

}

