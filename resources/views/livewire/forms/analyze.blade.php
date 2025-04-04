<div>
    @if($value)
    @if(isset($invalidGroups) && !empty($invalidGroups))
    <table style="width:100%" class="table datatable table-bordered responsive-table dataTable no-footer">
        <thead>
            <th>
                Vérification
            </th>

            <th>
                Documents
            </th>
            <th>
                Valeurs
            </th>
        </thead>
        @foreach($invalidGroups as $group)
        <tr>
            <td>{{ $group['error'] }}</td>
            <td>
                @foreach($group['groups'] as $item)
                    <li>
            
                        {{ implode(', ', $item['documentIds']) }}
                      
                    </li>
                @endforeach
            </td>

            <td>
                @foreach($group['groups'] as $item)
                    <li>
                    {{ $item['value'] }}
                    </li>
                @endforeach
            </td>

        </tr>
    @endforeach

    </table>
    @else
        Aucun écart noté dans la comparaison des documents par OCEER
    @endif


    @if(isset($ValidGroups) && !empty($ValidGroups))
    <table style="width:100%" class="table datatable table-bordered responsive-table dataTable no-footer">
        <thead>
            <th>
                Vérification
            </th>

            <th>
                Documents
            </th>
            <th>
                Valeurs
            </th>
        </thead>
        @foreach($ValidGroups as $group)
        <tr>
            <td>{{ $group['error'] }}</td>
            <td>
                @foreach($group['groups'] as $item)
                    <li>
            
                        {{ implode(', ', $item['documentIds']) }}
                      
                    </li>
                @endforeach
            </td>

            <td>
                @foreach($group['groups'] as $item)
                    <li>
                    {{ $item['value'] }}
                    </li>
                @endforeach
            </td>

        </tr>
    @endforeach

    </table>
    @else
        Aucun écart noté dans la comparaison des documents par OCEER
    @endif

    @endif

</div>
