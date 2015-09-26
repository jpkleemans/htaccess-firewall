# Htaccess Firewall

[![Build Status](https://img.shields.io/travis/jpkleemans/htaccess-firewall.svg)](https://travis-ci.org/jpkleemans/htaccess-firewall)
[![Code Quality](https://img.shields.io/scrutinizer/g/jpkleemans/htaccess-firewall.svg)](https://scrutinizer-ci.com/g/jpkleemans/htaccess-firewall/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

Simple access control using Htaccess.

## Install

Via Composer

``` bash
$ composer require jpkleemans/htaccess-firewall:dev-master
```

## Usage

First, create an instance of the `HtaccessFirewall` class:

``` php
use HtaccessFirewall\Firewall;

$firewall = new HtaccessFirewall('path/to/.htaccess');
```

### Block host

``` php
$host = Host::fromString('123.0.0.1');

$firewall->block($host);
```

### Unblock host

``` php
$host = Host::fromString('123.0.0.1');

$firewall->unblock($host);
```

### Block current visitor

``` php
$host = Host::fromCurrentRequest();

$firewall->block($host);
```

> More coming soon...

## Testing

``` bash
$ phpspec run
```
