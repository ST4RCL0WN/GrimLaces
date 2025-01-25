<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterProfile;
use App\Models\Gallery\GallerySubmission;
use App\Models\News;
use App\Models\Sales\Sales;
use App\Models\SitePage;
use App\Models\Users\UserProfile;
use Illuminate\Console\Command;

class ConvertExtendedMentions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-extended-mentions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts the unparsed text of extended mentions to follow the new format, and also reparsing to update the parsed text.';

    /**
     * Execute the console command.
     */
    public function handle() {
        //
        $this->info('Converting extended mentions...');
        $this->info('Converting News...');
        foreach (News::all() as $news) {
            $newText = $this->convertText($news->text);
            $news->text = $newText;
            $news->parsed_text = parse($newText);
            $news->save();
        }

        $this->info('Converting Site Pages...');
        foreach (SitePage::all() as $page) {
            $newText = $this->convertText($page->text);
            $page->text = $newText;
            $page->parsed_text = parse($newText);
            $page->save();
        }

        $this->info('Converting Sales...');
        foreach (Sales::all() as $sale) {
            $newText = $this->convertText($sale->description);
            $sale->description = $newText;
            $sale->parsed_description = parse($newText);
            $sale->save();
        }

        $this->info('Converting Character Profiles...');
        foreach (CharacterProfile::all() as $profile) {
            $newText = $this->convertText($profile->description);
            $profile->description = $newText;
            $profile->parsed_description = parse($newText);
            $profile->save();
        }

        $this->info('Converting User Profiles...');
        foreach (UserProfile::all() as $profile) {
            $newText = $this->convertText($profile->bio);
            $profile->bio = $newText;
            $profile->parsed_bio = parse($newText);
            $profile->save();
        }

        $this->info('Converting Gallery Submissions...');
        foreach (GallerySubmission::all() as $submission) {
            $newText = $this->convertText($submission->description);
            $submission->description = $newText;
            $submission->parsed_description = parse($newText);
            $submission->save();
        }

        $this->info('Conversion complete.');
    }

    private function convertText($text) {
        $toMatch = [
            '/\B@([A-Za-z0-9_-]+)/', // matches @mentions
            '/\B%([A-Za-z0-9_-]+)/', // matches %mentions
            '/\[user=([^\[\]&<>?"\']+)\]/', // matches [user=username]
            '/\[userav=([^\[\]&<>?"\']+)\]/', // matches [userav=username]
            '/\[character=([^\[\]&<>?"\']+)\]/', // matches [character=name]
            '/\[charthumb=([^\[\]&<>?"\']+)\]/', // matches [charthumb=name]
            '/\[thumb=([^\[\]&<>?"\']+)\]/', // matches [thumb=name]
        ];

        $replacements = [
            '<span class="data-mention" data-mention-type="user" data-id="$1">@$1</span>',
            '<span class="data-mention" data-mention-type="user" data-id="$1"><img src="/avatars/$1.jpg"></span><span class="data-mention" data-mention-type="user" data-id="$1">@$1</span>',
            '<span class="data-mention" data-mention-type="user" data-id="$1">@$1</span>',
            '<span class="data-mention" data-mention-type="user" data-id="$1"><img src="/avatars/$1.jpg"></span>',
            '<span class="data-mention" data-mention-type="character" data-id="$1">@$1</span>',
            '<span class="data-mention" data-mention-type="character" data-id="$1"><img src="/character_thumbs/$1.jpg"></span>',
            '<span class="data-mention" data-mention-type="gallery_submission" data-id="$1"><img class="img-fluid rounded" src="/gallery_thumbs/$1.jpg"></span>',
        ];

        foreach ($toMatch as $index => $pattern) {
            $text = preg_replace($pattern, $replacements[$index], $text);
        }

        return $text;
    }
}
