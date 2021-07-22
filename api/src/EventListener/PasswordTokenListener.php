<?php
namespace App\EventListener;

use App\Entity\PasswordToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\CaptchaException;
//use ApiPlatform\Core\Exception\FilterValidationException;
//use ApiPlatform\Core\Validator\Exception\ValidationException; 
//use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpKernel\Exception\HttpException;


class PasswordTokenListener
{
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function prePersist(PasswordToken $passwordToken)
    {
        $errors = $this->validator->validate($passwordToken);
        if($errors->count() > 0) {

            $msg = "";
            foreach($errors as $error) {
                 $msg .= $error->getConstraint()->message . "\n\n";
            }

            throw new HttpException(422, $msg);
        }

    }

}
