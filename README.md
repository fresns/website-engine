<p align="center"><a href="https://fresns.org" target="_blank"><img src="https://raw.githubusercontent.com/fresns/docs/main/images/Fresns-Logo(orange).png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-%5E8.0-green" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-%5E5.7%7C%5E8.0-orange" alt="MySQL">
<img src="https://img.shields.io/badge/License-Apache--2.0-blue" alt="License">
</p>

## About Fresns

Fresns is a free and open source social network service software, a general-purpose community product designed for cross-platform, and supports flexible and diverse content forms. It conforms to the trend of the times, satisfies a variety of operating scenarios, is more open and easier to re-development.

- Users should read the [installation](https://fresns.org/guide/install.html) and [operating](https://fresns.org/guide/operating.html) documentation.
- Extensions developers should read the [extension documentation](https://fresns.org/extensions/) and [database structure](https://fresns.org/database/).
- For client developers (web or app), please read the [API reference](https://fresns.org/api/) documentation.

## Introduction

Fresns officially developed website engine, integrated to run in the main application.

## Installation

- Installation with key name: `FresnsEngine`
- Installation using command: `php artisan fresns:require FresnsEngine`

### SDK Description

#### Information

* System configuration information

```php
Factory::information([
    "appId",
    ""
])->config->get($itemKey, $itemTag, $pageSize, $page);
```

* Extended Configuration Information

```php
Factory::information()->extension->get($type, $scene , $pageSize, $page);
```

#### Account

* Account Login

```php
Factory::account()->auth->login($mid, $password)
```

* Account Registration

```php
Factory::account()->auth->register($type,$account, $countryCode,$verifyCode,$password,$nickname)
```

#### User

* User Login

```php
Factory::user()->auth->login($mid, $password)
```

#### Editor

* Get a list of drafts

```php
Factory::editor()->draft->lists($type, $status, $class, $pageSize, $page);
```

* Get draft details

```php
Factory::editor()->draft->detail($type, $logId);
```

* Create a new draft

```php
Factory::editor()->draft->create($type, $uuid, $pid);
```

### Template tag description

**Read local database configuration table**

`fresnsengine_config('itemKey')`

**Read API configuration interface**

`fs_config('itemKey')`

**Read API language configuration**

`fs_lang('name')`

[View the list of names](https://fresns.org/database/dictionary/language-pack.html)


## Contributing

Thank you for considering contributing to the Fresns core library! The contribution guide can be found in the [Fresns documentation](https://fresns.org/community/join.html).

## Code of Conduct

In order to ensure that the Fresns community is welcoming to all, please review and abide by the [Code of Conduct](https://fresns.org/community/join.html#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Fresns, please send an e-mail to Taylor Otwell via [support@fresns.org](mailto:support@fresns.org). All security vulnerabilities will be promptly addressed.

## License

Fresns is open-sourced software licensed under the [Apache-2.0 license](https://github.com/fresns/fresns/blob/main/LICENSE).
