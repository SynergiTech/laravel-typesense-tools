<?php

namespace SynergiTech\LaravelTypesenseTools\Tests;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use SynergiTech\LaravelTypesenseTools\LaravelTypesenseToolsServiceProvider;

class CommandRegistrationTest extends TestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelTypesenseToolsServiceProvider::class,
        ];
    }

    public function testPackageCommandsAreRegistered(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('search:setup', $commands);
        $this->assertArrayHasKey('search:switch-alias', $commands);
        $this->assertArrayHasKey('search:delete-index', $commands);
        $this->assertArrayHasKey('search:cleanup', $commands);
    }
}
