<?php

namespace App\Livewire\Project\Shared;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ResourceLimits extends Component
{
    use AuthorizesRequests;

    public $resource;

    // Explicit properties
    public ?string $limitsCpus = null;

    public ?string $limitsCpuset = null;

    public mixed $limitsCpuShares = null;

    public string $limitsMemory;

    public string $limitsMemorySwap;

    public mixed $limitsMemorySwappiness = 0;

    public string $limitsMemoryReservation;

    protected $rules = [
        'limitsMemory' => ['required', 'string', 'regex:/^(0|\d+[bBkKmMgG])$/'],
        'limitsMemorySwap' => ['required', 'string', 'regex:/^(0|\d+[bBkKmMgG])$/'],
        'limitsMemorySwappiness' => 'required|integer|min:0|max:100',
        'limitsMemoryReservation' => ['required', 'string', 'regex:/^(0|\d+[bBkKmMgG])$/'],
        'limitsCpus' => ['nullable', 'regex:/^\d*\.?\d+$/'],
        'limitsCpuset' => ['nullable', 'regex:/^\d+([,-]\d+)*$/'],
        'limitsCpuShares' => 'nullable|integer|min:0',
    ];

    protected $validationAttributes = [
        'limitsMemory' => '内存',
        'limitsMemorySwap' => '交换内存',
        'limitsMemorySwappiness' => '交换倾向',
        'limitsMemoryReservation' => '预留内存',
        'limitsCpus' => 'CPU 数量',
        'limitsCpuset' => 'CPU 集',
        'limitsCpuShares' => 'CPU 权重',
    ];

    protected $messages = [
        'limitsMemory.regex' => '最大内存限制必须是带单位的数字（b、k、m、g）。例如 256m、1g。填 0 表示不限制。',
        'limitsMemorySwap.regex' => '最大交换内存限制必须是带单位的数字（b、k、m、g）。例如 256m、1g。填 0 表示不限制。',
        'limitsMemoryReservation.regex' => '软内存限制必须是带单位的数字（b、k、m、g）。例如 256m、1g。填 0 表示不限制。',
        'limitsCpus.regex' => 'CPU 数量必须是数字（整数或小数）。例如 0.5、2。',
        'limitsCpuset.regex' => 'CPU 集必须是用逗号分隔的 CPU 编号或范围列表。例如 0-2 或 0,1,3。',
        'limitsMemorySwappiness.integer' => '交换倾向必须是 0 到 100 之间的整数。',
        'limitsMemorySwappiness.min' => '交换倾向必须在 0 到 100 之间。',
        'limitsMemorySwappiness.max' => '交换倾向必须在 0 到 100 之间。',
        'limitsCpuShares.integer' => 'CPU 权重必须是整数。',
        'limitsCpuShares.min' => 'CPU 权重必须是正数。',
    ];

    /**
     * Sync data between component properties and model
     *
     * @param  bool  $toModel  If true, sync FROM properties TO model. If false, sync FROM model TO properties.
     */
    private function syncData(bool $toModel = false): void
    {
        if ($toModel) {
            // Sync TO model (before save)
            $this->resource->limits_cpus = $this->limitsCpus;
            $this->resource->limits_cpuset = $this->limitsCpuset;
            $this->resource->limits_cpu_shares = (int) $this->limitsCpuShares;
            $this->resource->limits_memory = $this->limitsMemory;
            $this->resource->limits_memory_swap = $this->limitsMemorySwap;
            $this->resource->limits_memory_swappiness = (int) $this->limitsMemorySwappiness;
            $this->resource->limits_memory_reservation = $this->limitsMemoryReservation;
        } else {
            // Sync FROM model (on load/refresh)
            $this->limitsCpus = $this->resource->limits_cpus;
            $this->limitsCpuset = $this->resource->limits_cpuset;
            $this->limitsCpuShares = $this->resource->limits_cpu_shares;
            $this->limitsMemory = $this->resource->limits_memory;
            $this->limitsMemorySwap = $this->resource->limits_memory_swap;
            $this->limitsMemorySwappiness = $this->resource->limits_memory_swappiness;
            $this->limitsMemoryReservation = $this->resource->limits_memory_reservation;
        }
    }

    public function mount()
    {
        $this->syncData(false);
    }

    public function submit()
    {
        try {
            $this->authorize('update', $this->resource);

            // Apply default values to properties
            if (! $this->limitsMemory) {
                $this->limitsMemory = '0';
            }
            if (! $this->limitsMemorySwap) {
                $this->limitsMemorySwap = '0';
            }
            if ($this->limitsMemorySwappiness === '' || is_null($this->limitsMemorySwappiness)) {
                $this->limitsMemorySwappiness = 60;
            }
            if (! $this->limitsMemoryReservation) {
                $this->limitsMemoryReservation = '0';
            }
            if (! $this->limitsCpus) {
                $this->limitsCpus = '0';
            }
            if ($this->limitsCpuset === '') {
                $this->limitsCpuset = null;
            }
            if ($this->limitsCpuShares === '' || is_null($this->limitsCpuShares)) {
                $this->limitsCpuShares = 1024;
            }

            $this->validate();

            $this->syncData(true);
            $this->resource->save();
            $this->dispatch('success', __('Resource limits updated.'));
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $message) {
                $this->dispatch('error', $message);
            }

            return;
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }
}
