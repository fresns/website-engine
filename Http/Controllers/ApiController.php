<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class ApiController extends Controller
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function getInputTips(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'queryType' => 'required|integer',
            'queryKey' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response::json(['message' => fs_lang('errorEmpty')], 400);
        }

        $result = Factory::information()->input_tips->get($request->get('queryType'), $request->get('queryKey'));

        return Response::json(Arr::get($result, 'data'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function postDraft(Request $request): JsonResponse
    {
        $validator = Validator::make($request->post(),
            [
                'logType' => 'required|in:1,2|integer',
                'logId' => 'required|integer',
                'user_id' => 'required',
                'account_id' => 'required',
                'request_token' => 'required',
            ],
        );

        if ($validator->fails()) {
            return Response::json(['message' => fs_lang('errorEmpty')], 400);
        }

        $types = 'text';

        if ($request->post('filesJsons') && $filesArray = json_decode($request->post('filesJsons'), true)) {
            $types = implode(',', get_filetypes(Arr::pluck($filesArray, 'type')));
        }

        $result = Factory::editor()->draft->update(
            $request->post('logType'),
            $request->post('logId'),
            $types,
            $request->post('gid'),
            $request->has('title') ? $request->post('title') : null,
            $request->has('content') ? $request->post('content') : null,
            $request->has('filesJson') ? $request->post('filesJson') : null,
        );

        return Response::json($result);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function sendVerifyCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required|in:1,2|integer',
                'useType' => 'required|integer',
                'templateId' => 'required|integer',
            ],
            [
                'templateId.required' => fs_lang('verifyCode').'(Template ID): '.fs_lang('errorEmpty'),
                'countryCode.required' => fs_lang('countryCode').': '.fs_lang('errorEmpty'),
            ]
        );

        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()[0]], 400);
        }

        try {
            $result = Factory::information()->verify_code->send(
                (int) $request->post('type'),
                (int) $request->post('useType'),
                (int) $request->post('templateId'),
                $request->post('account'),
                $request->post('countryCode'),
            );
            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_lang('send').': '.fs_lang('success')]);
            } else {
                return Response::json(['message' => Arr::get($result, 'message')], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @param  string  $category
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function groupListByCategory(string $category): JsonResponse
    {
        $data = Arr::get(Factory::content()->group->lists(2, null, null, null, $category), 'data.list');

        return Response::json(compact('data'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function postAccountSite(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->post(),
                [
                    'now_editPassword' => 'required_if:editPassword_mode,password_to_editPassword',
                    'new_editPassword' => 'required_with:editPassword_mode,password_to_editPassword,email_to_editPassword,phone_to_editPassword|confirmed',
                    'now_editWalletPassword' => 'required_if:editWalletPassword_mode,password_to_editWalletPassword',
                    'new_editWalletPassword' => 'required_with:editWalletPassword_mode,password_to_editWalletPassword,email_to_editWalletPassword,phone_to_editWalletPassword|confirmed',
                    'email_verifyCode' => ['required_if:editPassword_mode,email_to_editPassword', 'required_if:editWalletPassword_mode,email_to_editWalletPassword'],
                    'phone_verifyCode' => ['required_if:editPassword_mode,phone_to_editPassword', 'required_if:editWalletPassword_mode,phone_to_editWalletPassword'],
                ],
                [
                    'now_editPassword.required_if' => fs_lang('pleaseEnter').': '.fs_lang('passwordCurrent').'('.fs_lang('accountPassword').')',
                    'new_editPassword.required_with' =>  fs_lang('pleaseEnter').': '.fs_lang('passwordNew'),
                    'new_editPassword.confirmed' => fs_lang('accountPassword').': '.fs_lang('errorInconsistent'),
                    'now_editWalletPassword.required_if' => fs_lang('pleaseEnter').': '.fs_lang('passwordCurrent').'('.fs_lang('walletPassword').')',
                    'new_editWalletPassword.required_with' => fs_lang('pleaseEnter').': '.fs_lang('passwordNew'),
                    'new_editWalletPassword.confirmed' => fs_lang('walletPassword').': '.fs_lang('errorInconsistent'),
                    'email_verifyCode.required_if' => fs_lang('pleaseEnter').': '.fs_lang('verifyCode').'('.fs_lang('email').')',
                    'phone_verifyCode.required_if' => fs_lang('pleaseEnter').': '.fs_lang('verifyCode').'('.fs_lang('phone').')',
                ]
            );

            if ($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()[0]], 400);
            }

            if ($request->input('editPassword_mode')) {
                switch ($request->input('editPassword_mode')) {
                    case 'password_to_editPassword':
                        $password = request('now_editPassword');
                        $editPassword = request('new_editPassword');
                        break;
                    case 'email_to_editPassword':
                        $editPassword = request('new_editPassword');
                        $codeType = 1;
                        $verifyCode = request('email_verifyCode');
                        break;
                    case 'phone_to_editPassword':
                        $editPassword = request('new_editPassword');
                        $codeType = 2;
                        $verifyCode = request('phone_verifyCode');
                        break;
                    default:
                        break;
                }
            } elseif ($request->input('editWalletPassword_mode')) {
                switch ($request->input('editWalletPassword_mode')) {
                    case 'password_to_editWalletPassword':
                        $walletPassword = request('now_editWalletPassword');
                        $editWalletPassword = request('new_editWalletPassword');
                        break;
                    case 'email_to_editWalletPassword':
                        $editWalletPassword = request('new_editWalletPassword');
                        $codeType = 1;
                        $verifyCode = request('email_verifyCode');
                        break;
                    case 'phone_to_editWalletPassword':
                        $editWalletPassword = request('new_editWalletPassword');
                        $codeType = 2;
                        $verifyCode = request('phone_verifyCode');
                        break;
                    default:
                        break;
                }
            }

            $result = Factory::user()->auth->edit(
                $verifyCode ?? request('verifyCode'),
                $codeType ?? request('codeType'),
                $password ?? request('password'),
                $walletPassword ?? request('walletPassword'),
                request('editEmail'),
                request('editPhone'),
                request('editCountryCode'),
                $editPassword ?? request('editPassword'),
                $editWalletPassword ?? request('editWalletPassword'),
                request('editLastLoginTime'),
                request('deleteConnectId'),
                request('newVerifyCode'),
            );
            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_lang('modify').': '.fs_lang('success')]);
            } else {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function postUserSite(): JsonResponse
    {
        try {
            $result = Factory::user()->auth->edit(
                request('username', request()->has('username') ? '' : null),
                request('nickname', request()->has('nickname') ? '' : null),
                request('avatarFid', request()->has('avatarFid') ? '' : null),
                request('avatarUrl', request()->has('avatarFid') ? '' : null),
                request('gender'),
                request('birthday', request()->has('birthday') ? '' : null),
                request('bio', request()->has('bio') ? '' : null),
                request('dialogLimit'),
                request('timezone')
            );
            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_lang('modify').': '.fs_lang('success')]);
            } else {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function accountVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->post(),
            [
                'codeType' => 'required|integer',
                'verifyCode' => 'required|string',
            ],
            [
                'codeType.required' => fs_lang('verifyCode').': '.fs_lang('accountType').'('.fs_lang('errorEmpty').')',
                'verifyCode.required' => fs_lang('verifyCode').': '.fs_lang('errorEmpty'),
            ]
        );

        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()[0]], 400);
        }

        try {
            $result = Factory::user()->auth->verification(
                request('codeType'),
                request('verifyCode')
            );

            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_lang('check').': '.fs_lang('success')]);
            } else {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    public function drafts(Request $request)
    {
        try {
            $result = Factory::editor()->draft->lists(
                $request->input('type'),
                $request->input('status'),
            );

            if (Arr::get($result, 'code') === 0) {
                return Response::json(Arr::get($result, 'data.list'));
            } else {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    public function publish(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required',
                'content' => 'required',
            ],
            [
                'content.required' => fs_lang('pleaseEnter').': '.fs_lang('editorContent'),
            ]
        );

        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()[0], 'code' => 400], 400);
        }

        try {
            $result = Factory::editor()->upload->publish(
                (int) $request->post('type'),
                $request->post('title'),
                $request->post('content'),
                0,
                (int) $request->post('anonymous', 0),
                $request->file('formFile'),
                $request->post('gid'),
                $request->post('commentPid'),
                $request->post('commentCid')
            );

            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_config('publish_post_name').': '.fs_lang('success')]);
            }

            if (Arr::get($result, 'message')) {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getSign(): JsonResponse
    {
        return Response::json(['sign' => sign()]);
    }
}
