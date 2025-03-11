<div>
    @if ($form_type != 'document')
        <div class="card">
            <div class="card-header">
                <h2>Formulaires</h2>
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Form Title</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($forms as $index => $form)
                            @if ($form['type'] != 'document')
                                <tr>
                                    <td>
                                        <input type="text" wire:model="forms.{{ $index }}.form_title"
                                            class="form-control">
                                    </td>
                                    <td>{{ $form['type'] }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm"
                                            wire:click="deleteForm({{ $form['id'] }})">Delete</button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <form wire:submit.prevent="addForm">
                            <input type="hidden" wire:model="newForm.etape_id">
                            <input type="hidden" wire:model="newForm.etape_number">
                            <input type="hidden" wire:model="newForm.fiche_id">
                            <input type="hidden" wire:model="newForm.version_id">
                        <tr>
                            <td>
                                <input required type="text" class="form-control" wire:model="newForm.form_title">


                            </td>
                            <td>
                                <div class="row mb-3" @if ($form_type == 'document') style="display:none" @endif>
                                    <div class="col-sm-10">
                                        <select required class="form-select" wire:model="newForm.type">
                                            <option value="" disabled>Select Type</option>
                                            @foreach ($formTypes as $type)
                                                <option @if ($form_type == 'document' && $type == 'document') selected @endif value="{{ $type }}">
                                                    {{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-success">Ajouter</button>

                            </td>
                        </tr>
                        </form>
                    </tbody>
                </table>

            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h2>Documents</h2>
            </div>
            <div class="card-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Form Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($forms as $index => $form)
                            @if ($form['type'] == 'document')
                                <tr>
                                    <td>
                                        <input type="text" wire:model="forms.{{ $index }}.form_title"
                                            class="form-control">

                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm"
                                            wire:click="deleteForm({{ $form['id'] }})">Delete</button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <form wire:submit.prevent="addForm">
                            <input type="hidden" wire:model="newForm.etape_id">
                            <input type="hidden" wire:model="newForm.etape_number">
                            <input type="hidden" wire:model="newForm.fiche_id">
                            <input type="hidden" wire:model="newForm.version_id">
                        <tr>
                            <td>
                                <input required type="text" class="form-control" wire:model="newForm.form_title">


                            </td>
                            <td>
                                <button type="submit" class="btn btn-success">Ajouter</button>

                            </td>
                        </tr>
                        </form>
                    </tbody>
                </table>

            </div>
        </div>
    @endif
   
</div>
