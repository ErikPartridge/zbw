<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!\Sentry::check()) return Redirect::guest('auth');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (\Sentry::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|-------------------------s-------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

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
            'page' => Request::url(),
            'needed' => 'Registered User'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('staff', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->is('Staff')) {
        $data = [
            'page' => Request::url(),
            'needed' => 'general staff member'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('executive', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->is('Executive'))
    {
        $data = [
            'page' => Request::url(),
            'needed' => 'executive staff member'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('events', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->is('Executive') && ! \Sentry::getUser()->is('Events'))
    {
        $data = [
            'page' => Request::url(),
            'needed' => 'executive staff member'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('instructor', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->is('Instructors'))
    {
        $data = [
            'page' => Request::url(),
            'needed' => 'instructor'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('mentor', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->is('Mentors') && ! \Sentry::getUser()->is('Instructors') && ! \Sentry::getUser()->is('Executive'))
    {
        $data = [
            'page' => Request::url(),
            'needed' => 'mentor'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('facilities', function() {
    if(! \Sentry::check() ) {
        return Redirect::route('login');
    }
    if(! \Sentry::getUser()->hasAccess('files.sector'))
    {
        $data = [
          'page' => Request::url(),
          'needed' => 'facilities engineer'
        ];
        return View::make('zbw.errors.403', $data);
    }
});

Route::filter('suspended', function() {
      if(!\Sentry::check() || \Sentry::getUser()->rating->id === 0 || \Sentry::getUser()->activated === 0 || \Sentry::getUser()->terminated) {
          $data = [
              'page' => Request::url(),
              'needed' => 'active (your account is suspended by ZBW or VATUSA)'
          ];
          return View::make('zbw.errors.403', $data);
      }
  });

Route::filter('terminated', function() {
     if(\Sentry::check() && \Sentry::getUser()->terminated) {
         $data = [
             'page' => Request::url(),
             'needed' => 'active (your accout has been terminated)'
         ];
         return View::make('zbw.errors.403', $data);
     }
  });

Route::filter('cache.fetch', 'Zbw\Start\CacheFilter@fetch');
Route::filter('cache.put', 'Zbw\Start\CacheFilter@put');
