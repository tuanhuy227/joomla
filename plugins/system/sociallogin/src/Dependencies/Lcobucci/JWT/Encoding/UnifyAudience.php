<?php
declare(strict_types=1);

namespace Akeeba\Plugin\System\SocialLogin\Dependencies\Lcobucci\JWT\Encoding;

use Akeeba\Plugin\System\SocialLogin\Dependencies\Lcobucci\JWT\ClaimsFormatter;
use Akeeba\Plugin\System\SocialLogin\Dependencies\Lcobucci\JWT\Token\RegisteredClaims;

use function array_key_exists;
use function count;
use function current;

final class UnifyAudience implements ClaimsFormatter
{
    /** @inheritdoc */
    public function formatClaims(array $claims): array
    {
        if (
            ! array_key_exists(RegisteredClaims::AUDIENCE, $claims)
            || count($claims[RegisteredClaims::AUDIENCE]) !== 1
        ) {
            return $claims;
        }

        $claims[RegisteredClaims::AUDIENCE] = current($claims[RegisteredClaims::AUDIENCE]);

        return $claims;
    }
}
