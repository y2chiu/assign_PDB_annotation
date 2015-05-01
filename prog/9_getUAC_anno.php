<?
    define('DIR_DATA', dirname(__FILE__));

    if($argc != 3)
        die(sprintf("!! Wrong argv\n"));

    $src_fn = $argv[1];
    $dst_fn = $argv[2];

    // process FASTA file
    $cmd = sprintf("cat %s | grep ^'>' | awk -F'|' '{ print $2,$3 }' > %s", $src_fn, $dst_fn);
    //echo($cmd),"\n";exit;
    system($cmd);


    readUPSP($dst_fn);
    exit;


    function readUPSP($fn)
    {
        if(!file_exists($fn) || ($fp = fopen($fn, 'r')) == NULL)
            die(sprintf(" !! [Error] Can not open file (%s).\n", $fn));

        while(!feof($fp) && ($line = trim(fgets($fp))))
        {
            list($ac, $id, $t) = preg_split('/\s/', $line, 3);
            $data = array('ac'=>$ac,'id'=>$id,'os'=>'','gn'=>'','de'=>'');

            $d = explode('=',$t);
            if(count($d) != 1)
            {
                $data['de'] = substr(array_shift($d),0,-3);
                if(strpos($line,'OS=') !== false)   $data['os'] = substr(array_shift($d),0,-3);
                if(strpos($line,'GN=') !== false)   $data['gn'] = substr(array_shift($d),0,-3);

                //list($gn, $t) = (strpos($t,'PE=') !== false) ? explode('PE=', $t) : array('',$t);
            }

            $data['de'] = str_replace(' / ','/',$data['de']);
            $data['os'] = str_replace(' / ','/',$data['os']);

            echo join("\t", $data), "\n";
            unset($data);
        }

        
        fclose($fp);
    }

?>
