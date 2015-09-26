# htaccess-firewall

[![Build Status](https://travis-ci.org/jpkleemans/htaccess-firewall.svg?branch=master)](https://travis-ci.org/jpkleemans/htaccess-firewall)

Simple access control using Htaccess.

## Install

Via Composer

``` bash
$ composer require jpkleemans/htaccess-firewall
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
