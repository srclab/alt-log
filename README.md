# Laravel logging package

## Installation

```
$ composer require srclab/alt-log

$ php artisan alt-log:install
```

## Update assets

```
$ php artisan alt-log:assets
```

## Usage

```
alt_log()->file('log_file')->emergency('log message');
alt_log()->file('log_file')->alert('log message');
alt_log()->file('log_file')->critical('log message');
alt_log()->file('log_file')->error('log message');
alt_log()->file('log_file')->warning('log message');
alt_log()->file('log_file')->notice('log message');
alt_log()->file('log_file')->info('log message');
alt_log()->file('log_file')->debug('log message');

try {

} catch (\Exception $e) {
    alt_log()->file('log_file')->exception($e, 'log message');
}
```
