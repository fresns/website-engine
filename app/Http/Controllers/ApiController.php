<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Utilities\ConfigUtility;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Fresns\WebEngine\Helpers\QueryHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    // url authorization
    public function urlAuthorization()
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        $authorization = urlencode(base64_encode(json_encode($headers)));

        return \response()->json([
            'code' => 0,
            'message' => 'ok',
            'data' => [
                'authorization' => $authorization,
            ],
        ]);
    }

    // plugin callback
    public function pluginCallback(Request $request): JsonResponse
    {
        $result = ApiHelper::make()->get('/api/fresns/v1/common/callback', [
            'query' => $request->all(),
        ]);

        return Response::json($result);
    }

    // index list
    public function indexList(string $type, Request $request): JsonResponse
    {
        $queryType = match ($type) {
            'users' => QueryHelper::TYPE_USER,
            'groups' => QueryHelper::TYPE_GROUP,
            'hashtags' => QueryHelper::TYPE_HASHTAG,
            'posts' => QueryHelper::TYPE_POST,
            'comments' => QueryHelper::TYPE_COMMENT,
        };

        $query = QueryHelper::convertOptionToRequestParam($queryType, $request->all());

        $result = ApiHelper::make()->get("/api/fresns/v1/{$queryType}/list", [
            'query' => $query,
        ]);

        return Response::json($result);
    }

    // list
    public function list(string $type, Request $request): JsonResponse
    {
        $queryType = match ($type) {
            'users' => QueryHelper::TYPE_USER_LIST,
            'groups' => QueryHelper::TYPE_GROUP_LIST,
            'hashtags' => QueryHelper::TYPE_HASHTAG_LIST,
            'posts' => QueryHelper::TYPE_POST_LIST,
            'comments' => QueryHelper::TYPE_COMMENT_LIST,
        };

        $query = QueryHelper::convertOptionToRequestParam($queryType, $request->all());

        $type = match ($type) {
            'users' => 'user',
            'groups' => 'group',
            'hashtags' => 'hashtag',
            'posts' => 'post',
            'comments' => 'comment',
        };

        $result = ApiHelper::make()->get("/api/fresns/v1/{$type}/list", [
            'query' => $query,
        ]);

        return Response::json($result);
    }

    // sub groups
    public function subGroups(string $gid): JsonResponse
    {
        $response = ApiHelper::make()->get('/api/fresns/v1/group/list', [
            'query' => [
                'gid' => $gid,
                'pageSize' => request()->get('pageSize'),
                'page' => request()->get('page'),
            ],
        ]);

        return Response::json(data_get($response, 'data', []) ?? []);
    }

    // get input tips
    public function getInputTips(Request $request): JsonResponse
    {
        if ($request->get('type') && $request->get('key')) {
            $result = ApiHelper::make()->get('/api/fresns/v1/common/input-tips', [
                'query' => [
                    'type' => $request->get('type'),
                    'key' => $request->get('key'),
                ],
            ]);

            if ($result['code'] !== 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            return Response::json($result['data']);
        }

        return Response::json();
    }

    // get archives
    public function getArchives(Request $request, string $type)
    {
        $response = ApiHelper::make()->get("/api/fresns/v1/global/{$type}/archives", [
            'query' => [
                'fskey' => $request->get('fskey'),
            ],
        ]);

        return \response()->json($response);
    }

    // send verify code
    public function sendVerifyCode(Request $request)
    {
        if (\request('useType') == 4) {
            \request()->offsetSet('account', 'fresns_random_string:'.uniqid());
        }

        if (empty(\request('countryCode'))) {
            \request()->offsetSet('countryCode', fs_account()->get('detail.countryCode'));
        }

        if (empty(\request('phone'))) {
            \request()->offsetSet('phone', fs_account()->get('detail.phone'));
        }

        $response = ApiHelper::make()->post('/api/fresns/v1/common/send-verify-code', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response);
    }

    // upload file
    public function uploadFile(Request $request)
    {
        $multipart = [];

        foreach (\request()->file() as $name => $file) {
            if ($file instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $multipart[] = [
                    'name' => $name,
                    'filename' => $file->getClientOriginalName(),
                    'contents' => $file->getContent(),
                    'headers' => ['Content-Type' => $file->getClientMimeType()],
                ];
            }
        }

        foreach (\request()->post() as $name => $contents) {
            $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
            $multipart[] = compact('name', 'contents', 'headers');
        }

        $response = ApiHelper::make()->post('/api/fresns/v1/common/upload-file', [
            'multipart' => $multipart,
        ]);

        if ($request->post('tableName') == 'users') {
            DataHelper::cacheForgetAccountAndUser();
        }

        return \response()->json($response);
    }

    // account register
    public function accountRegister(Request $request)
    {
        $result = ApiHelper::make()->post('/api/fresns/v1/account/register', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'verifyCode' => $request->verifyCode,
                'password' => $request->password,
                'nickname' => $request->nickname,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        if ($result['code'] != 0) {
            return \response()->json($result);
        }

        // api data
        $data = $result['data'];
        $user = $data['detail']['users'][0];

        // cookie key name
        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
        $fresnsAid = "{$cookiePrefix}aid";
        $fresnsAidToken = "{$cookiePrefix}aid_token";
        $fresnsUid = "{$cookiePrefix}uid";
        $fresnsUidToken = "{$cookiePrefix}uid_token";

        // aid and token put to cache
        $cacheKey = 'fresns_web_'.Cookie::get("{$cookiePrefix}ulid");
        if ($cacheKey) {
            $cacheTags = ['fresnsWeb', 'fresnsWebAccountTokens'];
            $cacheData = [
                'aid' => data_get($result, 'data.sessionToken.aid'),
                'aidToken' => data_get($result, 'data.sessionToken.token'),
            ];
            CacheHelper::put($cacheData, $cacheKey, $cacheTags, 3, now()->addMinutes(3));
        }

        // aid and token put to cookie
        $accountExpiredHours = data_get($result, 'data.sessionToken.expiredHours') ?? 8760;
        $accountTokenMinutes = $accountExpiredHours * 60;

        Cookie::queue($fresnsAid, $data['sessionToken']['aid'], $accountTokenMinutes);
        Cookie::queue($fresnsAidToken, $data['sessionToken']['token'], $accountTokenMinutes);

        \request()->offsetSet($fresnsAid, $data['sessionToken']['aid']);
        \request()->offsetSet($fresnsAidToken, $data['sessionToken']['token']);

        // forget cache
        DataHelper::cacheForgetAccountAndUser();

        // user login
        $userResult = ApiHelper::make()->post('/api/fresns/v1/user/auth', [
            'json' => [
                'uidOrUsername' => strval($user['uid']),
                'password' => null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        $userExpiredHours = data_get($userResult, 'data.sessionToken.expiredHours') ?? 8760;
        $userTokenMinutes = $userExpiredHours * 60;

        Cookie::queue($fresnsUid, data_get($userResult, 'data.sessionToken.uid'), $userTokenMinutes);
        Cookie::queue($fresnsUidToken, data_get($userResult, 'data.sessionToken.token'), $userTokenMinutes);

        if ($userResult['code'] != 0) {
            return \response()->json($userResult);
        }

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        if ($request->wantsJson()) {
            return \response()->json([
                'code' => 0,
                'message' => data_get($userResult, 'message', 'success'),
                'data' => [
                    'redirectURL' => $redirectURL,
                ],
            ]);
        }

        return redirect()->intended($redirectURL);
    }

    // account login
    public function accountLogin(Request $request)
    {
        $result = ApiHelper::make()->post('/api/fresns/v1/account/login', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'password' => $request->password ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        if ($result['code'] != 0) {
            return back()->with([
                'code' => $result['code'],
                'failure' => $result['message'],
            ]);
        }

        // api data
        $data = $result['data'];
        $redirectURL = $request->redirectURL;

        // cookie key name
        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
        $fresnsAid = "{$cookiePrefix}aid";
        $fresnsAidToken = "{$cookiePrefix}aid_token";
        $fresnsUid = "{$cookiePrefix}uid";
        $fresnsUidToken = "{$cookiePrefix}uid_token";

        // aid and token put to cache
        $cacheKey = 'fresns_web_'.Cookie::get("{$cookiePrefix}ulid");
        if ($cacheKey) {
            $cacheTags = ['fresnsWeb', 'fresnsWebAccountTokens'];
            $cacheData = [
                'aid' => data_get($result, 'data.sessionToken.aid'),
                'aidToken' => data_get($result, 'data.sessionToken.token'),
            ];
            CacheHelper::put($cacheData, $cacheKey, $cacheTags, 3, now()->addMinutes(3));
        }

        // aid and token put to cookie
        $accountExpiredHours = data_get($result, 'data.sessionToken.expiredHours') ?? 8760;
        $accountTokenMinutes = $accountExpiredHours * 60;

        $aid = data_get($result, 'data.detail.aid');
        CacheHelper::forgetFresnsMultilingual("fresns_web_account_{$aid}", 'fresnsWeb');

        Cookie::queue($fresnsAid, $aid, $accountTokenMinutes);
        Cookie::queue($fresnsAidToken, data_get($result, 'data.sessionToken.token'), $accountTokenMinutes);

        // Number of users under the account
        $users = data_get($result, 'data.detail.users', []) ?? [];
        $userCount = count($users);

        // Only one user and no password
        if ($userCount == 1) {
            $user = $users[0];

            if ($user['hasPassword']) {
                // User has password
                // user-auth.blade.php

                if ($request->wantsJson()) {
                    return \response()->json([
                        'code' => 0,
                        'message' => data_get($result, 'message', 'success'),
                        'data' => [
                            'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                        ],
                    ]);
                }

                return redirect()->intended(fs_route(route('fresns.home')));
            } else {
                // User does not have a password
                \request()->offsetSet($fresnsAid, $data['sessionToken']['aid']);
                \request()->offsetSet($fresnsAidToken, $data['sessionToken']['token']);

                DataHelper::cacheForgetAccountAndUser();

                $userResult = ApiHelper::make()->post('/api/fresns/v1/user/auth', [
                    'json' => [
                        'uidOrUsername' => strval($user['uid']),
                        'password' => null,
                        'deviceToken' => $request->deviceToken ?? null,
                    ],
                ]);

                if ($userResult['code'] != 0) {
                    return back()->with([
                        'code' => $userResult['code'],
                        'failure' => $userResult['message'],
                    ]);
                }

                $userExpiredHours = data_get($userResult, 'data.sessionToken.expiredHours') ?? 8760;
                $userTokenMinutes = $userExpiredHours * 60;

                Cookie::queue($fresnsUid, data_get($userResult, 'data.sessionToken.uid'), $userTokenMinutes);
                Cookie::queue($fresnsUidToken, data_get($userResult, 'data.sessionToken.token'), $userTokenMinutes);

                if ($request->wantsJson()) {
                    return \response()->json([
                        'code' => 0,
                        'message' => data_get($userResult, 'message', 'success'),
                        'data' => [
                            'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                        ],
                    ]);
                }

                return redirect()->intended($redirectURL ?? fs_route(route('fresns.home')));
            }
        } elseif ($userCount > 1) {
            // There are more than one user
            // user-auth.blade.php

            if ($request->wantsJson()) {
                return \response()->json([
                    'code' => 0,
                    'message' => data_get($result, 'message', 'success'),
                    'data' => [
                        'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                    ],
                ]);
            }

            return redirect()->intended($redirectURL ?? fs_route(route('fresns.home')));
        }
    }

    // account connect login.
    public function accountConnectLogin(Request $request)
    {
        // api data
        $accountCode = $request->apiData['code'] ?? 30004;
        $accountMessage = $request->apiData['message'] ?? 0;
        $accountData = $request->apiData['data'] ?? null;

        $redirectURL = $request->redirectURL;

        // check api code
        if ($accountCode != 0 || empty(data_get($accountData, 'sessionToken.aid')) || empty(data_get($accountData, 'sessionToken.token'))) {
            return back()->with([
                'code' => $accountCode,
                'failure' => $accountMessage,
            ]);
        }

        // cookie key name
        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
        $fresnsAid = "{$cookiePrefix}aid";
        $fresnsAidToken = "{$cookiePrefix}aid_token";
        $fresnsUid = "{$cookiePrefix}uid";
        $fresnsUidToken = "{$cookiePrefix}uid_token";

        // aid and token put to cache
        $cacheKey = 'fresns_web_'.Cookie::get("{$cookiePrefix}ulid");
        if ($cacheKey) {
            $cacheTags = ['fresnsWeb', 'fresnsWebAccountTokens'];
            $cacheData = [
                'aid' => data_get($accountData, 'sessionToken.aid'),
                'aidToken' => data_get($accountData, 'sessionToken.token'),
            ];
            CacheHelper::put($cacheData, $cacheKey, $cacheTags, 3, now()->addMinutes(3));
        }

        // aid and token put to cookie
        $accountExpiredHours = data_get($accountData, 'sessionToken.expiredHours') ?? 8760;
        $accountTokenMinutes = $accountExpiredHours * 60;

        Cookie::queue($fresnsAid, data_get($accountData, 'sessionToken.aid'), $accountTokenMinutes);
        Cookie::queue($fresnsAidToken, data_get($accountData, 'sessionToken.token'), $accountTokenMinutes);

        // Number of users under the account
        $users = data_get($accountData, 'detail.users', []) ?? [];
        $userCount = count($users);

        // Only one user and no password
        if ($userCount == 1) {
            $user = $users[0];

            if ($user['hasPassword']) {
                // User has password
                // user-auth.blade.php

                if ($request->wantsJson()) {
                    return \response()->json([
                        'code' => 0,
                        'message' => $accountMessage,
                        'data' => [
                            'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                        ],
                    ]);
                }

                return redirect()->intended(fs_route(route('fresns.home')));
            } else {
                // User does not have a password
                \request()->offsetSet($fresnsAid, $accountData['sessionToken']['aid']);
                \request()->offsetSet($fresnsAidToken, $accountData['sessionToken']['token']);

                DataHelper::cacheForgetAccountAndUser();

                $userResult = ApiHelper::make()->post('/api/fresns/v1/user/auth', [
                    'json' => [
                        'uidOrUsername' => strval($user['uid']),
                        'password' => null,
                        'deviceToken' => $request->deviceToken ?? null,
                    ],
                ]);

                if ($userResult['code'] != 0) {
                    return back()->with([
                        'code' => $userResult['code'],
                        'failure' => $userResult['message'],
                    ]);
                }

                $userExpiredHours = data_get($userResult, 'data.sessionToken.expiredHours') ?? 8760;
                $userTokenMinutes = $userExpiredHours * 60;

                Cookie::queue($fresnsUid, data_get($userResult, 'data.sessionToken.uid'), $userTokenMinutes);
                Cookie::queue($fresnsUidToken, data_get($userResult, 'data.sessionToken.token'), $userTokenMinutes);

                if ($request->wantsJson()) {
                    return \response()->json([
                        'code' => 0,
                        'message' => data_get($userResult, 'message', 'success'),
                        'data' => [
                            'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                        ],
                    ]);
                }

                return redirect()->intended($redirectURL ?? fs_route(route('fresns.home')));
            }
        } elseif ($userCount > 1) {
            // There are more than one user
            // user-auth.blade.php

            if ($request->wantsJson()) {
                return \response()->json([
                    'code' => 0,
                    'message' => $accountMessage,
                    'data' => [
                        'redirectURL' => $redirectURL ?? fs_route(route('fresns.home')),
                    ],
                ]);
            }

            return redirect()->intended($redirectURL ?? fs_route(route('fresns.home')));
        }
    }

    // account reset password
    public function accountResetPassword(Request $request)
    {
        if (\request('password') !== \request('password_confirmation')) {
            return \response()->json([
                'code' => 34104,
                'message' => fs_lang('passwordAgainError'),
                'data' => null,
            ]);
        }

        $response = ApiHelper::make()->put('/api/fresns/v1/account/reset-password', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'newPassword' => $request->password ?? null,
            ],
        ]);

        return \response()->json($response);
    }

    // account verify identity
    public function accountVerifyIdentity(Request $request)
    {
        $response = ApiHelper::make()->post('/api/fresns/v1/account/verify-identity', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response);
    }

    // account edit
    public function accountEdit(Request $request)
    {
        if ($request->codeType == 'password') {
            \request()->offsetSet('codeType', '');
            \request()->offsetSet('verifyCode', '');
        }

        $response = ApiHelper::make()->put('/api/fresns/v1/account/edit', [
            'json' => $request->all(),
        ]);

        DataHelper::cacheForgetAccountAndUser();

        return \response()->json($response);
    }

    // account apply delete
    public function accountApplyDelete(Request $request)
    {
        $result = ApiHelper::make()->post('/api/fresns/v1/account/apply-delete', [
            'json' => [
                'password' => $request->password ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'codeType' => $request->codeType ?? null,
            ],
        ]);

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        return \response()->json($result);
    }

    // account recall delete
    public function accountRecallDelete(Request $request)
    {
        $result = ApiHelper::make()->post('/api/fresns/v1/account/recall-delete');

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        return \response()->json($result);
    }

    // user auth
    public function userAuth(Request $request)
    {
        $result = ApiHelper::make()->post('/api/fresns/v1/user/auth', [
            'json' => [
                'uidOrUsername' => $request->uidOrUsername,
                'password' => $request->password ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';

        $userExpiredHours = data_get($result, 'data.sessionToken.expiredHours') ?? 8760;
        $userTokenMinutes = $userExpiredHours * 60;

        $uid = data_get($result, 'data.detail.uid');
        CacheHelper::forgetFresnsMultilingual("fresns_web_user_{$uid}", 'fresnsWeb');

        Cookie::queue("{$cookiePrefix}uid", $uid, $userTokenMinutes);
        Cookie::queue("{$cookiePrefix}uid_token", data_get($result, 'data.sessionToken.token'), $userTokenMinutes);

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        if ($request->wantsJson()) {
            return \response()->json([
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'redirectURL' => $redirectURL,
                ],
            ]);
        }

        return redirect()->intended($redirectURL);
    }

    // user edit
    public function userEdit()
    {
        $response = ApiHelper::make()->put('/api/fresns/v1/user/edit', [
            'json' => \request()->all(),
        ]);

        DataHelper::cacheForgetAccountAndUser();

        return \response()->json($response);
    }

    // user mark
    public function userMark(Request $request)
    {
        $response = ApiHelper::make()->post('/api/fresns/v1/user/mark', [
            'json' => $request->all(),
        ]);

        if ($request->get('forgetCache')) {
            $uid = fs_user('detail.uid');
            CacheHelper::forgetFresnsMultilingual("fresns_web_group_categories_by_{$uid}", 'fresnsWeb');
            CacheHelper::forgetFresnsMultilingual("fresns_web_group_tree_by_{$uid}", 'fresnsWeb');
            CacheHelper::forgetFresnsMultilingual("fresns_web_users_index_list_by_{$uid}", 'fresnsWeb');
            CacheHelper::forgetFresnsMultilingual("fresns_web_users_list_by_{$uid}", 'fresnsWeb');
        }

        return \response()->json($response);
    }

    // message mark-as-read
    public function messageMarkAsRead(Request $request, string $type)
    {
        $response = ApiHelper::make()->put("/api/fresns/v1/{$type}/mark-as-read", [
            'json' => \request()->all(),
        ]);

        $uid = fs_user('detail.uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_user_panel_{$uid}", 'fresnsWeb');

        return \response()->json($response);
    }

    // message delete
    public function messageDelete(Request $request, string $type)
    {
        $response = ApiHelper::make()->delete("/api/fresns/v1/{$type}/delete", [
            'json' => \request()->all(),
        ]);

        $uid = fs_user('detail.uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_user_panel_{$uid}", 'fresnsWeb');

        return \response()->json($response);
    }

    // send message
    public function messageSend(Request $request)
    {
        $response = ApiHelper::make()->post('/api/fresns/v1/conversation/send-message', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response);
    }

    // messages
    public function messages(Request $request, $conversationId)
    {
        $response = ApiHelper::make()->get("/api/fresns/v1/conversation/{$conversationId}/messages", [
            'query' => [
                'orderDirection' => $request->get('orderDirection'),
                'pageListDirection' => $request->get('pageListDirection'),
                'pageSize' => $request->get('pageSize'),
                'page' => $request->get('page'),
            ],
        ]);

        return \response()->json($response);
    }

    // content download file
    public function contentFileLink(Request $request, $fid)
    {
        $response = ApiHelper::make()->get("/api/fresns/v1/common/file/{$fid}/link", [
            'query' => [
                'type' => $request->get('type'),
                'fsid' => $request->get('fsid'),
            ],
        ]);

        return \response()->json($response);
    }

    // content download users
    public function contentFileUsers(Request $request, $fid)
    {
        $response = ApiHelper::make()->get("/api/fresns/v1/common/file/{$fid}/users", [
            'query' => [
                'pageSize' => $request->get('pageSize') ?? 30,
                'page' => $request->get('page') ?? 1,
            ],
        ]);

        return \response()->json($response);
    }

    // content delete
    public function contentDelete(string $type, string $fsid)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->delete("/api/fresns/v1/{$type}/{$fsid}");

        return \response()->json($response);
    }

    // quick publish
    public function editorQuickPublish(Request $request, string $type)
    {
        $validator = Validator::make($request->post(),
            [
                'content' => 'required',
                'postGid' => ($type === 'post' && fs_config('post_editor_group_required')) ? 'required' : 'nullable',
                'postTitle' => ($type === 'post' && fs_config('post_editor_title_required')) ? 'required' : 'nullable',
                'commentPid' => ($type === 'comment') ? 'required' : 'nullable',
            ], [
                'postGid.required' => ConfigUtility::getCodeMessage(38208, 'Fresns', current_lang_tag()),
                'postTitle.required' => ConfigUtility::getCodeMessage(38202, 'Fresns', current_lang_tag()),
                'commentPid.required' => ConfigUtility::getCodeMessage(37300, 'Fresns', current_lang_tag()),
            ]
        );

        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()[0], 'code' => 400]);
        }

        $multipart = [
            [
                'name' => 'postQuotePid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('postQuotePid'),
            ],
            [
                'name' => 'postGid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('postGid'),
            ],
            [
                'name' => 'postTitle',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('postTitle'),
            ],
            [
                'name' => 'content',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('content'),
            ],
            [
                'name' => 'isMarkdown',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => (bool) $request->post('isMarkdown', false),
            ],
            [
                'name' => 'isAnonymous',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => (bool) $request->post('isAnonymous', false),
            ],
            [
                'name' => 'commentPid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('commentPid'),
            ],
            [
                'name' => 'commentCid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('commentCid'),
            ],
        ];
        if ($request->file('image')) {
            $multipart[] = [
                'name' => 'image',
                'filename' => $request->file('image')->getClientOriginalName(),
                'contents' => $request->file('image')->getContent(),
                'headers' => ['Content-Type' => $request->file('image')->getClientMimeType()],
            ];
        }

        $result = ApiHelper::make()->post("/api/fresns/v1/editor/{$type}/quick-publish", [
            'multipart' => array_filter($multipart, fn ($val) => isset($val['contents'])),
        ]);

        DataHelper::cacheForgetAccountAndUser();

        return Response::json($result);
    }

    // editor upload file
    public function editorUploadFile(Request $request)
    {
        $multipart = [
            [
                'name' => 'usageType',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('usageType'),
            ],
            [
                'name' => 'tableName',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableName'),
            ],
            [
                'name' => 'tableColumn',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableColumn', 'id'),
            ],
            [
                'name' => 'tableId',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableId'),
            ],
            [
                'name' => 'uploadMode',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('uploadMode'),
            ],
            [
                'name' => 'type',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('type'),
            ],
        ];

        if (! $request->file('files')) {
            return Response::json([]);
        }

        $postAsyncs = [];
        foreach ($request->file('files') as $key => $file) {
            $params = $multipart;
            $params[] = [
                'name' => 'file',
                'filename' => $file->getClientOriginalName(),
                'contents' => $file->getContent(),
                'headers' => ['Content-Type' => $file->getClientMimeType()],
            ];
            $postAsyncs[] = ApiHelper::make()->postAsync('/api/fresns/v1/common/upload-file', [
                'multipart' => array_filter($params, fn ($val) => isset($val['contents'])),
            ]);
        }

        $results = ApiHelper::make()->unwrapRequests($postAsyncs);

        $data = [];
        foreach ($results as $result) {
            if (data_get($result, 'code') !== 0) {
                return Response::json($result);
            }
            $data[] = data_get($result, 'data');
        }

        return Response::json(['data' => $data, 'code' => 0]);
    }

    // editor update
    public function editorUpdate(Request $request, string $type, int $draftId)
    {
        $response = ApiHelper::make()->put("/api/fresns/v1/editor/{$type}/{$draftId}", [
            'json' => $request->all(),
        ]);

        return \response()->json($response);
    }

    // editor publish
    public function editorPublish(string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->post("/api/fresns/v1/editor/{$type}/{$draftId}");

        DataHelper::cacheForgetAccountAndUser();

        return \response()->json($response);
    }

    // editor recall
    public function editorRecall(string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->patch("/api/fresns/v1/editor/{$type}/{$draftId}");

        return \response()->json($response);
    }

    // editor delete
    public function editorDelete(string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->delete("/api/fresns/v1/editor/{$type}/{$draftId}");

        $uid = fs_user('detail.uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_user_panel_{$uid}", 'fresnsWeb');

        return \response()->json($response);
    }
}
