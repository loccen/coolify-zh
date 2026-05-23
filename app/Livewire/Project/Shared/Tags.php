<?php

namespace App\Livewire\Project\Shared;

use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

// Refactored ✅
class Tags extends Component
{
    use AuthorizesRequests;

    public $resource = null;

    #[Validate('required|string|min:2')]
    public string $newTags;

    public $tags = [];

    public $filteredTags = [];

    public function mount()
    {
        $this->loadTags();
    }

    public function loadTags()
    {
        $this->tags = Tag::ownedByCurrentTeam()->get();
        $this->filteredTags = $this->tags->filter(function ($tag) {
            return ! $this->resource->tags->contains($tag);
        });
    }

    public function submit()
    {
        try {
            $this->authorize('update', $this->resource);
            $this->validate();
            $tags = str($this->newTags)->trim()->explode(' ');
            foreach ($tags as $tag) {
                $tag = strip_tags($tag);
                if (strlen($tag) < 2) {
                    $this->dispatch('error', __('Invalid tag.'), __('Tag <span class=\'dark:text-warning\'>:tag</span> is invalid. Min length is 2.', ['tag' => $tag]));

                    continue;
                }
                if ($this->resource->tags()->where('name', $tag)->exists()) {
                    $this->dispatch('error', __('Duplicate tags.'), __('Tag <span class=\'dark:text-warning\'>:tag</span> already added.', ['tag' => $tag]));

                    continue;
                }
                $found = Tag::ownedByCurrentTeam()->where(['name' => $tag])->exists();
                if (! $found) {
                    $found = Tag::create([
                        'name' => $tag,
                        'team_id' => currentTeam()->id,
                    ]);
                }
                $this->resource->tags()->attach($found->id);
            }
            $this->refresh();
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function addTag(string $id, string $name)
    {
        try {
            $this->authorize('update', $this->resource);
            $name = strip_tags($name);
            if ($this->resource->tags()->where('id', $id)->exists()) {
                $this->dispatch('error', __('Duplicate tags.'), __('Tag <span class=\'dark:text-warning\'>:tag</span> already added.', ['tag' => $name]));

                return;
            }
            $this->resource->tags()->attach($id);
            $this->refresh();
            $this->dispatch('success', __('Tag added.'));
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function deleteTag(string $id)
    {
        try {
            $this->authorize('update', $this->resource);
            $this->resource->tags()->detach($id);
            $found_more_tags = Tag::ownedByCurrentTeam()->find($id);
            if ($found_more_tags && $found_more_tags->applications()->count() == 0 && $found_more_tags->services()->count() == 0) {
                $found_more_tags->delete();
            }
            $this->refresh();
            $this->dispatch('success', __('Tag deleted.'));
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function refresh()
    {
        $this->resource->refresh(); // Remove this when legacy_model_binding is false
        $this->loadTags();
        $this->reset('newTags');
    }
}
