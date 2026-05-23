<div class="flex flex-col gap-2">
    <div class="flex items-center gap-2">
        <h2>{{ __('Webhooks') }}</h2>
        <x-helper
            :helper="__('For more details goto our <a class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\' target=\'_blank\'>docs</a>.')" />
    </div>
    <div>
        <x-forms.input readonly
            :helper="__('See details in our <a target=\'_blank\' class=\'underline dark:text-white\' href=\'https://coolify.io/docs/api-reference/api/operations/deploy-by-tag-or-uuid\'>documentation</a>.')"
            :label="__('Deploy Webhook (auth required)')" id="deploywebhook"></x-forms.input>
    </div>
    @if ($resource->type() === 'application')
        <div>
            <h3>{{ __('Manual Git Webhooks') }}</h3>
            @if ($githubManualWebhook && $gitlabManualWebhook)
                <form wire:submit='submit' class="flex flex-col gap-2">
                    <div class="flex items-end gap-2">
                        <x-forms.input :helper="__('Content Type in GitHub configuration could be json or form-urlencoded.')"
                            readonly :label="__('GitHub')" id="githubManualWebhook"></x-forms.input>
                        @can('update', $resource)
                            <x-forms.input type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in GitHub.')"
                                :label="__('GitHub Webhook Secret')" id="githubManualWebhookSecret"></x-forms.input>
                        @else
                            <x-forms.input disabled type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in GitHub.')"
                                :label="__('GitHub Webhook Secret')" id="githubManualWebhookSecret"></x-forms.input>
                        @endcan
                    </div>
                    <a target="_blank" class="flex hover:no-underline" href="{{ $resource?->gitWebhook }}">
                        <x-forms.button>{{ __('Webhook Configuration on GitHub') }}
                            <x-external-link />
                        </x-forms.button>
                    </a>
                    <div class="flex gap-2">
                        <x-forms.input readonly :label="__('GitLab')" id="gitlabManualWebhook"></x-forms.input>
                        @can('update', $resource)
                            <x-forms.input type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in GitLab.')"
                                :label="__('GitLab Webhook Secret')" id="gitlabManualWebhookSecret"></x-forms.input>
                        @else
                            <x-forms.input disabled type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in GitLab.')"
                                :label="__('GitLab Webhook Secret')" id="gitlabManualWebhookSecret"></x-forms.input>
                        @endcan
                    </div>
                    <div class="flex gap-2">
                        <x-forms.input readonly :label="__('Bitbucket')" id="bitbucketManualWebhook"></x-forms.input>
                        @can('update', $resource)
                            <x-forms.input type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in Bitbucket.')"
                                :label="__('Bitbucket Webhook Secret')" id="bitbucketManualWebhookSecret"></x-forms.input>
                        @else
                            <x-forms.input disabled type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in Bitbucket.')"
                                :label="__('Bitbucket Webhook Secret')" id="bitbucketManualWebhookSecret"></x-forms.input>
                        @endcan
                    </div>
                    <div class="flex gap-2">
                        <x-forms.input readonly :label="__('Gitea')" id="giteaManualWebhook"></x-forms.input>
                        @can('update', $resource)
                            <x-forms.input type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in Gitea.')"
                                :label="__('Gitea Webhook Secret')" id="giteaManualWebhookSecret"></x-forms.input>
                        @else
                            <x-forms.input disabled type="password"
                                :helper="__('Need to set a secret to be able to use this webhook. It should match with the secret in Gitea.')"
                                :label="__('Gitea Webhook Secret')" id="giteaManualWebhookSecret"></x-forms.input>
                        @endcan
                    </div>
                    @can('update', $resource)
                        <x-forms.button type="submit">{{ __('Save') }}</x-forms.button>
                    @endcan
                </form>
            @else
                <x-callout type="info" :title="__('Information')">
                    {{ __('You are using an official Git App. You do not need manual webhooks.') }}
                </x-callout>
            @endif
        </div>
    @endif

</div>
