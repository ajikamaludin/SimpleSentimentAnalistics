<?php

function dd($var, $type = null){
    if($type == "json"){
        header('Content-Type: application/json');
        echo json_encode($var);
    }else if($type == null){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
    die();
}

function setJs($script)
{
    $_SESSION['js'] = "<script>". $script ."</script>";
}

function js(){
    $js = "";
    if(isset($_SESSION['js'])){
        $js = $_SESSION['js'];    
    }
    unset($_SESSION['js']);
    return $js;
}