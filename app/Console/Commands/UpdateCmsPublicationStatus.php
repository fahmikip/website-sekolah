<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;

class UpdateCmsPublicationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:update-publication-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled announcements and expire outdated announcements';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $published = Announcement::where('status', 'scheduled')->where('published_at', '<=', now())->update(['status' => 'published']);
        $expired = Announcement::where('status', 'published')->whereNotNull('expires_at')->where('expires_at', '<=', now())->update(['status' => 'expired']);
        $this->info("{$published} dipublikasikan, {$expired} kedaluwarsa.");

        return self::SUCCESS;
    }
}
