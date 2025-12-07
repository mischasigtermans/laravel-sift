<?php

namespace MischaSigtermans\Sift;

use Illuminate\Support\Facades\Config;
use MischaSigtermans\Sift\Rules\BusinessEmail;

class Sift
{
    /**
     * Extract domain from an email, optionally filtering common providers.
     */
    public static function domain(string $email, bool $includeCommon = false): ?string
    {
        $domain = self::extractDomain($email);

        return ($domain && ($includeCommon || ! self::isCommon($domain))) ? $domain : null;
    }

    /**
     * Extract domains from multiple emails, filtering common providers by default.
     *
     * @param  iterable<string>  $emails
     * @return array<string>
     */
    public static function domains(iterable $emails, bool $includeCommon = false): array
    {
        $domains = [];

        foreach ($emails as $email) {
            $domain = self::domain($email, $includeCommon);
            if ($domain !== null) {
                $domains[] = $domain;
            }
        }

        return array_values(array_unique($domains));
    }

    /**
     * Extract all domains from emails without filtering.
     *
     * @param  iterable<string>  $emails
     * @return array<string>
     */
    public static function extractAll(iterable $emails): array
    {
        $domains = [];

        foreach ($emails as $email) {
            $domain = self::extractDomain($email);
            if ($domain !== null) {
                $domains[] = $domain;
            }
        }

        return array_values(array_unique($domains));
    }

    /**
     * Get statistics about a collection of emails.
     *
     * @param  iterable<string>  $emails
     * @return array{total: int, business: int, personal: int, business_rate: float, top_domains: array<string, int>}
     */
    public static function stats(iterable $emails, int $topDomainsLimit = 10): array
    {
        $total = 0;
        $business = 0;
        $personal = 0;
        $domainCounts = [];

        foreach ($emails as $email) {
            $total++;
            $domain = self::extractDomain($email);

            if ($domain === null) {
                continue;
            }

            if (self::isCommon($domain)) {
                $personal++;
            } else {
                $business++;
                $domainCounts[$domain] = ($domainCounts[$domain] ?? 0) + 1;
            }
        }

        arsort($domainCounts);

        return [
            'total' => $total,
            'business' => $business,
            'personal' => $personal,
            'business_rate' => $total > 0 ? round($business / $total * 100, 1) : 0.0,
            'top_domains' => array_slice($domainCounts, 0, $topDomainsLimit, true),
        ];
    }

    /**
     * Check if a domain or email belongs to a common provider.
     */
    public static function isCommon(string $emailOrDomain): ?bool
    {
        $domain = self::extractDomain($emailOrDomain);

        if (! $domain) {
            return null;
        }

        return in_array($domain, self::getCommonDomains(), true);
    }

    /**
     * Get the merged list of common domains.
     *
     * @return array<string>
     */
    public static function getCommonDomains(): array
    {
        $defaults = DefaultDomains::LIST;
        $excludes = Config::get('sift.exclude_default_domains', []);
        $additional = Config::get('sift.additional_domains', []);

        // Remove excluded domains from defaults
        $filtered = array_diff($defaults, $excludes);

        // Add additional domains
        return array_values(array_unique(array_merge($filtered, $additional)));
    }

    /**
     * Check if a domain or email is a business email (not a common provider).
     */
    public static function isBusiness(string $emailOrDomain): ?bool
    {
        $isCommon = self::isCommon($emailOrDomain);

        return $isCommon === null ? null : ! $isCommon;
    }

    /**
     * Extract the domain portion from an email or return domain as-is.
     */
    public static function extractDomain(string $emailOrDomain): ?string
    {
        if (str_contains($emailOrDomain, '@')) {
            $domain = strrchr($emailOrDomain, '@');

            return $domain ? strtolower(substr($domain, 1)) : null;
        }

        return strtolower($emailOrDomain);
    }

    /**
     * Get the validation rule for business emails.
     */
    public static function rule(): BusinessEmail
    {
        return new BusinessEmail;
    }
}
