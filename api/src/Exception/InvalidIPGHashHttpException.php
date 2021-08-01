<?php

/*
 * This file is part of the CoopTilleulsForgotPasswordBundle package.
 *
 * (c) Vincent Chalamon <vincent@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class InvalidIPGHashHttpException extends HttpException 
{
    public function __construct()
    {
        parent::__construct(403, "Access Forbidden");
    }
}
