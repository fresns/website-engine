# Release Notes

All notable changes to this project will be documented in this file.


## 2.4.7 (2023-04-27)

- fix: private page
- fix: api request name


## 2.4.6 (2023-04-22)

- fix: account edit
- fix: comment parameter name
- chore: lang menus


## 2.4.5 (2023-04-15)

- add `$fresnsVersion`


## 2.4.4 (2023-04-14)

- Optimise message routing
- Optimise prompt text
- Optimise editor draft logic
- Adjust location encoding parameters


## 2.4.3 (2023-03-05)

- Fixed issue causing incorrect output of private mode contents.
- Adapted to the new configs interface.


## 2.4.2 (2023-03-02)

- Customize new Editor API parameter names
- Optimize account credentials cache key names and labels
- Fix failure to create new drafts


## 2.4.1 (2023-02-28)

- Fix the problem of inaccurate hints when the editor is verified
- Fix the cache tag error problem of registration and login


## 2.4.0 (2023-02-26)

- All list interfaces support Ajax requests
- Optimize editor comment function
- Optimize the cache tag
- Fix the problem of unsynchronized language tags when the interface is requested
- Fix login status and error reporting, compatible with interface abnormal status
- Unikey plugin when the plugin configuration item URL is empty
- Disable SSL authentication for API requests


## 2.3.2 (2023-02-16)

- Add conversation message list API
- Add `engineVersion` parameter
- Fix problem with `themeVersion` parameter
- Fix default data if `data_get` is empty


## 2.3.1 (2023-02-09)

- Clear user panel cache after deleting a message
- Fix the number of pages in the query parameter
- Output topic version number


## 2.3.0 (2023-02-01)

- New `fs_stickers` wrapper function
- New composite login interface
- Drop uuid and use ulid
- Optimise editor parameters
- Fix problem requesting account credentials during login
- Fix problem with user panel cache not being cleared after drafts are deleted


## 2.2.0 (2023-01-20)

- Take over the custom 404 page
- Repair the problem that the account credentials of headers cannot be obtained when logging in
- Modify headers parameter name
- Change plugin path signature to path credentials `urlAuthorization`


## 2.1.0 (2023-01-18)

- Compatible with the / symbol for paging paths
- Group-compatible empty arrays
- Move wrapper function file location


## 2.0.1 (2023-01-11)

- Optimize cache tags


## 2.0.0 (2023-01-09)

- Set token validity to one year by default
- The list of comments on the post page is arranged in positive chronological order
- Fix configToQuery
- Remove timezone configuration and let API handle timezone


## 2.0.0-beta.8 (2022-12-24)

- Refactoring the cache mechanism
- Default return to home page after registration and login
- Optimize multi-language support


## 2.0.0-beta.7 (2022-12-13)

- Refactor: providers


## 2.0.0-beta.6 (2022-12-12)

- Content types use the wrapper function `fs_content_types('post')`
- Automatic login after successful registration


## 2.0.0-beta.5 (2022-12-08)

- Add API interface for home page and list page, for example, page flip without refresh.
- Fix the redundant `/` symbols in pagination.
- Jump to post homepage after successful posting.
- Move private mode view file to `portal/private.blade.php` directory.
- Add policy terms page `portal/policies.blade.php`
- Adaptation of main program split `token` parameter


## 2.0.0-beta.4 (2022-12-01)

- `interactive` API modified to `interaction`
- Get top content wrapped as `fs_sticky_posts` and `fs_sticky_comments`
- Count when cache adds `null` results


## 2.0.0-beta.3 (2022-11-28)

- Support custom cookie prefix to avoid conflict with domain name
- token expiration does not automatically empty the cookie
- Fix the problem that the message reading status does not change in real time due to caching
- Configure key name account_cookie_status to account_cookies_status
- Configure key name account_cookie to account_cookies
- Language pack identifier name accountPoliciesCookie changed to accountPoliciesCookies
- Account and user details are cached


## 2.0.0-beta.2 (2022-11-23)

- Add API for account deletion and undelete


## 2.0.0-beta.1 (2022-11-22)

- Adapted to Fresns 2.x API
- Adaptation of the new plugin manager
