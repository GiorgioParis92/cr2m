<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" >

    <title>grille_analyse</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        p {
            color: black;
            font-family: "Arial", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            margin: 0pt;
        }

        .s1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 14pt;

        }

        .s2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 11pt;
         
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 10pt;
        }

        .s4 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
        }

        .s5 {
            color: black;
            font-family: "Arial", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
        }

        .s6 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s7 {
            color: black;
            font-family: "Arial", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            vertical-align: -1pt;
        }

        .s8 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9pt;
        }

        .s9 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 11pt;
            vertical-align: -6pt;
        }

        .s10 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
        }

        .s11 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            vertical-align: -2pt;
        }

        .s12 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5.5pt;
            vertical-align: 1pt;
            margin-top:10px;
            margin-bottom:5px;
        }

        .s13 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            vertical-align: 1pt;
        }

        .s14 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            vertical-align: 2pt;
        }

        .s15 {
            color: black;
            font-family: "Arial", monospace;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 12pt;
            vertical-align: 2pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }

        img {
            width:100%;
            max-width:100%;
        }
        .page-break {
        page-break-before: always;
        
    }
    .radio_line {
    display: inline-block;
    margin-right: 20px;
    background: #8392ab;
    padding-right: 10px;
    padding-left: 5px;
    border-radius: 7px;
    line-height: normal;
    padding-top: 3px;
    margin-bottom: 11px;
}

.form_title {
    background:#EDEDED;
    width:100%;
    margin:auto;
    text-align:center;
    padding:20px;
    font-size:13px;
}
@page {
            margin-top: 40px;
            margin-right: 30px;
            margin-bottom: 40px;
            margin-left: 30px;
        }
    </style>
</head>

<body>


 
       
            {{-- <p style="padding-top: 2pt; text-indent: 0pt; text-align: left;"><br /></p>
            <p class="s1" style="background:#EDEDED; display:block; height:69.4pt; width:481.9pt;">
                {{$title}}
            </p> --}}

            <table style="margin:auto;width:90%;border-collapse: collapse;margin-top:20px">
                <tr><td class="s1 form_title" style="font-size:18px">{{$title}}</td></tr>
            </table>

            <div>
                {!! nl2br($content) !!}
                
                    
          
                  
                </div>
   
   

</body>

</html>
