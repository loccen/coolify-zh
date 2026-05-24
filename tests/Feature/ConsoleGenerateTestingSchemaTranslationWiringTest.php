<?php

use App\Console\Commands\GenerateTestingSchema;

it('wires the generate testing schema command strings for each locale', function (string $locale, array $expected): void {
    app()->setLocale($locale);

    $command = app(GenerateTestingSchema::class);

    expect($command->getDescription())->toBe($expected['description'])
        ->and(trans('console.generate_testing_schema.error.not_postgresql', ['connection' => 'testing'], locale: $locale))->toBe($expected['error'])
        ->and(trans('console.generate_testing_schema.info.reading_schema', locale: $locale))->toBe($expected['reading_schema'])
        ->and(trans('console.generate_testing_schema.info.schema_written', ['path' => '/tmp/schema.sql'], locale: $locale))->toBe($expected['schema_written'])
        ->and(trans('console.generate_testing_schema.info.summary', ['tables' => 3, 'migrations' => 7], locale: $locale))->toBe($expected['summary']);
})->with([
    'en' => [
        'locale' => 'en',
        'expected' => [
            'description' => 'Generate SQLite testing schema from the PostgreSQL database',
            'error' => "Connection 'testing' is not PostgreSQL.",
            'reading_schema' => 'Reading schema from PostgreSQL...',
            'schema_written' => 'Schema written to /tmp/schema.sql',
            'summary' => '3 tables, 7 migration records.',
        ],
    ],
    'zh_CN' => [
        'locale' => 'zh_CN',
        'expected' => [
            'description' => '生成 PostgreSQL 数据库的 SQLite 测试 schema',
            'error' => "连接 'testing' 不是 PostgreSQL。",
            'reading_schema' => '正在从 PostgreSQL 读取 schema...',
            'schema_written' => 'Schema 已写入 /tmp/schema.sql',
            'summary' => '3 个表，7 条 migration 记录。',
        ],
    ],
])->group('console');
