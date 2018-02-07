<?php

use function Deployer\{
    task, get, run
};

/**
 * Normalize asset timestamps
 */
task('deploy:assets', function () {
    $assets = implode(' ', array_map(function ($asset) {
        return "{{release_path}}/$asset";
    }, get('assets')));

    $time = date('Ymdhi.s');

    run("find $assets -exec touch -t $time {} ';' &> /dev/null || true");

    // BUGFIX: liip imagine need image folder
    run("mkdir -p {{deploy_path}}/shared/web/media/image");
})->desc('Normalize asset timestamps');
