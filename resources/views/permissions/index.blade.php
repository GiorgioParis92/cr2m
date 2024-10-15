@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Permissions</h1>
        <table class="datatable table table-bordered responsive-table table-responsive dataTable no-footer"
            style="max-width: 100%">
            @csrf
            @php $client_array=[0=>'Ad.',1=>'Mar',3=>"Ins."] @endphp
            <thead>
                <tr>
                    <th>Type d'utilisateur</th>
                    @foreach ($etapes as $etape)
                        <th style="text-align:center;max-width: 5%;" colspan="{{ count($client_array) }}">
                            <div style="    text-wrap: wrap;">{{ $etape->etape_desc }}</div>
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <td>Type de client</td>

                    @foreach ($etapes as $etape)
                        @foreach ($client_array as $key => $client)
                            <th>{{ $client }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($user_types as $type)
                    @if ($type->id > 1)
                        <tr>
                            <td>{{ $type->type_desc }}</td>
                            @foreach ($etapes as $etape)
                                @foreach ($client_array as $key => $client)
                                    <td style="text-align:center">

                                        @if (
                                            (isset($permission_array[$etape->etape_name][$type->id][$key]) &&
                                                $permission_array[$etape->etape_name][$type->id][$key] == 1) ||
                                                !isset($permission_array[$etape->etape_name][$type->id][$key]))
                                            <i data-new_value="0" onclick="update_permission(this, '{{ $etape->etape_name }}', '{{ $type->id }}', '{{ $key }}', $(this).attr('data-new_value'))" class="fa fa-circle-check text-success"></i>

                                        @endif
                                        @if (isset($permission_array[$etape->etape_name][$type->id][$key]) &&
                                                $permission_array[$etape->etape_name][$type->id][$key] == 0)
                                            <i data-new_value="1" onclick="update_permission(this, '{{ $etape->etape_name }}', '{{ $type->id }}', '{{ $key }}', $(this).attr('data-new_value'))" class="fa fa-circle-xmark text-danger"></i>

                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>


@endsection

    <script>
    function update_permission(element, name, type_user, type_client, value) {
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
                if ($(element).hasClass('text-danger')) {
                    $(element).removeClass('text-danger fa-circle-xmark')
                              .addClass('text-success fa-circle-check')
                              .attr('data-new_value', '1');
                } else if ($(element).hasClass('text-success')) {
                    $(element).removeClass('text-success fa-circle-check')
                              .addClass('text-danger fa-circle-xmark')
                              .attr('data-new_value', '0');
                }
            }
        });
    }

</script>
