# Deployer
Just a deploy template.

## Install
```shell
$ composer require intbizth/deployer-php:dev-master
```
`MUST` without `--dev`

## Usage
See [servers.dist.yml](servers.dist.yml)

 - `common:system:install` install web server and friends.
 - `common:install` first install project.
 - `common:deploy` update project.
 - `common:deploy_quick` quick update project -- without upload assets.

 PS. No `npm` or `yarn` action on target machine, `MUST` to install all assets locally before deploy.

## TODOs
  - [ ] Separate service template in require to use.

## LICENSE
MIT
