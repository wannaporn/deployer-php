<?php

use function Deployer\{
    task, upload, get, run, parse
};

task('common:install:init', function () {
    $phpVersion = get('php_version');

    // create target
    run('if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi');

    // upload config
    upload('{{app_path}}/*', "{{deploy_path}}/.deploy");

    // may -i '' -e ... @see https://stackoverflow.com/questions/19456518
    $substitutions = '';

    foreach ((array)get('substitutions', []) as $key => $value) {
        $substitutions .= sprintf("-e 's/%s/%s/g' ", $key, preg_quote(parse($value), '/'));
    }

    if (get('http_strict_server_name')) {
        // can't not use multiple condition on nginx -- if .. and ...
        $substitutions .= "-e 's/EDIT_ME_HTTP_STRICT_SERVER_NAME_COND/\$host != \$server_name/g' ";
    } else {
        // no way true condition
        $substitutions .= "-e 's/EDIT_ME_HTTP_STRICT_SERVER_NAME_COND/\$host = 0/g' ";
    }

    if (!empty($substitutions)) {
        run("find {{deploy_path}}/.deploy/ -type f -exec sed -i $substitutions {} \;");
    }

    run("ln -nfs {{deploy_path}}/.deploy/nginx/nginx.conf /etc/nginx/nginx.conf");
    run("ln -nfs {{deploy_path}}/.deploy/nginx/server.d /etc/nginx/server.d");
    run("ln -nfs {{deploy_path}}/.deploy/nginx/bots.d /etc/nginx/bots.d");
    run("ln -nfs {{deploy_path}}/.deploy/nginx/conf.d/blacklist.conf /etc/nginx/conf.d/blacklist.conf");
    run("chmod 0755 {{deploy_path}}/.deploy/nginx/conf.d/blacklist.conf");
    run("ln -nfs {{deploy_path}}/.deploy/nginx/backend.conf /etc/nginx/sites-enabled/backend");
    run("rm -rf /etc/nginx/sites-enabled/default");

    run("ln -nfs {{deploy_path}}/.deploy/cli/php.ini /etc/php/$phpVersion/cli/conf.d/10-custom.ini");
    run("ln -nfs {{deploy_path}}/.deploy/fpm/php.ini /etc/php/$phpVersion/fpm/conf.d/10-custom.ini");
    run("ln -nfs {{deploy_path}}/.deploy/fpm/pool/www.conf /etc/php/$phpVersion/fpm/pool.d/www.conf");

    run("ln -nfs {{deploy_path}}/.deploy/supervisor/supervisord.conf /etc/supervisor/supervisord.conf");

    // cannot use symlink for mysql due to permission on my.cnf denide by mysql user
    run("cp -f {{deploy_path}}/.deploy/mysql/my.cnf /etc/mysql/my.cnf");

    run("cp -f {{deploy_path}}/.deploy/ssl/* /etc/ssl");
})->setPrivate();

task('common:install:testing', function () {
    run("rm -rf {{deploy_path}}/current && mkdir -p {{deploy_path}}/current/web");
    run("ln -nfs {{deploy_path}}/.deploy/app.php {{deploy_path}}/current/web/app.php");
})->setPrivate();

task('common:install:clear', function () {
    run("rm -rf {{deploy_path}}/*");
})->setPrivate();

task('common:system:install', [
    'common:setup',
    'common:install:clear',
    'common:install:init',
    'common:install:testing',
    'reload:fpm',
    'reload:nginx',
    'reload:mysql',
    'reload:supervisor',
])->desc('Initial system');

task('common:system:reset_config', [
    'common:setup',
    'common:install:init',
    'reload:fpm',
    'reload:nginx',
    'reload:mysql',
    'reload:supervisor',
])->desc('Reset system');

task('common:system:reset_nginx', [
    'common:setup',
    'common:install:init',
    'reload:nginx',
])->desc('Reset Only Nginx system');

task('common:system:clear', [
    'common:install:clear',
])->desc('Clear system');
