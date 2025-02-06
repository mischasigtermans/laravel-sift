<?php

namespace MischaSigtermans\Sift\Tests\Feature;

use Illuminate\Support\Facades\Config;
use MischaSigtermans\Sift\Facades\Sift;
use MischaSigtermans\Sift\Tests\TestCase;

class SiftTest extends TestCase
{
    public function it_extracts_the_domain_from_an_email()
    {
        expect(Sift::domain('user@example.com'))->toBe('example.com');
    }

    public function it_filters_out_public_email_domains_by_default()
    {
        expect(Sift::domain('user@gmail.com'))->toBeNull();
    }

    public function it_allows_public_domains_when_explicitly_allowed()
    {
        expect(Sift::domain('user@gmail.com', true))->toBe('gmail.com');
    }

    public function it_correctly_identifies_common_domains()
    {
        expect(Sift::isCommon('aol.com'))->toBeTrue();
        expect(Sift::isCommon('live.com'))->toBeTrue();
        expect(Sift::isCommon('yandex.com'))->toBeTrue();
    }

    public function it_recognizes_custom_additional_domains_from_config()
    {
        Config::set('sift.additional_domains', ['customdomain.com']);

        expect(Sift::isCommon('customdomain.com'))->toBeTrue();
        expect(Sift::domain('user@customdomain.com'))->toBeNull();
    }
}
