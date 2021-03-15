<?php

namespace SrcLab\AltLog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SrcLab\AltLog\LogManager;
use SrcLab\AltLog\LogParser;
use SrcLab\AltLog\Exceptions\LogNotFoundException;
use SrcLab\AltLog\Exceptions\LargeLogException;
use Throwable;
use Log;

class LogController extends Controller
{
    /**
     * Index page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('alt-log::index', [
            'back_url' => config('alt-log.view.back_url', '/'),
            'log_date_format' => config('alt-log.view.log_date_format'),
        ]);
    }

    /**
     * Get list logs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {

            $log_manager = app(LogManager::class);

            $log_list = $log_manager->getLogsList();
            $log_groups = $log_manager->getGroupingList();

            return $this->returnSuccess(null, [
                'log_list' => $log_list,
                'log_groups' => $log_groups,
            ]);

        } catch (Throwable $e) {
            Log::error($e);
            return $this->returnError(__('alt-log::general.server.server_error'));
        }
    }

    /**
     * Get log data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        try {

            $log_manager = app(LogManager::class);
            $log_parser = app(LogParser::class);

            return $this->returnSuccess(null, [
                'log_info' => $log_manager->getLogInfo($request->log),
                'log_data' => $log_parser->parseLog($log_manager->getLogPath($request->log)),
            ]);

        } catch (LargeLogException $e) {
            return $this->returnError(__('alt-log::general.server.large_log'));
        } catch (LogNotFoundException $e) {
            return $this->returnError(__('alt-log::general.server.log_not_found'));
        } catch (Throwable $e) {
            Log::error($e);
            return $this->returnError(__('alt-log::general.server.server_error'));
        }
    }

    /**
     * Delete logs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {

            $log_manager = app(LogManager::class);

            foreach ($request->logs as $log) {
                $log_manager->deleteLog($log);
            }

            return $this->returnSuccess(__('alt-log::general.server.logs_deleted'));

        } catch (LargeLogException $e) {
            return $this->returnError(__('alt-log::general.server.large_log'));
        } catch (LogNotFoundException $e) {
            return $this->returnError(__('alt-log::general.server.log_not_found'));
        } catch (Throwable $e) {
            Log::error($e);
            return $this->returnError(__('alt-log::general.server.server_error'));
        }
    }

    /**
     * Return success.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnSuccess($message, array $data = [])
    {
        return $this->returnJsonResult(array_merge([
            'operation_status' => [
                'status' => 'success',
                'message' => $message,
            ]
        ], $data));
    }

    /**
     * Return error.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnError($message)
    {
        return $this->returnJsonResult([
            'operation_status' => [
                'status' => 'error',
                'message' => $message,
            ]
        ]);
    }

    /**
     * Return fson format result.
     *
     * @param array $result
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnJsonResult(array $result)
    {
        if (!empty($result['operation_status']['status']) && $result['operation_status']['status'] == 'error') {
            return response()->json($result)->setStatusCode(422);
        } else {
            return response()->json($result);
        }
    }
}
