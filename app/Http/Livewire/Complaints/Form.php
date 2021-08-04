<?php

namespace App\Http\Livewire\Complaints;

use App\Actions\Complaint\CreateComplaint;
use App\Actions\Complaint\UpdateComplaint;
use App\Models\Complaint;
use App\Traits\Validators\FocusError;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    use FocusError;

    public $complaintId;

    public $input;

    public $showModal = false;

    public $options = [
        'labels' => [],
        'insurers' => [],
        'natures' => [],
        'tier.1.results' => [],
        'tier.2.staffPositions' => [],
        'tier.2.results' => [],
    ];

    protected $listeners = ['add', 'edit'];

    public function mount()
    {
        $this->resetInput();

        foreach ($this->options as $key => $option) {
            $this->options[$key] = collect(config('services.complaint.' . $key))->map(function ($item) {
                return [
                    'value' => $item,
                    'label' => $item,
                ];
            })->all();
        }
    }

    public function render()
    {
        return view('livewire.complaints.form');
    }

    public function updated($name, $value)
    {
        if ('input.tier.1.result' == $name && 'Failed' != $value) {
            unset($this->input['tier'][2]);

            $this->input['tier'][2]['handed_over_at'] = '';
        }
    }

    public function resetInput()
    {
        $this->input = [
            'received_at' => '',
            'registered_at' => '',
            'acknowledged_at' => '',
            'tier' => [
                1 => [
                    'handed_over_at' => '',
                    'resulted_at' => '',
                ],
                2 => [
                    'handed_over_at' => '',
                ],
            ],
        ];
    }

    public function add()
    {
        $this->complaintId = null;

        $this->resetInput();

        $this->dispatchBrowserEvent('complainant-lookup-value');

        $this->dispatchBrowserEvent('adviser-lookup-value');

        $this->dispatchBrowserEvent('staff-lookup-value');

        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->complaintId = $id;

        $this->input = collect(Complaint::findOrFail($id))->except([
            'id',
            'created_at',
            'updated_at',
        ])->all();

        $complainant = json_encode([[
            'value' => $this->input['complainant'],
            'label' => $this->input['complainant'],
        ]]);

        $this->dispatchBrowserEvent('complainant-lookup-value', $complainant);

        $adviser = json_encode([[
            'value' => $this->input['tier'][1]['adviser'],
            'label' => $this->input['tier'][1]['adviser'],
        ]]);

        $this->dispatchBrowserEvent('adviser-lookup-value', $adviser);

        $staff = isset($this->input['tier'][2]['staff_name']) ? json_encode([[
            'value' => $this->input['tier'][2]['staff_name'],
            'label' => $this->input['tier'][2]['staff_name'],
        ]]) : null;

        $this->dispatchBrowserEvent('staff-lookup-value', $staff);

        $this->showModal = true;
    }

    public function complainantLookupSearch($search = '')
    {
        $complainants = Complaint::select('complainant')->groupBy('complainant')
            ->when($search, function ($query) use ($search) {
                return $query->where('complainant', 'like', '%' . $search . '%');
            })->oldest('complainant')->get()->map(function ($complaint) {
                return [
                    'value' => $complaint->complainant,
                    'label' => $complaint->complainant,
                ];
            });

        $this->dispatchBrowserEvent('complainant-lookup-list', $complainants);
    }

    public function adviserLookupSearch($search = '')
    {
        $query = Complaint::select('tier->1->adviser as adviser')->groupBy('adviser')
            ->when($search, function ($query) use ($search) {
                return $query->whereRaw('LOWER(json_unquote(json_extract(`tier`, \'$."1"."adviser"\'))) LIKE ?', '%' . Str::lower($search) . '%');
            })->oldest('adviser');

        $advisers = $query->get()->map(function ($complaint) {
            return [
                'value' => $complaint['adviser'],
                'label' => $complaint['adviser'],
            ];
        });

        $this->dispatchBrowserEvent('adviser-lookup-list', $advisers);
    }

    public function staffLookupSearch($search = '')
    {
        $query = Complaint::select('tier->2->staff_name as staff_name')->groupBy('staff_name')
            ->where('tier->1->result', 'Failed')
            ->when($search, function ($query) use ($search) {
                return $query->whereRaw('LOWER(json_unquote(json_extract(`tier`, \'$."2"."staff_name"\'))) LIKE ?', '%' . Str::lower($search) . '%');
            })->oldest('staff_name');

        $staffs = $query->get()->map(function ($complaint) {
            return [
                'value' => $complaint->staff_name,
                'label' => $complaint->staff_name,
            ];
        });

        $this->dispatchBrowserEvent('staff-lookup-list', $staffs);
    }

    public function dehydrate()
    {
        $this->focusError();
    }

    public function submit()
    {
        $this->complaintId ? $this->update(new UpdateComplaint()) : $this->create(new CreateComplaint());
    }

    public function create(CreateComplaint $action)
    {
        $action->create($this->input);

        $this->emitTo('complaints.index', 'render');

        $this->showModal = false;

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Complaint has been created.',
        ]);
    }

    public function update(UpdateComplaint $action)
    {
        $action->update($this->input, Complaint::findOrFail($this->complaintId));

        $this->emitTo('complaints.index', 'render');

        $this->showModal = false;

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Complaint has been updated.',
        ]);
    }
}
