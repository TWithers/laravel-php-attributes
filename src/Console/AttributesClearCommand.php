<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'attributes:clear')]
class AttributesClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'attributes:clear';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'attributes:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the attributes cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->getCachedAttributesPath());

        $this->components->info('Attribute cache cleared successfully.');
    }

    protected function getCachedAttributesPath(): string
    {
        if (is_null($env = Env::get('APP_ATTRIBUTES_CACHE'))) {
            return $this->laravel->bootstrapPath('cache/attributes.php');
        }

        return Str::startsWith($env, ['/', '\\'])
            ? $env
            : $this->laravel->basePath($env);
    }
}
