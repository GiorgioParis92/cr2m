@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Permissions</h1>

        @php
            $client_array = [0 => 'Ad.', 1 => 'Mar', 3 => 'Ins.'];
            $etapes_chunks = $etapes->chunk(6);
        @endphp

        @foreach($etapes_chunks as $chunk)
            <table class="table table-bordered responsive-table table-responsive no-footer" style="max-width: 100%">
                @csrf
                <tr>
                    <th>Type d'utilisateur</th>
                    @foreach ($chunk as $etape)
                        <th style="text-align:center;max-width: 5%;" colspan="{{ count($client_array) }}">
                            <div style="text-wrap: wrap;">{{ $etape->etape_icon}} - {{ $etape->etape_desc }}</div>
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <td>Type de client</td>
                    @foreach ($chunk as $etape)
                        @foreach ($client_array as $client)
                            <th>{{ $client }}</th>
                        @endforeach
                    @endforeach
                </tr>

                @foreach ($user_types as $type)
                    @if ($type->id > 1)
                        <tr>
                            <td>{{ $type->type_desc }}</td>
                            @foreach ($chunk as $etape)
                                @foreach ($client_array as $key => $client)
                                    <td style="text-align:center">
                                        @if (
                                            (isset($permission_array[$etape->etape_name][$type->id][$key]) &&
                                                $permission_array[$etape->etape_name][$type->id][$key] == 1) ||
                                                !isset($permission_array[$etape->etape_name][$type->id][$key]))
                                            <i data-name="{{ $etape->etape_name }}" data-type_user="{{ $type->id }}"
                                                data-type_client="{{ $key }}" onclick="update_permission(this)"
                                                class="fa fa-circle-check text-success"></i>
                                        @endif
                                        @if (isset($permission_array[$etape->etape_name][$type->id][$key]) &&
                                                $permission_array[$etape->etape_name][$type->id][$key] == 0)
                                            <i data-name="{{ $etape->etape_name }}" data-type_user="{{ $type->id }}"
                                                data-type_client="{{ $key }}" onclick="update_permission(this)"
                                                class="fa fa-circle-xmark text-danger"></i>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </table>
        @endforeach
    </div>
@endsection

<script>
    function update_permission(element) {
        var name = $(element).data('name');
        var type_user = $(element).data('type_user');
        var type_client = $(element).data('type_client');

        // Determine the new value based on the current class
        var value = $(element).hasClass('text-success') ? '0' : '1';

        var token = '{{ auth()->user()->api_token }}';

        $.ajax({
            url: '/api/update_permission', // Adjust this URL to your actual API endpoint
            type: 'POST',
            data: {
                name: name,
                type_user: type_user,
                type_client: type_client,
                value: value
            },
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                // Toggle classes
                if ($(element).hasClass('text-danger')) {
                    $(element).removeClass('text-danger fa-circle-xmark')
                        .addClass('text-success fa-circle-check');
                } else if ($(element).hasClass('text-success')) {
                    $(element).removeClass('text-success fa-circle-check')
                        .addClass('text-danger fa-circle-xmark');
                }
            }
        });
    }
</script>
