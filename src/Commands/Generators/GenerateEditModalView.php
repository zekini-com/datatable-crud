<?php
namespace Zekini\DatatableCrud\Commands\Generators;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class GenerateEditModalView extends BaseGenerator
{

    protected $classType = 'edit-view';

     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:edit-modal-view {table : table to generate crud for } {--user : When added the crud is generated for a user model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a unique form for model';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $files)
    {

       //publish any vendor files to where they belong
       $this->className = $this->getClassName();

       $this->classNameKebab = Str::kebab($this->className);

       $templateContent = $this->replaceContent();

       @$this->files->makeDirectory($path = resource_path('views/livewire/'.Str::plural($this->classNameKebab).DIRECTORY_SEPARATOR), 0777, true);
       $filename = $path.DIRECTORY_SEPARATOR.'edit.blade.php';

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

        $belongsTo = $this->belongsToConfiguration()->pluck('column')->toArray();



        return [
            'vissibleColumns'=> $this->getColumnDetails(),
            'relations'=>  $this->getRelations(),
            'belongsTo'=> $belongsTo,
            'recordTitleMap'=> $this->getRecordTitleTableMap(),
            'pivots'=> $pivots ?? [],
            'resourcePlural'=> Str::plural($this->classNameKebab)
        ];
    }





}
