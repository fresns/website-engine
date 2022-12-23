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

### Theme directory Structure

```php
ThemeDemo/          // Theme template folder (named after uniKey)
├── assets/             // Resource files
│   ├── fresns.png          // Theme cover image (square), must be present and fixed in position
│   ├── js/
│   │   └── app.js
│   └── css/
│       └── style.css
├── portal/             // Portal view folder
├── users/              // User view folder
├── groups/             // Group view folder
├── hashtags/           // Hashtag view folder
├── posts/              // Post view folder
├── comments/           // Comment view folder
├── profile/            // User profile view folder
├── search/             // Search view folder
├── account/            // Account view folder
├── follows/            // Follow view folder
├── messages/           // Message view folder
├── editor/             // Editor view folder
├── functions.blade.php // Theme Functions
├── private.blade.php   // Private Mode Tip Page
├── error.blade.php     // Error Message Page
└── theme.json          // Theme configuration file, responsible for defining the base properties of the theme
```

- [Theme Functions](https://fresns.org/extensions/theme/functions.html)
- [Path Structure](https://fresns.org/extensions/theme/structure.html)
- [Template Tags](https://fresns.org/extensions/theme/tags.html)

### Contributing

Thank you for considering contributing to the Fresns core library! The contribution guide can be found in the [Fresns documentation](https://fresns.org/community/join.html).

## Code of Conduct

In order to ensure that the Fresns community is welcoming to all, please review and abide by the [Code of Conduct](https://fresns.org/community/join.html#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Fresns, please send an e-mail to Taylor Otwell via [support@fresns.org](mailto:support@fresns.org). All security vulnerabilities will be promptly addressed.

## Cache Key List

- tag: `fresnsWeb`

```php
'fresns_web_languages'                                  // tag: fresnsWebConfigs
'fresns_web_api_host'                                   // tag: fresnsWebConfigs
'fresns_web_api_key'                                    // tag: fresnsWebConfigs
"fresns_web_key_{$keyId}"                               // tag: fresnsWebConfigs
"fresns_web_api_config_all_{$langTag}"                  // tag: fresnsWebConfigs
"fresns_web_db_config_{$itemKey}_{$langTag}"            // tag: fresnsWebConfigs
"fresns_web_code_message_all_{$unikey}_{$langTag}"      // tag: fresnsWebConfigs
"fresns_web_post_content_types_{$langTag}"              // tag: fresnsWebConfigs
"fresns_web_comment_content_types_{$langTag}"           // tag: fresnsWebConfigs

"fresns_web_account_{$aid}_{$langTag}"                  // tag: fresnsWebAccountData
"fresns_web_user_{$uid}_{$langTag}"                     // tag: fresnsWebUserData
"fresns_web_user_panel_{$uid}_{$langTag}"               // tag: fresnsWebUserData
"fresns_web_post_{$pid}"                                // tag: fresnsWebPostData
"fresns_web_comment_{$cid}"                             // tag: fresnsWebCommentData
"fresns_web_group_categories_by_{$uid}_{$langTag}"      // tag: fresnsWebGroupData
"fresns_web_group_tree_by_{$uid}_{$langTag}"            // tag: fresnsWebGroupData

"fresns_web_users_index_list_by_{$uid}_{$langTag}"      // tag: fresnsWebUserData
"fresns_web_groups_index_list_by_{$uid}_{$langTag}"     // tag: fresnsWebGroupData
"fresns_web_hashtags_index_list_by_{$uid}_{$langTag}"   // tag: fresnsWebHashtagData
"fresns_web_posts_index_list_by_{$uid}_{$langTag}"      // tag: fresnsWebPostData
"fresns_web_comments_index_list_by_{$uid}_{$langTag}"   // tag: fresnsWebCommentData

"fresns_web_users_index_list_by_guest_{$langTag}"       // tag: fresnsWebUserData
"fresns_web_groups_index_list_by_guest_{$langTag}"      // tag: fresnsWebGroupData
"fresns_web_hashtags_index_list_by_guest_{$langTag}"    // tag: fresnsWebHashtagData
"fresns_web_posts_index_list_by_guest_{$langTag}"       // tag: fresnsWebPostData
"fresns_web_comments_index_list_by_guest_{$langTag}"    // tag: fresnsWebCommentData

"fresns_web_users_list_by_{$uid}_{$langTag}"            // tag: fresnsWebUserData
"fresns_web_groups_list_by_{$uid}_{$langTag}"           // tag: fresnsWebGroupData
"fresns_web_hashtags_list_by_{$uid}_{$langTag}"         // tag: fresnsWebHashtagData
"fresns_web_posts_list_by_{$uid}_{$langTag}"            // tag: fresnsWebPostData
"fresns_web_comments_list_by_{$uid}_{$langTag}"         // tag: fresnsWebCommentData

"fresns_web_users_list_by_guest_{$langTag}"             // tag: fresnsWebUserData
"fresns_web_groups_list_by_guest_{$langTag}"            // tag: fresnsWebGroupData
"fresns_web_hashtags_list_by_guest_{$langTag}"          // tag: fresnsWebHashtagData
"fresns_web_posts_list_by_guest_{$langTag}"             // tag: fresnsWebPostData
"fresns_web_comments_list_by_guest_{$langTag}"          // tag: fresnsWebCommentData

"fresns_web_sticky_posts_{$langTag}"                    // tag: fresnsWebPostData
"fresns_web_group_{$gid}_sticky_posts_{$langTag}"       // tag: fresnsWebPostData
"fresns_web_post_{$pid}_sticky_comments_{$langTag}"     // tag: fresnsWebCommentData
```

## License

Fresns is open-sourced software licensed under the [Apache-2.0 license](https://github.com/fresns/fresns/blob/main/LICENSE).
