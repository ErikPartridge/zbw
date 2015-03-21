<?php namespace Zbw\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'Zbw\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);
		Route::filter('auth', function()
		{
			if (!\Sentry::check()) return \Redirect::guest('auth');
		});

		Route::filter('guest', function()
		{
			if (\Sentry::check()) return \Redirect::to('/');
		});

		Route::filter('csrf', function()
		{
			if (Session::token() != Input::get('_token'))
			{
				throw new Illuminate\Session\TokenMismatchException;
			}
		});

		Route::filter('cache.fetch', 'Zbw\Start\CacheFilter@fetch');
		Route::filter('cache.put', 'Zbw\Start\CacheFilter@put');

		Route::filter('controller', function() {
		    if(! \Sentry::check()) {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'Registered User'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('staff', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->is('Staff')) {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'general staff member'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('executive', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->is('Executive'))
		    {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'executive staff member'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('events', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->is('Executive') && ! \Sentry::getUser()->is('Events'))
		    {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'executive staff member'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('instructor', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->is('Instructors'))
		    {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'instructor'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('mentor', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->is('Mentors') && ! \Sentry::getUser()->is('Instructors') && ! \Sentry::getUser()->is('Executive'))
		    {
		        $data = [
		            'page' => \Request::url(),
		            'needed' => 'mentor'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('facilities', function() {
		    if(! \Sentry::check() ) {
		        return \Redirect::route('login');
		    }
		    if(! \Sentry::getUser()->hasAccess('files.sector'))
		    {
		        $data = [
		          'page' => \Request::url(),
		          'needed' => 'facilities engineer'
		        ];
		        return \View::make('zbw.errors.403', $data);
		    }
		});

		Route::filter('suspended', function() {
		      if(!\Sentry::check() || \Sentry::getUser()->rating->id === 0 || \Sentry::getUser()->activated === 0 || \Sentry::getUser()->terminated) {
		          $data = [
		              'page' => \Request::url(),
		              'needed' => 'active (your account is suspended by ZBW or VATUSA)'
		          ];
		          return \View::make('zbw.errors.403', $data);
		      }
		  });

		Route::filter('terminated', function() {
		     if(\Sentry::check() && \Sentry::getUser()->terminated) {
		         $data = [
		             'page' => \Request::url(),
		             'needed' => 'active (your accout has been terminated)'
		         ];
		         return \View::make('zbw.errors.403', $data);
		     }
		  });

		Route::filter('cache.fetch', 'Zbw\Start\CacheFilter@fetch');
		Route::filter('cache.put', 'Zbw\Start\CacheFilter@put');
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/public_routes.php');
			require app_path('Http/member_routes.php');
			require app_path('Http/staff_routes.php');
		});
	}

}