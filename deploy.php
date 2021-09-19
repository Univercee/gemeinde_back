<?php
namespace Deployer;

require 'recipe/laravel.php';

set('bin/php', function () {
    return '/opt/php80/bin/php';
});

set('bin/composer', function () {
    return '/opt/php80/bin/php /usr/local/bin/composer';
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

// Deployer 6.x only
//set('default_stage', 'dev');

//host('dev-api.gemeindeonline.ch')
//    ->stage('dev')
//    ->user('admin')
//    ->identityFile('~/.ssh/id_admin@5.101.123.4-external')
//    ->set('deploy_path', '/var/www/admin/www/dev-api.gemeindeonline.ch')
//    ->set('dotenv', '{{deploy_path}}/shared/.env')
//    ->multiplexing(false);

// Deployer 7.x only
host('dev-api.gemeindeonline.ch')
    ->set('labels', ['stage' => 'dev'])
    ->set('remote_user','admin')
    ->set('deploy_path', '/var/www/admin/www/dev-api.gemeindeonline.ch')
    ->set('dotenv', '{{deploy_path}}/shared/.env')
    ->set('multiplexing',false)
    ->set('identity_file','~/.ssh/id_admin@5.101.123.4-external');

// Deployer 7.x Lumen tasks    
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:cache:clear',
    'artisan:optimize',
    'artisan:migrate:fresh',
    'artisan:db:seed',
    'deploy:publish',
]);

// Deployer 6.x Lumen task
//task('deploy', [
//    'deploy:info',
//    'deploy:prepare',
//    'deploy:lock',
//    'deploy:release',
//    'deploy:update_code',
//    'deploy:shared',
//    'deploy:vendors',
//    'deploy:writable',
//    'artisan:cache:clear',
//    'artisan:optimize',
//    'artisan:migrate:fresh',
//    'artisan:db:seed',
//    'deploy:symlink',
//    'deploy:unlock',
//    'cleanup',
//]);

//Deployment finishes with re-pointing /current symplink from previour release to the new one
//However most of the webservers have configuration cached, and will route traffic to old old path
//To overcome it  we need to restart webserver or PHP-FPM service. This requires root or sudo rights
//Deployer should not have root rights in the remote system, so sudo is a way to go.
//However, sudo requires password to be entered, so we need to avoid it during automatic deployment  
//Solution would be to allow deployment user to execute some sudo commands without a password:
// ->/etc/sudoers.d
// ->touch admin_restart_fpm
// ->chmod 0440 admin_restart_fpm
// ->"admin ALL=(ALL) NOPASSWD: /bin/systemctl restart fp2-php74-fpm.service" > admin_restart_fpm
// (pattern is" "%username% ALL=(ALL) NOPASSWD: %coma-separated command list%"
// -> sudo service sudo restart

task('restart-fpm', function () {
    $output = run('sudo systemctl restart fp2-php80-fpm.service');
    if(strlen(trim($output)) == 0) info("PHP-FPM restarted");
    else info($output);
});


// If deploy fails - unlock automatically
after('deploy:failed', 'deploy:unlock');

// If deploe success - restart FPM to avoid symlink caching
after('deploy:success','restart-fpm'); //Deployer 7.x success hook
//after('success','restart-fpm'); //Deployer 6.x success hook
