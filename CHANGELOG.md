# Release Notes

All notable changes to this project will be documented in this file.


## 2.0.0-Beta.5 (2022-12-08)

- Add API interface for home page and list page, for example, page flip without refresh.
- Fix the redundant `/` symbols in pagination.
- Jump to post homepage after successful posting.
- Move private mode view file to `portal/private.blade.php` directory.
- Add policy terms page `portal/policies.blade.php`
- Adaptation of main program split `token` parameter


## 2.0.0-Beta.4 (2022-12-01)

- `interactive` API modified to `interaction`
- Get top content wrapped as `fs_sticky_posts` and `fs_sticky_comments`
- Count when cache adds `null` results


## 2.0.0-Beta.3 (2022-11-28)

- Support custom cookie prefix to avoid conflict with domain name
- token expiration does not automatically empty the cookie
- Fix the problem that the message reading status does not change in real time due to caching
- Configure key name account_cookie_status to account_cookies_status
- Configure key name account_cookie to account_cookies
- Language pack identifier name accountPoliciesCookie changed to accountPoliciesCookies
- Account and user details are cached


## 2.0.0-Beta.2 (2022-11-23)

- Add API for account deletion and undelete


## 2.0.0-Beta.1 (2022-11-22)

- Adapted to Fresns 2.x API
- Adaptation of the new plugin manager
