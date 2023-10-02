<p align="center"><a href="https://fresns.org" target="_blank"><img src="https://raw.githubusercontent.com/fresns/docs/main/images/Fresns-Logo(orange).png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/Platform-Web-blue" alt="Platform">
<img src="https://img.shields.io/badge/PHP-%5E8.1-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/Fresns-2.x-orange" alt="Fresns">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License">
</p>

## About Fresns

Fresns is a free and open source social network service software, a general-purpose community product designed for cross-platform, and supports flexible and diverse content forms. It conforms to the trend of the times, satisfies a variety of operating scenarios, is more open and easier to re-development.

- Users should read the [installation](https://fresns.org/guide/install.html) and [operating](https://fresns.org/guide/operating.html) documentation.
- Extensions developers should read the [extension documentation](https://fresns.org/extensions/) and [database structure](https://fresns.org/database/).
- For client developers (web or app), please read the [API reference](https://fresns.org/api/) documentation.

## Introduction

Fresns website route engine extension package.

```bash
composer require fresns/web-engine
```

## Path Structure

### Home

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.home |  | Web Home Page |

### Portal

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.portal | /portal/index.blade.php | Portal Home |
| fresns.policies | /portal/policies.blade.php | Policies Info Page |
|  | /portal/private.blade.php | Private Mode Tip Page |

### User

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.user.index | /users/index.blade.php | User Home |
| fresns.user.list | /users/list.blade.php | User List |
| fresns.user.likes | /users/likes.blade.php | My Like Users |
| fresns.user.dislikes | /users/dislikes.blade.php | My Dislike Users |
| fresns.user.following | /users/following.blade.php | My Follow Users |
| fresns.user.blocking | /users/blocking.blade.php | My Block Users |

### Group

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.group.index | /groups/index.blade.php | Group Home |
| fresns.group.list | /groups/list.blade.php | Group List |
| fresns.group.likes | /groups/likes.blade.php | My Like Groups |
| fresns.group.dislikes | /groups/dislikes.blade.php | My Dislike Groups |
| fresns.group.following | /groups/following.blade.php | My Follow Groups |
| fresns.group.blocking | /groups/blocking.blade.php | My Block Groups |
| fresns.group.detail | /groups/detail.blade.php | Group Detail |

### Hashtag

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.hashtag.index | /hashtags/index.blade.php | Hashtag Home |
| fresns.hashtag.list | /hashtags/list.blade.php | Hashtag List |
| fresns.hashtag.likes | /hashtags/likes.blade.php | My Like Hashtags |
| fresns.hashtag.dislikes | /hashtags/dislikes.blade.php | My Dislike Hashtags |
| fresns.hashtag.following | /hashtags/following.blade.php | My Follow Hashtags |
| fresns.hashtag.blocking | /hashtags/blocking.blade.php | My Block Hashtags |
| fresns.hashtag.detail | /hashtags/detail.blade.php | Hashtag Detail |

### Post

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.post.index | /posts/index.blade.php | Post Home |
| fresns.post.list | /posts/list.blade.php | Post List |
| fresns.post.nearby | /posts/nearby.blade.php | Nearby Posts |
| fresns.post.likes | /posts/likes.blade.php | My Like Posts |
| fresns.post.dislikes | /posts/dislikes.blade.php | My Dislike Posts |
| fresns.post.following | /posts/following.blade.php | My Follow Posts |
| fresns.post.blocking | /posts/blocking.blade.php | My Block Posts |
| fresns.post.detail | /posts/detail.blade.php | Post Detail |

### Comment

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.comment.index | /comments/index.blade.php | Comment Home |
| fresns.comment.list | /comments/list.blade.php | Comment List |
| fresns.comment.nearby | /comments/nearby.blade.php | Nearby Comments |
| fresns.comment.likes | /comments/likes.blade.php | My Like Comments |
| fresns.comment.dislikes | /comments/dislikes.blade.php | My Dislike Comments |
| fresns.comment.following | /comments/following.blade.php | My Follow Comments |
| fresns.comment.blocking | /comments/blocking.blade.php | My Block Comments |
| fresns.comment.detail | /comments/detail.blade.php | Comment Detail |

### User Profile

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.profile.posts | /profile/posts.blade.php | They Posts |
| fresns.profile.comments | /profile/comments.blade.php | They Comments |
| fresns.profile.followers.you.follow | /profile/interactions/followers-you-follow.blade.php | Followers you know |
| fresns.profile.likers | /profile/interactions/likers.blade.php | Like they users |
| fresns.profile.dislikers | /profile/interactions/dislikers.blade.php | Dislike they users |
| fresns.profile.followers | /profile/interactions/followers.blade.php | Follow they users |
| fresns.profile.blockers | /profile/interactions/blockers.blade.php | Block they users |
| fresns.profile.likes.users | /profile/likes/users.blade.php | They like users |
| fresns.profile.likes.groups | /profile/likes/groups.blade.php | They like groups |
| fresns.profile.likes.hashtags | /profile/likes/hashtags.blade.php | They like hashtags |
| fresns.profile.likes.posts | /profile/likes/posts.blade.php | They like posts |
| fresns.profile.likes.comments | /profile/likes/comments.blade.php | They like comments |
| fresns.profile.dislikes.users | /profile/dislikes/users.blade.php | They dislike users |
| fresns.profile.dislikes.groups | /profile/dislikes/groups.blade.php | They dislike groups |
| fresns.profile.dislikes.hashtags | /profile/dislikes/hashtags.blade.php | They dislike hashtags |
| fresns.profile.dislikes.posts | /profile/dislikes/posts.blade.php | They dislike posts |
| fresns.profile.dislikes.comments | /profile/dislikes/comments.blade.php | They dislike comments |
| fresns.profile.following.users | /profile/following/users.blade.php | They follow users |
| fresns.profile.following.groups | /profile/following/groups.blade.php | They follow groups |
| fresns.profile.following.hashtags | /profile/following/hashtags.blade.php | They follow hashtags |
| fresns.profile.following.posts | /profile/following/posts.blade.php | They follow posts |
| fresns.profile.following.comments | /profile/following/comments.blade.php | They follow comments |
| fresns.profile.blocking.users | /profile/blocking/users.blade.php | They block users |
| fresns.profile.blocking.groups | /profile/blocking/groups.blade.php | They block groups |
| fresns.profile.blocking.hashtags | /profile/blocking/hashtags.blade.php | They block hashtags |
| fresns.profile.blocking.posts | /profile/blocking/posts.blade.php | They block posts |
| fresns.profile.blocking.comments | /profile/blocking/comments.blade.php | They block comments |

### Search

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.search.index | /search/index.blade.php | Search Home |
| fresns.search.users | /search/users.blade.php | Search Users |
| fresns.search.groups | /search/groups.blade.php | Search Groups |
| fresns.search.hashtags | /search/hashtags.blade.php | Search Hashtags |
| fresns.search.posts | /search/posts.blade.php | Search Posts |
| fresns.search.comments | /search/comments.blade.php | Search Comments |

### Account

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.account.register | /account/register.blade.php | Register |
| fresns.account.login | /account/login.blade.php | Login |
| fresns.account.reset.password | /account/reset-password.blade.php | Reset Password |
| fresns.account.index | /account/index.blade.php | Account Home |
| fresns.account.wallet | /account/wallet.blade.php | Wallet |
| fresns.account.user.extcredits | /account/user-extcredits.blade.php | User Extcredits |
| fresns.account.users | /account/users.blade.php | Users under the account |
| fresns.account.settings | /account/settings.blade.php | Settings |

### Follow

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.follow.all.posts | /follows/all-posts.blade.php | I follow all object posts |
| fresns.follow.user.posts | /follows/user-posts.blade.php | I follow users posts |
| fresns.follow.group.posts | /follows/group-posts.blade.php | I follow groups posts |
| fresns.follow.hashtag.posts | /follows/hashtag-posts.blade.php | I follow hashtags posts |
| fresns.follow.all.comments | /follows/all-comments.blade.php | I follow all object comments |
| fresns.follow.user.comments | /follows/user-comments.blade.php | I follow users comments |
| fresns.follow.group.comments | /follows/group-comments.blade.php | I follow groups comments |
| fresns.follow.hashtag.comments | /follows/hashtag-comments.blade.php | I follow hashtags comments |

### Notifications

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.notifications.index | /notifications/index.blade.php | Notification List |

### Messages

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.messages.index | /messages/index.blade.php | Conversation List |
| fresns.messages.conversation | /messages/conversation.blade.php | Conversation Messages |

### Editor

| Route Name | View File | Description |
| --- | --- | --- |
| fresns.editor.drafts | /editor/drafts.blade.php | Drafts |
| fresns.editor.post | /editor/editor.blade.php | Post Editor |
| fresns.editor.comment | /editor/editor.blade.php | Comment Editor |

## Template Tags

### Route

```php
{{ fs_route(route('route name')) }}

{{ fs_route(route('fresns.user.index')) }}
```

- `route` Real routing in the system
- `fs_route` Handling route as multilingual route

### Config Items

**Get API values**

Get configuration values from API [global configuration information](https://fresns.org/api/global/configs.html)

```php
{{ fs_api_config('item_key') }}
```

**Get database values**

Get configuration values from the database [configs](https://fresns.org/database/systems/configs.html) table

```php
{{ fs_db_config('item_key') }}
```

### Language Packs

- [Language Pack Information](https://fresns.org/database/dictionary/language-pack.html)
- Configuration Path `Panel > Clients > Language Packs`

```php
{{ fs_lang('KeyName') }}
```

- [Code Message](https://fresns.org/api/error-code.html)
- Configuration Path `Panel > Clients > Code Messages`

```php
{{ fs_code_message('code') }}
```

### Account and User

```php
# Is the account logged in
fs_account()->check()
fs_account()->guest()

# Is the user logged in
fs_user()->check()
fs_user()->guest()
```

```php
# Account Parameter
fs_account('key')

# User Parameter
fs_user('key')
```

- The parameter key comes from the API `data` parameter.
- [Account Detail API](https://fresns.org/api/account/detail.html)
- [User Detail API](https://fresns.org/api/user/detail.html)

### Channel Extends

```php
fs_channels()
```

### Global Data

**User Panel**

```php
fs_user_panel('key')
// or
fs_user_panel('key.key')
```

From [the user panel](https://fresns.org/api/user/panel.html) interface `data` parameters

**Groups**

```php
fs_groups('categories') // Group categories
fs_groups('tree') // Tree all group
```

**Home List**

```php
fs_index_list('users') // User home list
fs_index_list('groups') // Group home list
fs_index_list('hashtags') // Hashtag home list
fs_index_list('posts') // Post home list
fs_index_list('comments') // Comment home list
```

The above wrapper function only gets the first page content, if you need to turn the page, then use the following interface.

```php
// 1.Route method
route('fresns.api.index.list', [$type => 'users', 'page' => 2]) // User home list
route('fresns.api.index.list', [$type => 'groups', 'page' => 2]) // Group home list
route('fresns.api.index.list', [$type => 'hashtags', 'page' => 2]) // Hashtag home list
route('fresns.api.index.list', [$type => 'posts', 'page' => 2]) // Post home list
route('fresns.api.index.list', [$type => 'comments', 'page' => 2]) // Comment home list

// 2.Path method
/api/engine/index-list/users?page=2
/api/engine/index-list/groups?page=2
/api/engine/index-list/hashtags?page=2
/api/engine/index-list/posts?page=2
/api/engine/index-list/comments?page=2
```

**List**

```php
fs_list('users') // User list
fs_list('groups') // Group list
fs_list('hashtags') // Hashtag list
fs_list('posts') // Post list
fs_list('comments') // Comment list
```

The above wrapper function only gets the first page content, if you need to turn the page, then use the following interface.

```php
// 1.Route method
route('fresns.api.list', [$type => 'users', 'page' => 2]) // User list
route('fresns.api.list', [$type => 'groups', 'page' => 2]) // Group list
route('fresns.api.list', [$type => 'hashtags', 'page' => 2]) // Hashtag list
route('fresns.api.list', [$type => 'posts', 'page' => 2]) // Post list
route('fresns.api.list', [$type => 'comments', 'page' => 2]) // Comment list

// 2.Path method
/api/engine/list/users?page=2
/api/engine/list/groups?page=2
/api/engine/list/hashtags?page=2
/api/engine/list/posts?page=2
/api/engine/list/comments?page=2
```

**Sticky Post List**

```php
# Global Sticky
fs_sticky_posts()

# Sticky of the group
fs_sticky_posts($gid)
```

**Sticky Comment List**

```php
fs_sticky_comments($pid)
```

**Content Types**

```php
fs_content_types($type) // post or comment
```

**Stickers**

```php
fs_stickers()
```

### Client Options

```html
@mobile
    <p>This is the MOBILE template!</p>
    @include('your-mobile-template')
@endmobile

@tablet
    <p>This is the TABLET template!</p>
    <link rel="stylesheet" href="tablet.css" title="Reduce the page size, load what the user need">
@endtablet

@desktop
    <p>This is the DESKTOP template!</p>
@enddesktop

<!-- Every result key is supported -->
@browser('isBot')
    <p>Bots are identified too :)</p>
@endbrowser
```

### List and Detail

The parameter names are detailed in the API data and [common data structures](https://fresns.org/api/data-structure.html) of the corresponding interfaces.
