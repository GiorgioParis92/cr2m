<div>

    <table style="width:100%" class="table  no-footer">
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


</div>
