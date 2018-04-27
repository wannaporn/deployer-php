<?php

use function Deployer\{
    task, run
};

task('docker:compose:ps', function () {
    run('cd {{deploy_root}}/.docker && docker-compose ps');
});

task('docker:compose:rm', function () {
    run('cd {{deploy_root}}/.docker && docker-compose rm --force');
});

task('docker:compose:stop', function () {
    run('cd {{deploy_root}}/.docker && docker-compose stop');
});

task('docker:compose:start', function () {
    run('cd {{deploy_root}}/.docker && docker-compose start');
});

task('docker:compose:restart', function () {
    run('cd {{deploy_root}}/.docker && docker-compose restart');
});

task('docker:compose:up', function () {
    run('cd {{deploy_root}}/.docker && docker-compose up');
});

task('docker:compose:down', function () {
    run('cd {{deploy_root}}/.docker && docker-compose down');
});

task('docker:compose:build', function () {
    run('cd {{deploy_root}}/.docker && docker-compose build');
});

task('docker:compose:up-build', function () {
    run('cd {{deploy_root}}/.docker && docker-compose up --build');
});
