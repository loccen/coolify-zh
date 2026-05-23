<?php

use App\Livewire\Project\New\Select;
use Illuminate\Support\Facades\App;

it('translates one-click service slogans and database descriptions on the select page', function () {
    App::setLocale('zh_CN');

    $component = app(Select::class);
    $payload = $component->loadServices();

    $services = collect($payload['services'])->keyBy('name');
    $databases = collect($payload['databases'])->keyBy('id');

    expect($services->get('Activepieces')['slogan'])->toBe('开源无代码业务自动化平台。')
        ->and($services->get('Actualbudget')['slogan'])->toBe('本地优先的个人理财应用。')
        ->and($services->get('Affine')['slogan'])->toBe('Affine 是一个开源的一体化知识管理工作区和操作系统，可作为 Notion/Miro 的替代方案。')
        ->and($services->get('Alexandrie')['slogan'])->toBe('为速度、清晰度和创造力而设计的强大 Markdown 工作区。')
        ->and($services->get('Anythingllm')['slogan'])->toBe('AnythingLLM 是一款易用的一体化 AI 应用，可实现 RAG、AI Agents 等功能，无需编写代码，也不用折腾基础设施。')
        ->and($services->get('Appflowy')['slogan'])->toBe('AppFlowy 是一个 AI 协作工作空间，让你在不失去数据控制权的前提下完成更多工作。')
        ->and($databases->get('redis')['description'])->toBe('Redis 是一种源码可用的内存存储，可作为分布式内存键值数据库、缓存和消息代理，并支持可选持久化。')
        ->and($databases->get('keydb')['description'])->toBe('KeyDB 是一款数据库，可为多种数据结构和工作负载提供高性能、低延迟与可扩展性。')
        ->and($databases->get('dragonfly')['description'])->toBe('Dragonfly DB 是 Redis 的即插即用替代方案，吞吐量可达 Redis 的 25 倍，快照速度可达 12 倍。')
        ->and($databases->get('mongodb')['description'])->toBe('MongoDB 是一款源码可用、跨平台、面向文档的数据库程序。')
        ->and($databases->get('clickhouse')['description'])->toBe('ClickHouse 是一款列式数据库，支持实时分析、商业智能、可观测性、机器学习和 GenAI 等场景。');
});
