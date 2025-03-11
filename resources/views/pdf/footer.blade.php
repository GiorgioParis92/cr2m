
<page_footer>
   
    <div class="footer_bande">
        {{ $dossier->mar_client->type_societe }}  - RCS {{ $dossier->mar_client->rcs }} <br/>
        {{ $dossier->mar_client->naf }} - N° TVA intracommunautaire {{ $dossier->mar_client->tva_intracomm }}<br/>
 N° d'Agréement Mon Accompagnateur Rénov': {{ $dossier->mar_client->agrement }}
    </div>
    <div class="pagination"><i>Page [[page_cu]]/[[page_nb]]</i></div>
<style>
    .footer_bande {
text-align:center;
font-size:11px;
font-style:italic;
color:grey;
position: relative;
top:20px;
margin-top:20px;
padding-top:20px
}
.pagination {
    position: relative;
    font-size:9px;
    left:90%;
    bottom:100%;
}
page_footer {
    position: absolute;
    top:210px;
    margin-top:35px;
}
    </style>
</page_footer>
