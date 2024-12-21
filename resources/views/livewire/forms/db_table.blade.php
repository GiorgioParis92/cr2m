<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
 
    @if($check_condition)
    {{-- <div class="card-header">
        <h5>{{ $conf['title'] ?? '' }}</h5>
    </div> --}}

    
    @php 
    
    $data = '<div class="form-group  col-lg-12">';

$data .= '';
$data .= '<table class="table table-bordered responsive-table table-responsive dataTable no-footer">';
$data .= '<thead>';
$data .= '<th>';
$data .= 'Date';



$data .= '</th>';
$data .= '<th>';
$data .= 'Auditeur';

$data .= '</th>';
$data .= '<th>';
$data .= 'Type de rdv';

$data .= '</th>';
$data .= '<th>';
$data .= 'Statut';

$data .= '</th>';

$data .= '<th>';
$data .= 'Observations';

$data .= '</th>';

$data .= '<th>';
$data .= '';

$data .= '</th>';

$data .= '</thead>';
foreach ($request as $key => $value) {
    $data .= '<tr>';

    $data .= '<td>';
    $data .= date('d/m/Y', strtotime($value['date_rdv']));
    $data .= ' à '.date('H:i', strtotime($value['date_rdv']));
    $data .= '</td>';


    $data .= '<td>';
    $data .= $value['name'];

    $data .= '</td>';


    $data .= '<td>';
    $data .= $value['title'];

    $data .= '</td>';


    $data .= '<td>';
    $data .= $value['rdv_desc'] ? '<div data-rdv_id="' . $value['rdv_id'] . '" class="show_rdv btn btn-'.$value['rdv_style'].'">'.$value['rdv_desc'].'</div>' : '';

    $data .= '</td>';
    $data .= '<td>';
    $data .= $value['observations'] ? '<div >'.$value['observations'].'</div>' : '';

    $data .= '</td>';
    $data .= '<td>';
    $data .= '<div data-rdv_id="' . $value['rdv_id'] . '" class="btn btn-primary show_rdv"><i class="fa fa-eye"></i></div>';

    $data .= '</td>';

    $data .= '</tr>';
}
$data .= '<tr>';

$data .= '<td>';

$data .= '</td>';
$data .= '<td>';

$data .= '</td>';

$data .= '<td>';

$data .= '</td>';


$data .= '<td>';

$data .= '</td>';
$data .= '<td>';

$data .= '</td>';
$data .= '<td>';
$data .= '<div data-rdv_id="" class="btn btn-secondary show_rdv">Ajouter un Rdv </div>';

$data .= '</td>';

$data .= '</tr>';
$data .= '</table>';


$data .= '</div>';
$data .= '<script>';

$data .= '</script>';

echo $data;
    
    @endphp



    @endif



</div>


