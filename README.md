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

First, create an instance of the `HtaccessFirewall` class:

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

### Block current visitor

``` php
$host = IP::fromCurrentRequest();

$firewall->deny($host);
```

> More coming soon...

## Testing

``` bash
$ phpspec run
```
