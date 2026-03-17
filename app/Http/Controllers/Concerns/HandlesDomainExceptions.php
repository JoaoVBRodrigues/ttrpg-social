<?php

namespace App\Http\Controllers\Concerns;

use App\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait HandlesDomainExceptions
{
    /**
     * @template TReturn of \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\Response
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn|JsonResponse|RedirectResponse
     */
    protected function handleAction(Request $request, callable $callback, string $fallbackMessage = 'An unexpected error occurred.')
    {
        try {
            return $callback();
        } catch (DomainException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ], $exception->status());
            }

            return back()->withInput()->with('status', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Unhandled controller exception.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'path' => $request->path(),
                'user_id' => $request->user()?->getKey(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $fallbackMessage,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()->withInput()->with('status', $fallbackMessage);
        }
    }
}
