<div >
    @php
        $content = nl2br($options['prompt'], false);
        $content=str_replace('<br/>',"\n", $content);

        $data = '<div style="margin-bottom:20px" class="form-group  col-sm-12 '.($conf['class'] ?? "").'">';
        $data .= '<label>'.$conf['title'].'</label>';
        $data .= '<textarea  style="min-height:500px" class="form-control" id="'.$conf['name'].'" name="'.$conf['name'].'">'.$content.'</textarea>';

        $data .= '</div>';
        
        echo $data;
    @endphp
</div>


