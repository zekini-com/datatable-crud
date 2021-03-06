<?php
namespace Zekini\DatatableCrud\Commands\Generators;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateIndexComponent extends BaseGenerator
{

    protected $classType = "component-index";

     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:index
                            {table : table to generate crud for }
                            {--user : When added the crud is generated for a user model}
                            {--readonly : The datatable is read only no create and edit buttons}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a livewire index component for the model';


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'Http\Livewire\\';
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $files)
    {
        $this->info('Generating Index Component Class');

       //publish any vendor files to where they belong
       $this->className = $this->getClassName();

       $this->classNameKebab = Str::kebab($this->className);

       $this->namespace = $this->getDefaultNamespace($this->rootNamespace());

       $templateContent = $this->replaceContent();

       $path = $this->getLivewireComponentDir();

       @$this->files->makeDirectory($path, 0777);
       $filename = $path.DIRECTORY_SEPARATOR.'Index.php';

       $this->files->put($filename, $templateContent);

        return Command::SUCCESS;
    }


    /**
     * Get view data
     *
     * @return array
     */
    protected function getViewData()
    {
        $pivots = $this->belongsToConfiguration()->filter(function($item){
            return !empty($item['pivot']) && isset($item['pivot']);
        });

        return [
            'controllerNamespace' => rtrim($this->namespace, '\\'),
            'modelBaseName' => $this->className,
            'viewName'=> Str::plural($this->classNameKebab),
            'modelVariableName' => strtolower($this->className),
            'modelDotNotation' => Str::singular($this->argument('table')),
            'resource'=> strtolower($this->className),
            'modelFullName'=> "App\Models\\".$this->className,
            'vissibleColumns'=> $this->getColumnDetails(),
            'hasFile'=> $this->hasColumn('image') || $this->hasColumn('file'),
            'pivots'=> $pivots ?? [],
            'userModel'=> $this->option('user'),
            'tableName'=> $this->argument('table'),
            'isReadonly'=> $this->option('readonly')
        ];
    }



}
