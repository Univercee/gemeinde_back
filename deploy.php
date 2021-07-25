<?php
namespace Deployer;

require 'recipe/laravel.php';

set('bin/php', function () {
    return '/opt/php74/bin/php';
});

set('bin/composer', function () {
    return '/opt/php74/bin/php /usr/local/bin/composer';
});

// Project name
set('application', 'gena-api');

// Project repository
set('repository', 'git@github.com:bits-ee/gena.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Other config
set('allow_anonymous_stats', false);
set('default_stage', 'dev');

// Shared files/dirs between deploys 
set('shared_files', [
    '.env'
]);
add('shared_dirs', []);

set('writable_dirs', [
    'storage'
]);

// Hosts
host('dev-api.gemeindeonline.ch')
    ->stage('dev')
    ->user('admin')
    ->identityFile('~/.ssh/id_admin@5.101.123.4-external')
    ->set('deploy_path', '/var/www/admin/www/dev-api.gemeindeonline.ch')
    ->set('dotenv', '{{deploy_path}}/shared/.env');;
    
// Tasks
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:cache:clear',
    'artisan:optimize',
    'artisan:migrate:fresh',
    'artisan:db:seed',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);


// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');