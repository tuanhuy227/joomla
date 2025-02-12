<?php
declare(strict_types=1);

namespace Akeeba\Plugin\System\SocialLogin\Dependencies\Lcobucci\JWT\Signer;

use InvalidArgumentException;
use Akeeba\Plugin\System\SocialLogin\Dependencies\Lcobucci\JWT\Exception;

final class CannotSignPayload extends InvalidArgumentException implements Exception
{
    public static function errorHappened(string $error): self
    {
        return new self('There was an error while creating the signature:' . $error);
    }
}
