<div wire:poll="refresh" class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">

    @php

        $data = '';
        if (!$this->value) {
            $data .= '<div>';
            $data .= '<div  class="btn btn-primary" onclick="sendApiRequest(this)" ';
         
            $all_data = load_all_dossier_data($dossier);

            foreach ($all_data as $data_key => $data_value) {
                foreach ($data_value as $k => $v) {
                    foreach ($v as $a => $b) {
                        if (!empty($b)) {
                            $array[$a] = $b;
                        }

                    }
                }
            }
            foreach ($options['fields'] as $k => $v) {
                $val = '';
                if (!empty($v)) {
                    if (is_array($v)) {
                        foreach ($v as $cle => $valeur) {
                            $val .= $array[$valeur] . ' ';
                        }
                    } else {
                        if (!empty($array[$v])) {
                            $val = $array[$v];
                        } else {
                            $val = $v;
                        }
                    }
                } else {
                    $val = $array[$k] ?? '';
                }

                $data .= 'data-';
                $data .= $k;
                $data .= '="';
                $data .= $val;
                $data .= '"';
            }

            if (isset($options['files'])) {
                $data_files = '';
                foreach ($options['files'] as $files) {
                    if (isset($array[$files])) {
                        $data_files .= ($array[$files] ?? '') . ';';
                    }
                }
                $data .= 'data-';
                $data .= 'files';
                $data .= '="';
                $data .= $data_files;
                $data .= '"';
            }

            $data .= 'data-';
            $data .= 'form_id';
            $data .= '="';
            $data .= $form_id;
            $data .= '"';

            $data .= '>';
            $data .= $conf['title'];
            $data .= '</div>';
            $data .= '</div>';
        } else {
            $data .= '<div>';
            $data .=
                '<a class="btn btn-success " target="_blank" href="https://crm.elitequalityinspection.fr/demandes/' .
                $value .
                '">Voir la demande d\'inspection sur ELITE';

            $data .= '</a>';
            $data .= '</div>';
        }
        echo $data;
   
    @endphp
    

</div>
