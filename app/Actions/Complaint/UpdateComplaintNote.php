<?php

namespace App\Actions\Complaint;

use App\Models\ComplaintNote;
use App\Traits\Validators\ComplaintNoteValidator;
use Illuminate\Support\Facades\Validator;

class UpdateComplaintNote
{
    use ComplaintNoteValidator;

    public function update($input, ComplaintNote $note)
    {
        $data = Validator::make($input, $this->complaintNoteRules(true), [], $this->complaintNoteAttributes())->validate();

        $data['created_by'] = auth()->user()->id;

        $note->update($data);
    }
}
