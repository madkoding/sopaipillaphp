<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sopaipilla\Env;

class EnvTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = sys_get_temp_dir() . '/.env_test_' . uniqid();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testLoadSilentlyIgnoresMissingFile(): void
    {
        Env::load('/path/to/definitely/non-existent/.env');
        $this->assertTrue(true);
    }

    public function testLoadParsesKeyValuePairs(): void
    {
        file_put_contents($this->tempFile, "SOPAIPILLA_UNIT_TEST_KEY=hello_world\n");
        Env::load($this->tempFile);

        $this->assertSame('hello_world', Env::get('SOPAIPILLA_UNIT_TEST_KEY'));
    }

    public function testLoadStripsDoubleQuotes(): void
    {
        file_put_contents($this->tempFile, "SOPAIPILLA_QUOTED_DOUBLE=\"quoted string\"\n");
        Env::load($this->tempFile);

        $this->assertSame('quoted string', Env::get('SOPAIPILLA_QUOTED_DOUBLE'));
    }

    public function testLoadStripsSingleQuotes(): void
    {
        file_put_contents($this->tempFile, "SOPAIPILLA_QUOTED_SINGLE='single quoted'\n");
        Env::load($this->tempFile);

        $this->assertSame('single quoted', Env::get('SOPAIPILLA_QUOTED_SINGLE'));
    }

    public function testLoadSkipsCommentLines(): void
    {
        file_put_contents($this->tempFile, "# This is a comment\nSOPAIPILLA_AFTER_COMMENT=value\n");
        Env::load($this->tempFile);

        $this->assertSame('value', Env::get('SOPAIPILLA_AFTER_COMMENT'));
    }

    public function testGetReturnsDefaultWhenKeyMissing(): void
    {
        $this->assertSame('fallback', Env::get('SOPAIPILLA_DEFINITELY_MISSING_XYZ', 'fallback'));
    }

    public function testGetReturnsNullWhenKeyMissingAndNoDefault(): void
    {
        $this->assertNull(Env::get('SOPAIPILLA_DEFINITELY_MISSING_XYZ_2'));
    }

    public function testRequireThrowsRuntimeExceptionWhenKeyMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Missing required environment variable/');

        Env::require('SOPAIPILLA_DEFINITELY_MISSING_XYZ_3');
    }

    public function testRequireReturnsValueWhenKeyExists(): void
    {
        file_put_contents($this->tempFile, "SOPAIPILLA_REQUIRE_TEST=exists\n");
        Env::load($this->tempFile);

        $this->assertSame('exists', Env::require('SOPAIPILLA_REQUIRE_TEST'));
    }
}
