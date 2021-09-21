<x-jet-dialog-modal wire:model="showModal">
  <x-slot name="title">Complaint Notes</x-slot>
  <x-slot name="content">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div
          class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white divide-y divide-gray-200">
          @if ($notes->count())
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-shark uppercase tracking-wider">
                    Date Added
                  </th>
                  <th scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-shark uppercase tracking-wider">
                    Added By
                  </th>
                  <th scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-shark uppercase tracking-wider">
                    Notes
                  </th>
                </tr>
              </thead>
              <tbody>
                @foreach ($notes as $index => $note)
                  <tr class="{{ $index % 2 ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-shark text-opacity-75">
                      {{ $note->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-shark text-opacity-75">
                      {{ $note->creator->name }}
                    </td>
                    <td class="px-4 py-2 text-sm text-shark text-opacity-75">
                      {{ $note->notes }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            {{ $notes->links() }}
          @else
            <p class="text-shark px-4 py-3">No available notes.</p>
          @endif
        </div>
      </div>
    </div>
  </x-slot>
  <x-slot name="footer">
    <x-jet-secondary-button type="button" wire:click="$set('showModal', false)">Close</x-jet-secondary-button>
  </x-slot>
</x-jet-dialog-modal>
