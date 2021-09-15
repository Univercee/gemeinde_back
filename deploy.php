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
//set('git_tty', true); 

// Other config
set('allow_anonymous_stats', false);
set('keep_releases', 1);

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
    ->set('labels', ['stage' => 'dev'])
    ->set('remote_user','admin')
    ->set('deploy_path', '/var/www/admin/www/dev-api.gemeindeonline.ch')
    ->set('dotenv', '{{deploy_path}}/shared/.env')
    ->set('multiplexing',false)
    ->set('identity_file','~/.ssh/id_admin@5.101.123.4-external');
    
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:cache:clear',
    'artisan:optimize',
    'artisan:migrate:fresh',
    'artisan:db:seed',
    'deploy:publish',
]);

task('restart-fpm', function () {
    $output = run('sudo systemctl restart fp2-php74-fpm.service');
    if(strlen(trim($output)) == 0) info("PHP-FPM restarted");
    else info($output);
});


// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:success','restart-fpm');
