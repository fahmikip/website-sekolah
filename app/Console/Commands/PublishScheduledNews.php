<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;

class PublishScheduledNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish news whose scheduled publication time has arrived';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = News::where('status', 'scheduled')->whereNotNull('published_at')->where('published_at', '<=', now())->update(['status' => 'published']);
        $this->info("{$count} berita terjadwal dipublikasikan.");

        return self::SUCCESS;
    }
}
