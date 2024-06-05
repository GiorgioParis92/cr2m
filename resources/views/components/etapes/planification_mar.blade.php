

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="tab-content">
            @foreach ($forms as $index => $form)
                @if ($form->etape_id == $etape->id)
                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab-{{ $form->id }}"
                        role="tabpanel" aria-labelledby="tab-{{ $form->id }}-tab">
                        <x-form :id="$form->id" :dossierId="$dossier->id" />
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
