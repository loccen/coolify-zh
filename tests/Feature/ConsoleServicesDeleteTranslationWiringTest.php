<?php

use App\Console\Commands\ServicesDelete;
use Illuminate\Support\Facades\App;

it('wires services delete console strings through console translations', function () {
    $basePath = dirname(__DIR__, 2);
    $command = file_get_contents($basePath.'/app/Console/Commands/ServicesDelete.php');
    $enTranslations = require $basePath.'/lang/en/console.php';
    $zhTranslations = require $basePath.'/lang/zh_CN/console.php';

    expect($command)
        ->toContain("trans('console.services_delete.description'")
        ->toContain("trans('console.services_delete.select_resource'")
        ->toContain("trans('console.services_delete.select_application'")
        ->toContain("trans('console.services_delete.select_database'")
        ->toContain("trans('console.services_delete.select_service'")
        ->toContain("trans('console.services_delete.select_server'")
        ->toContain("trans('console.services_delete.no_applications_to_delete'")
        ->toContain("trans('console.services_delete.no_databases_to_delete'")
        ->toContain("trans('console.services_delete.no_services_to_delete'")
        ->toContain("trans('console.services_delete.confirm_delete_selected_resources'");

    expect($enTranslations['services_delete']['description'])->toBe('Delete services and related resources from the database')
        ->and($enTranslations['services_delete']['select_resource'])->toBe('What service do you want to delete?')
        ->and($enTranslations['services_delete']['select_application'])->toBe('What application do you want to delete?')
        ->and($enTranslations['services_delete']['select_database'])->toBe('What database do you want to delete?')
        ->and($enTranslations['services_delete']['select_service'])->toBe('What service do you want to delete?')
        ->and($enTranslations['services_delete']['select_server'])->toBe('What server do you want to delete?')
        ->and($enTranslations['services_delete']['no_applications_to_delete'])->toBe('There are no applications to delete.')
        ->and($enTranslations['services_delete']['no_databases_to_delete'])->toBe('There are no databases to delete.')
        ->and($enTranslations['services_delete']['no_services_to_delete'])->toBe('There are no services to delete.')
        ->and($enTranslations['services_delete']['confirm_delete_selected_resources'])->toBe('Are you sure you want to delete all selected resources?');

    expect($zhTranslations['services_delete']['description'])->toBe('从数据库中删除服务及相关资源')
        ->and($zhTranslations['services_delete']['select_resource'])->toBe('你想删除哪种服务？')
        ->and($zhTranslations['services_delete']['select_application'])->toBe('你想删除哪个应用？')
        ->and($zhTranslations['services_delete']['select_database'])->toBe('你想删除哪个数据库？')
        ->and($zhTranslations['services_delete']['select_service'])->toBe('你想删除哪个服务？')
        ->and($zhTranslations['services_delete']['select_server'])->toBe('你想删除哪台服务器？')
        ->and($zhTranslations['services_delete']['no_applications_to_delete'])->toBe('没有可删除的应用。')
        ->and($zhTranslations['services_delete']['no_databases_to_delete'])->toBe('没有可删除的数据库。')
        ->and($zhTranslations['services_delete']['no_services_to_delete'])->toBe('没有可删除的服务。')
        ->and($zhTranslations['services_delete']['confirm_delete_selected_resources'])->toBe('你确定要删除所有选中的资源吗？');
});

it('resolves services delete translations in en and zh_CN', function () {
    App::setLocale('en');

    expect((new ServicesDelete)->getDescription())->toBe('Delete services and related resources from the database')
        ->and(trans('console.services_delete.select_resource'))->toBe('What service do you want to delete?')
        ->and(trans('console.services_delete.select_application'))->toBe('What application do you want to delete?')
        ->and(trans('console.services_delete.select_database'))->toBe('What database do you want to delete?')
        ->and(trans('console.services_delete.select_service'))->toBe('What service do you want to delete?')
        ->and(trans('console.services_delete.select_server'))->toBe('What server do you want to delete?')
        ->and(trans('console.services_delete.no_applications_to_delete'))->toBe('There are no applications to delete.')
        ->and(trans('console.services_delete.no_databases_to_delete'))->toBe('There are no databases to delete.')
        ->and(trans('console.services_delete.no_services_to_delete'))->toBe('There are no services to delete.')
        ->and(trans('console.services_delete.confirm_delete_selected_resources'))->toBe('Are you sure you want to delete all selected resources?');

    App::setLocale('zh_CN');

    expect((new ServicesDelete)->getDescription())->toBe('从数据库中删除服务及相关资源')
        ->and(trans('console.services_delete.select_resource'))->toBe('你想删除哪种服务？')
        ->and(trans('console.services_delete.select_application'))->toBe('你想删除哪个应用？')
        ->and(trans('console.services_delete.select_database'))->toBe('你想删除哪个数据库？')
        ->and(trans('console.services_delete.select_service'))->toBe('你想删除哪个服务？')
        ->and(trans('console.services_delete.select_server'))->toBe('你想删除哪台服务器？')
        ->and(trans('console.services_delete.no_applications_to_delete'))->toBe('没有可删除的应用。')
        ->and(trans('console.services_delete.no_databases_to_delete'))->toBe('没有可删除的数据库。')
        ->and(trans('console.services_delete.no_services_to_delete'))->toBe('没有可删除的服务。')
        ->and(trans('console.services_delete.confirm_delete_selected_resources'))->toBe('你确定要删除所有选中的资源吗？');
});
