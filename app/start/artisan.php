<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new Zbw\Commands\UpdateMetars);
Artisan::add(new Zbw\Commands\UpdateUrls);
Artisan::add(new Zbw\Commands\UpdateClients);
Artisan::add(new Zbw\Commands\UpdateRoster);
Artisan::add(new Zbw\Commands\MigrateOldRoster);
Artisan::add(new Zbw\Commands\ImportExamQuestions);
