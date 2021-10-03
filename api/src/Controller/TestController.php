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
use Psr\Log\LoggerInterface;

/**
 * SecurityController for /login.  This runs when a user has been correctly logged in via json_login.
 * This also gets called when the content-type is the wrong format, but is caught by checking IS_AUTHENTICATED_FULLY.
 * Returns a Location header to user IRI if the user is logged in.
 *
 * @package App\Controller
 */
class TestController extends AbstractController
{

    /**
     * Runs after login event.
     *
     * @Route("/test", name="app_test", methods={"POST", "GET"})
     */
    public function test(RequestStack $request, TransactionRepository $transactionRepository,  ValidatorInterface $validator, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $logger->error('Test error');
        
        
        return new Response(null, 204);
    }

}

