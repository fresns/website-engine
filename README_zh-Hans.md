<p align="center"><a href="https://fresns.org" target="_blank"><img src="https://files.fresns.org/images/logo.png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/Platform-Web-blue" alt="Platform">
<img src="https://img.shields.io/badge/PHP-%5E8.1-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/Fresns-2.x-orange" alt="Fresns">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License">
</p>

[English](README.md)

## 介绍

Fresns 是一款免费开源的社交网络服务软件，专为跨平台而打造的通用型社区产品，支持灵活多样的内容形态，可以满足多种运营场景，符合时代潮流，更开放且更易于二次开发。

- [点击了解产品 16 个功能特色](https://zh-hans.fresns.org/guide/features.html)
- 使用者请阅读[安装教程](https://zh-hans.fresns.org/guide/install.html)和[运营文档](https://zh-hans.fresns.org/operating/)；
- 扩展插件开发者请阅读[扩展文档](https://zh-hans.fresns.org/extensions/)和[数据字典](https://zh-hans.fresns.org/database/)；
- 客户端开发者（网站端、小程序、App）请阅读 [API 文档](https://zh-hans.fresns.org/api/)。

## 仓库介绍

Fresns 网站端路由引擎扩展包。

```bash
composer require fresns/web-engine
```

案例参考: [https://github.com/fresns/website](https://github.com/fresns/website)

## 路径结构

### 首页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.home |  | 首页 |

### 门户页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.portal | portal/index.blade.php | 门户主页 |
| fresns.policies | portal/policies.blade.php | 隐私权和条款信息页 |
|  | portal/private.blade.php | 私有模式提示页 |

### 用户页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.user.index | users/index.blade.php | 用户主页 |
| fresns.user.list | users/list.blade.php | 用户列表页 |
| fresns.user.likes | users/likes.blade.php | 我点赞的用户 |
| fresns.user.dislikes | users/dislikes.blade.php | 我点踩的用户 |
| fresns.user.following | users/following.blade.php | 我关注的用户 |
| fresns.user.blocking | users/blocking.blade.php | 我屏蔽的用户 |

### 小组页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.group.index | groups/index.blade.php | 小组主页 |
| fresns.group.list | groups/list.blade.php | 小组列表页 |
| fresns.group.likes | groups/likes.blade.php | 我点赞的小组 |
| fresns.group.dislikes | groups/dislikes.blade.php | 我点踩的小组 |
| fresns.group.following | groups/following.blade.php | 我关注的小组 |
| fresns.group.blocking | groups/blocking.blade.php | 我屏蔽的小组 |
| fresns.group.detail | groups/detail.blade.php | 小组详情页 |

### 话题页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.hashtag.index | hashtags/index.blade.php | 话题主页 |
| fresns.hashtag.list | hashtags/list.blade.php | 话题列表页 |
| fresns.hashtag.likes | hashtags/likes.blade.php | 我点赞的话题 |
| fresns.hashtag.dislikes | hashtags/dislikes.blade.php | 我点踩的话题 |
| fresns.hashtag.following | hashtags/following.blade.php | 我关注的话题 |
| fresns.hashtag.blocking | hashtags/blocking.blade.php | 我屏蔽的话题 |
| fresns.hashtag.detail | hashtags/detail.blade.php | 话题详情页 |

### 帖子页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.post.index | posts/index.blade.php | 帖子主页 |
| fresns.post.list | posts/list.blade.php | 帖子列表页 |
| fresns.post.nearby | posts/nearby.blade.php | 附近的帖子 |
| fresns.post.likes | posts/likes.blade.php | 我点赞的帖子 |
| fresns.post.dislikes | posts/dislikes.blade.php | 我点踩的帖子 |
| fresns.post.following | posts/following.blade.php | 我关注的帖子 |
| fresns.post.blocking | posts/blocking.blade.php | 我屏蔽的帖子 |
| fresns.post.detail | posts/detail.blade.php | 帖子详情页 |

### 评论页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.comment.index | comments/index.blade.php | 评论主页 |
| fresns.comment.list | comments/list.blade.php | 评论列表页 |
| fresns.comment.nearby | comments/nearby.blade.php | 附近的评论 |
| fresns.comment.likes | comments/likes.blade.php | 我点赞的评论 |
| fresns.comment.dislikes | comments/dislikes.blade.php | 我点踩的评论 |
| fresns.comment.following | comments/following.blade.php | 我关注的评论 |
| fresns.comment.blocking | comments/blocking.blade.php | 我屏蔽的评论 |
| fresns.comment.detail | comments/detail.blade.php | 评论详情页 |

### 用户详情页

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.profile.posts | profile/posts.blade.php | 他帖子列表 |
| fresns.profile.comments | profile/comments.blade.php | 他评论列表 |
| fresns.profile.followers.you.follow | profile/interactions/followers-you-follow.blade.php | 你认识的关注者 |
| fresns.profile.likers | profile/interactions/likers.blade.php | 点赞他的用户 |
| fresns.profile.dislikers | profile/interactions/dislikers.blade.php | 点踩他的用户 |
| fresns.profile.followers | profile/interactions/followers.blade.php | 关注他的用户 |
| fresns.profile.blockers | profile/interactions/blockers.blade.php | 屏蔽他的用户 |
| fresns.profile.likes.users | profile/likes/users.blade.php | 他点赞的用户 |
| fresns.profile.likes.groups | profile/likes/groups.blade.php | 他点赞的小组 |
| fresns.profile.likes.hashtags | profile/likes/hashtags.blade.php | 他点赞的话题 |
| fresns.profile.likes.posts | profile/likes/posts.blade.php | 他点赞的帖子 |
| fresns.profile.likes.comments | profile/likes/comments.blade.php | 他点赞的评论 |
| fresns.profile.dislikes.users | profile/dislikes/users.blade.php | 他点踩的用户 |
| fresns.profile.dislikes.groups | profile/dislikes/groups.blade.php | 他点踩的小组 |
| fresns.profile.dislikes.hashtags | profile/dislikes/hashtags.blade.php | 他点踩的话题 |
| fresns.profile.dislikes.posts | profile/dislikes/posts.blade.php | 他点踩的帖子 |
| fresns.profile.dislikes.comments | profile/dislikes/comments.blade.php | 他点踩的评论 |
| fresns.profile.following.users | profile/following/users.blade.php | 他关注的用户 |
| fresns.profile.following.groups | profile/following/groups.blade.php | 他关注的小组 |
| fresns.profile.following.hashtags | profile/following/hashtags.blade.php | 他关注的话题 |
| fresns.profile.following.posts | profile/following/posts.blade.php | 他关注的帖子 |
| fresns.profile.following.comments | profile/following/comments.blade.php | 他关注的评论 |
| fresns.profile.blocking.users | profile/blocking/users.blade.php | 他屏蔽的用户 |
| fresns.profile.blocking.groups | profile/blocking/groups.blade.php | 他屏蔽的小组 |
| fresns.profile.blocking.hashtags | profile/blocking/hashtags.blade.php | 他屏蔽的话题 |
| fresns.profile.blocking.posts | profile/blocking/posts.blade.php | 他屏蔽的帖子 |
| fresns.profile.blocking.comments | profile/blocking/comments.blade.php | 他屏蔽的评论 |

### 搜索

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.search.index | search/index.blade.php | 搜索主页 |
| fresns.search.users | search/users.blade.php | 搜索用户列表 |
| fresns.search.groups | search/groups.blade.php | 搜索小组列表 |
| fresns.search.hashtags | search/hashtags.blade.php | 搜索话题列表 |
| fresns.search.posts | search/posts.blade.php | 搜索帖子列表 |
| fresns.search.comments | search/comments.blade.php | 搜索评论列表 |

### 账号

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.account.register | account/register.blade.php | 注册 |
| fresns.account.login | account/login.blade.php | 登录 |
| fresns.account.reset.password | account/reset-password.blade.php | 重置密码 |
| fresns.account.index | account/index.blade.php | 账号中心 |
| fresns.account.wallet | account/wallet.blade.php | 账号钱包 |
| fresns.account.user.extcredits | account/user-extcredits.blade.php | 用户扩展分值 |
| fresns.account.users | account/users.blade.php | 账号名下用户 |
| fresns.account.settings | account/settings.blade.php | 账号设置 |

### 关注

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.follow.all.posts | follows/all-posts.blade.php | 全部关注对象的帖子 |
| fresns.follow.user.posts | follows/user-posts.blade.php | 关注用户的帖子 |
| fresns.follow.group.posts | follows/group-posts.blade.php | 关注小组的帖子 |
| fresns.follow.hashtag.posts | follows/hashtag-posts.blade.php | 关注话题的帖子 |
| fresns.follow.all.comments | follows/all-comments.blade.php | 全部关注对象的评论 |
| fresns.follow.user.comments | follows/user-comments.blade.php | 关注用户的评论 |
| fresns.follow.group.comments | follows/group-comments.blade.php | 关注小组的评论 |
| fresns.follow.hashtag.comments | follows/hashtag-comments.blade.php | 关注话题的评论 |

### 通知

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.notifications.index | notifications/index.blade.php | 通知列表 |

### 私信

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.messages.index | messages/index.blade.php | 私信列表 |
| fresns.messages.conversation | messages/conversation.blade.php | 私信消息 |

### 编辑器

| 路由名 | 模板文件 | 介绍 |
| --- | --- | --- |
| fresns.editor.drafts | editor/drafts.blade.php | 草稿箱 |
| fresns.editor.post | editor/editor.blade.php | 帖子编辑器 |
| fresns.editor.comment | editor/editor.blade.php | 评论编辑器 |

## 视图标签

### 路由

```php
{{ fs_route(route('路由名')) }}

{{ fs_route(route('fresns.user.index')) }}
```

- `route` 系统中实际的路由
- `fs_route` 处理路由为多语言路由

### 配置值

**接口配置**

从 API [全局配置信息](https://zh-hans.fresns.org/api/global/configs.html)获取配置值

```php
{{ fs_api_config('配置键名') }}
```

**数据库配置**

从本地数据库 [configs](https://zh-hans.fresns.org/database/systems/configs.html) 表获取配置值

```php
{{ fs_db_config('配置键名') }}
```

### 语言配置

- [语言包信息](https://zh-hans.fresns.org/database/dictionary/language-pack.html)
- 配置位置 `控制面板 > 客户端 > 语言包配置`

```php
{{ fs_lang('语言键名') }}
```

- [状态码信息](https://zh-hans.fresns.org/api/error-code.html)
- 配置位置 `控制面板 > 客户端 > 状态码配置`

```php
{{ fs_code_message('编号') }}
```

### 账号和用户参数

```php
# 是否登录账号
fs_account()->check()
fs_account()->guest()

# 是否登录用户
fs_user()->check()
fs_user()->guest()
```

```php
# 账号参数
fs_account('参数名')

# 用户参数
fs_user('参数名')
```

- 参数名来自 API `data` 参数。
- [账号 API](https://zh-hans.fresns.org/api/account/detail.html)
- [用户 API](https://zh-hans.fresns.org/api/user/detail.html)

### 频道扩展

```php
fs_channels()
```

### 全局数据

**用户面板**

```php
fs_user_panel('key')
// 或者
fs_user_panel('key.key')
```

- 参数来自[用户面板](https://zh-hans.fresns.org/api/user/panel.html)接口 `data`

**小组**

```php
fs_groups('categories') // 小组分类
fs_groups('tree') // 树结构全部小组
```

**首页列表**

```php
fs_index_list('users') // 用户首页列表
fs_index_list('groups') // 小组首页列表
fs_index_list('hashtags') // 话题首页列表
fs_index_list('posts') // 帖子首页列表
fs_index_list('comments') // 评论首页列表
```

以上封装函数仅获取第一页内容，如需翻页，则使用以下接口。

```php
// 1.路由方式
route('fresns.api.index.list', [$type => 'users', 'page' => 2]) // 用户首页列表
route('fresns.api.index.list', [$type => 'groups', 'page' => 2]) // 小组首页列表
route('fresns.api.index.list', [$type => 'hashtags', 'page' => 2]) // 话题首页列表
route('fresns.api.index.list', [$type => 'posts', 'page' => 2]) // 帖子首页列表
route('fresns.api.index.list', [$type => 'comments', 'page' => 2]) // 评论首页列表

// 2.路径方式
/api/web-engine/index-list/users?page=2
/api/web-engine/index-list/groups?page=2
/api/web-engine/index-list/hashtags?page=2
/api/web-engine/index-list/posts?page=2
/api/web-engine/index-list/comments?page=2
```

**列表**

```php
fs_list('users') // 用户列表
fs_list('groups') // 小组列表
fs_list('hashtags') // 话题列表
fs_list('posts') // 帖子列表
fs_list('comments') // 评论列表
```

以上封装函数仅获取第一页内容，如需翻页，则使用以下接口。

```php
// 1.路由方式
route('fresns.api.list', [$type => 'users', 'page' => 2]) // 用户首页列表
route('fresns.api.list', [$type => 'groups', 'page' => 2]) // 小组首页列表
route('fresns.api.list', [$type => 'hashtags', 'page' => 2]) // 话题首页列表
route('fresns.api.list', [$type => 'posts', 'page' => 2]) // 帖子首页列表
route('fresns.api.list', [$type => 'comments', 'page' => 2]) // 评论首页列表

// 2.路径方式
/api/web-engine/list/users?page=2
/api/web-engine/list/groups?page=2
/api/web-engine/list/hashtags?page=2
/api/web-engine/list/posts?page=2
/api/web-engine/list/comments?page=2
```

**置顶帖子**

```php
# 全局置顶
fs_sticky_posts()

# 指定小组的置顶
fs_sticky_posts($gid)
```

**置顶评论**

```php
fs_sticky_comments($pid)
```

**内容类型**

```php
fs_content_types($type) // post or comment
```

**表情**

```php
fs_stickers()
```

### 客户端判断

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

### 列表和详情页

参数名详见对应接口的 API 数据和[通用数据结构](https://zh-hans.fresns.org/api/data-structure.html)。
