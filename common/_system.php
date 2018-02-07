<?php

use function Deployer\{
    task, get, run
};

task('reload:fpm', function () {
    run(sprintf('systemctl restart php%s-fpm', get('php_version')));
});

task('reload:nginx', function () {
    run('nginx -t');
    run('systemctl restart nginx');
});

task('reload:supervisor', function () {
    run('systemctl restart supervisor');
});

task('reload:mysql', function () {
    run('systemctl restart mysql');
});
