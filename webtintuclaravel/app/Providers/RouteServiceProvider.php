<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('comments', function (Request $request) {
            $identity = $request->user()
                ? 'user:' . $request->user()->id
                : 'ip:' . $request->ip();

            return [
                Limit::perMinute(max(1, (int) config('comments.rate_limit.per_minute', 3)))
                    ->by($identity)
                    ->response(function () {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bạn gửi bình luận quá nhanh. Vui lòng chờ một chút rồi thử lại.',
                        ], 429);
                    }),
                Limit::perHour(max(1, (int) config('comments.rate_limit.per_hour', 20)))
                    ->by($identity)
                    ->response(function () {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bạn đã đạt giới hạn bình luận trong giờ này.',
                        ], 429);
                    }),
            ];
        });
    }
}
