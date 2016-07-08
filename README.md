# Htaccess Firewall

[![Build Status](https://img.shields.io/travis/jpkleemans/htaccess-firewall.svg)](https://travis-ci.org/jpkleemans/htaccess-firewall)
[![Code Quality](https://img.shields.io/scrutinizer/g/jpkleemans/htaccess-firewall.svg)](https://scrutinizer-ci.com/g/jpkleemans/htaccess-firewall/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/01ac1272-ec69-43ab-b918-bbed9898e073/big.png)](https://insight.sensiolabs.com/projects/01ac1272-ec69-43ab-b918-bbed9898e073)

Simple access control using Htaccess.

> This library is currently under development. Things will change!

## Install

Via Composer

``` bash
$ composer require jpkleemans/htaccess-firewall:dev-master
```

## Usage

First, create an instance of the `HtaccessFirewall\HtaccessFirewall` class:

``` php
$firewall = new HtaccessFirewall('path/to/.htaccess');
```

### Block IP

``` php
$host = IP::fromString('123.0.0.1');

$firewall->deny($host);
```

### Unblock IP

``` php
$host = IP::fromString('123.0.0.1');

$firewall->undeny($host);
```

### Get all denied hosts

``` php
$hosts = $firewall->getDenied();
```

### Deactivate firewall (comment .htaccess lines)

``` php
$firewall->deactivate();

// And to reactivate:
$firewall->reactivate();
```

### Set 403 message

``` php
$hosts = $firewall->set403Message('You are blocked!');

// And to remove:
$hosts = $firewall->remove403Message();
```

## Use other filesystem

You can use another filesystem by passing it as the second argument of the `HtaccessFirewall` constructor.
The filesystem must implement the `HtaccessFirewall\Filesystem\Filesystem` interface.

``` php
$filesystem = new YourCustomFilesystem();
$firewall = new HtaccessFirewall('path/to/.htaccess', $filesystem);
```

## Testing

``` bash
$ phpspec run
```
