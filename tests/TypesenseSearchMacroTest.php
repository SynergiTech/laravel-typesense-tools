<?php

namespace SynergiTech\LaravelTypesenseTools\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Orchestra\Testbench\TestCase;
use SynergiTech\LaravelTypesenseTools\LaravelTypesenseToolsServiceProvider;

class TypesenseSearchMacroTest extends TestCase
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

    public function testTypesenseSearchMacroIsRegisteredOnBuilder(): void
    {
        $this->assertTrue(Builder::hasGlobalMacro('typesenseSearch'));
    }

    public function testTypesenseSearchReturnsSameBuilderWhenSearchTermIsMissing(): void
    {
        $builder = new Builder($this->createStub(QueryBuilder::class));
        $macro = Builder::getGlobalMacro('typesenseSearch');

        $this->assertSame($builder, $macro->call($builder));
        $this->assertSame($builder, $macro->call($builder, ''));
    }
}
