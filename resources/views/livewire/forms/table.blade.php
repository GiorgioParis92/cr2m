{{-- </div></div></div>
<div class="card mt-4 pl-4 pr-4 pb-3">
    <div class="card-body p-0">
        <div class="row"> --}}
<div style="" class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
 
    @if($check_condition)

        @php print_r($value) @endphp
        
        {{-- @if($value && !empty($value))
        @foreach($value as $index)

            @foreach($options as $key=> $option)
        
                    @php $option['name']=$conf['name'].'.'.$index.'.'.$key; @endphp
                
                    @if(View::exists('livewire.forms.' . $option['type']) )

                    @livewire("forms.{$option['type']}", ['conf' =>  $option,'form_id'=>$form_id,'dossier_id'=>$dossier_id], key($key))
                
                    @endif

            @endforeach
        
        @endforeach
        @endif --}}

 


    @endif



</div>


