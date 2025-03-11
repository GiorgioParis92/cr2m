@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                @livewire('edit-etape', ['id' => $id])
            </div>
            <div class="row">

                <div class="col-lg-6 col-sm-6">
                    @livewire('edit-etape-forms', ['id' => $id,'form_type'=>'forms'])

                </div>
                <div class="col-lg-6 col-sm-6">
                    @livewire('edit-etape-forms', ['id' => $id,'form_type'=>'document'])
                </div>
            </div>
        </div>
    </div>
@endsection
