<?php

namespace Tests\Unit;

use App\Services\Formatter;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormatterTest extends TestCase
{
    /**
     * @test
     */
    public function formatting_a_mac_with_no_separators_returns_a_valid_mac()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("0A003EB1452B"));
    }

    /**
     * @test
     */
    public function formatting_a_mac_with_dash_separators_returns_a_valid_mac()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("0A-00-3E-B1-45-2B"));
    }

    /**
     * @test
     */
    public function formatting_a_mac_with_period_separators_returns_a_valid_mac()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("0A:00:3E:B1:45:2B"));
    }

    /**
     * @test
     */
    public function formatting_a_mac_with_colon_separators_returns_a_valid_mac()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("0A:00:3E:B1:45:2B"));
    }

    /**
     * @test
     */
    public function formatting_a_mac_returns_it_in_uppercase()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("0a:00:3e:b1:45:2b"));
    }

    /**
     * @test
     */
    public function formatting_a_mac_missing_leading_zeroes_adds_them()
    {
        $formatter = new Formatter();
        $this->assertEquals("0A:00:3E:B1:45:2B",$formatter->formatMac("a:0:3e:b1:45:2b"));
    }
}
