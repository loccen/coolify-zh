<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function Termwind\ask;
use function Termwind\render;
use function Termwind\style;

class NotifyDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:demo-notify {channel?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a demo notification, to a given channel. Run to see options.';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.notify_demo.description', locale: app()->getLocale()));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channel = $this->argument('channel');

        if (blank($channel)) {
            $this->showHelp();

            return;
        }
    }

    private function showHelp()
    {
        style('coolify')->color('#9333EA');
        style('title-box')->apply('mt-1 px-2 py-1 bg-coolify');

        $intro = trans('console.notify_demo.intro', locale: app()->getLocale());
        $channelsLabel = trans('console.notify_demo.channels_label', locale: app()->getLocale());

        render(
            <<<HTML
        <div>
            <div class="title-box">
                Coolify
            </div>
            <p class="mt-1 ml-1 ">
              {$intro}
            </p>
            <p class="px-1 mt-1 ml-1 bg-coolify">
              php artisan app:demo-notify {channel}
            </p>
            <div class="my-1">
                <div class="text-warning-500"> {$channelsLabel} </div>
                <ul class="text-coolify">
                    <li>email</li>
                    <li>discord</li>
                    <li>telegram</li>
                    <li>slack</li>
                    <li>pushover</li>
                </ul>
            </div>
        </div>
        HTML
        );

        $prompt = trans('console.notify_demo.prompt', locale: app()->getLocale());

        ask(<<<HTML
        <div class="mr-1">
            {$prompt}
        </div>
        HTML, ['email', 'discord', 'telegram', 'slack', 'pushover']);
    }
}
