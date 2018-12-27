<?php
/*
 * @AjiKamaludin <aji19kamaludin@gmail.com>
 * @Version 1.0
 * @Package LibEnv
 */

class LibEnv{

    private $envFile = ""; //Env File Resource From fread()
    private $sysPath = ""; //env file path from system
    private $env = []; //env vars from env resouce file
    
    //get dir form system path
    private function getDir()
    {
        $this->sysPath = dirname(__DIR__);
        return $this->sysPath;
    }

    public function __construct($path = null)
    {
        if($path == null){
            $envFile = $this->getDir()."/env";
        }else{
            $envFile = $path."/env";
        }
        
        $this->envFile = fopen($envFile, "r") or die("File ENV tidak ditemukan");
        if(filesize($envFile) == 0){
            die('Environmen Tidak ditemukan');
        }
        $var = fread($this->envFile, filesize($envFile));

        $vars = explode("\n", $var);

        foreach ($vars as $value) {
            if(!empty($value)){
                $fill = explode("=", $value);

                $key = trim($fill[0]);
                if(empty($fill[1])){
                    $val = null;
                }else{
                    $val = trim($fill[1]);
                }
                

                $keys[$key] = $val;
                $_ENV[$key] = $val;

                $envs = $keys;
            }
        }
        if(empty($_ENV)){
            die('Environmen Tidak ditemukan');
        }
        $this->env = $envs;

        return $this;
    }

    public function __desctruct()
    {
        fclose($this->envFile);
    }

}

