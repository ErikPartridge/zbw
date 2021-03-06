<?php  namespace Zbw\Console;

use Illuminate\Console\Command;

class MigrateOldRoster extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vatsim:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates ARTCC roster from ZBW website.';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $migrator = \App::make('Zbw\Bostonjohn\Roster\ZbwRosterUpdater');
        $data = $migrator->update();
        $this->info($data[0].' new users were added, and '.$data[1].' users were updated.');
    }
} 
