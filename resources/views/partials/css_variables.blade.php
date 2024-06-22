@if(isset($cssVariables) && !empty($cssVariables))
    <style>
        :root {
            $cssVariables
        }
    </style>
@endif