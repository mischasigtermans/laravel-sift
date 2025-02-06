<?php

namespace MischaSigtermans\Sift;

use Illuminate\Support\Facades\Config;

class Sift
{
    public static function domain(string $email, bool $includeCommon = false): ?string
    {
        $domain = self::extractDomain($email);

        return ($domain && ($includeCommon || ! self::isCommon($domain))) ? $domain : null;
    }

    public static function isCommon(string $emailOrDomain): ?bool
    {
        $domain = self::extractDomain($emailOrDomain);

        if (! $domain) {
            return null;
        }

        $commonDomains = array_merge(
            Config::get('sift.common_domains', []),
            Config::get('sift.additional_domains', [])
        );

        return in_array($domain, $commonDomains, true);
    }

    public static function extractDomain(string $emailOrDomain): ?string
    {
        if (str_contains($emailOrDomain, '@')) {
            $domain = strrchr($emailOrDomain, '@');

            return $domain ? strtolower(substr($domain, 1)) : null;
        }

        return strtolower($emailOrDomain);
    }
}
