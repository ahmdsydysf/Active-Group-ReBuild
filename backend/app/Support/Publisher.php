<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * The public site is static HTML. Publishing asks GitHub Actions to rebuild it from the API
 * and upload it to the hosting (.github/workflows/publish.yml). This class tracks when content
 * last changed and when the site was last published, so the dashboard can say if a publish is due.
 */
final class Publisher
{
    private const CHANGED = 'content_changed_at';

    private const PUBLISHED = 'site_published_at';

    public static function touch(): void
    {
        Setting::query()->updateOrCreate(['key' => self::CHANGED], ['value' => now()->toIso8601String()]);
    }

    public static function configured(): bool
    {
        return filled(config('services.publish.token')) && filled(config('services.publish.repo'));
    }

    /** @return array{configured: bool, changed_at: ?string, published_at: ?string, pending: bool} */
    public static function status(): array
    {
        $changed = self::read(self::CHANGED);
        $published = self::read(self::PUBLISHED);

        return [
            'configured' => self::configured(),
            'changed_at' => $changed?->toIso8601String(),
            'published_at' => $published?->toIso8601String(),
            'pending' => $changed !== null && ($published === null || $changed->gt($published)),
        ];
    }

    /** Starts the publish workflow. Throws when GitHub refuses or is not configured. */
    public static function publish(): void
    {
        if (! self::configured()) {
            throw new RuntimeException('Publishing is not configured.');
        }

        $repo = config('services.publish.repo');
        $workflow = config('services.publish.workflow');
        $res = Http::withToken(config('services.publish.token'))
            ->acceptJson()
            ->timeout(15)
            ->post("https://api.github.com/repos/{$repo}/actions/workflows/{$workflow}/dispatches", [
                'ref' => config('services.publish.branch'),
            ]);

        if (! $res->successful()) {
            throw new RuntimeException('GitHub refused the publish request: '.$res->status());
        }

        Setting::query()->updateOrCreate(['key' => self::PUBLISHED], ['value' => now()->toIso8601String()]);
    }

    /** Scheduled posts whose date passed since the last publish need a rebuild to appear. */
    public static function hasDuePosts(): bool
    {
        $since = self::read(self::PUBLISHED);

        return Post::query()->where('is_active', true)
            ->where('published_at', '<=', now())
            ->when($since, fn ($q) => $q->where('published_at', '>', $since))
            ->exists();
    }

    private static function read(string $key): ?Carbon
    {
        $value = Setting::query()->whereKey($key)->value('value');

        return is_string($value) ? Carbon::parse($value) : null;
    }
}
