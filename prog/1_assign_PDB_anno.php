<?
    $DIR_PROG = dirname(realpath(__FILE__));
    include($DIR_PROG.'/libcom.php');
    ini_set('memory_limit', '2048M');

    $opt= getOption('l:', array());
    foreach(array('l') as $k)
    {
        if(!isset($opt[$k]))
            die(sprintf("\n !! %s -l PDB_LIST\n\n", $argv[0]));
    }

    $FN_LIST  = $opt['l'];

    $DIR_DATA    = sprintf('%s/0_download/', dirname($DIR_PROG));
    $FN_UAC_ANNO = sprintf('%s/uniprot_sprot.anno', $DIR_DATA);
    $FN_UNIPROT  = sprintf('%s/pdb_chain_uniprot.lst', $DIR_DATA);
    //$FN_TAXONOMY = sprintf('%s/pdb_chain_taxonomy.lst', $DIR_DATA);
   
    $map_uniprot  = array();
    $map_taxonomy = array();
    $map_uac_anno = array();



    readUNIPROT($FN_UNIPROT);
    //readTAXONOMY($FN_TAXONOMY);

    readUAC_ANNO($FN_UAC_ANNO);
    readPDB_LIST($FN_LIST);

    exit;


    function readUAC_ANNO($fn)
    {
        global $map_uniprot, $map_uac_anno;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));
       
        $key = array('ac','id','os','gn','de');
        while(!feof($fp))
        {
            $line = trim(fgets($fp));

            if(empty($line) || $line[0] == '#')
                continue;

            //Q6GZX4  001R_FRG3G      Frog virus 3 (isolate Goorha)   FV3-001R        Putative transcription factor 001R
            //Q6GZX3  002L_FRG3G      Frog virus 3 (isolate Goorha)   FV3-002L        Uncharacterized protein 002L
            list($ac,$id,$os,$gn,$de) = explode("\t", $line);

            // keys were stored in readUNIPROT
            if(isset($map_uac_anno[$ac]))
            {
                $map_uac_anno[$ac] = array_combine($key, array($ac,$id,$os,$gn,$de));
            }
        }

        //print_r($map_uac_anno);
    }



    function readPDB_LIST($fn)
    {
        global $map_uniprot, $map_uac_anno, $map_taxonomy;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));
        
        $uniq  = array();
        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            
            if(empty($line) || $line[0] == '#')
                continue;

            $id  = preg_split('/\s+/',$line);
            $id  = array_shift($id);

            if(preg_match("/[0-9]\w{3}/",$id) != 1)
            {
                fprintf(STDERR, " # Error, input LIST format error (%s)\n", $id);
                exit;
            }

            $o = array();
            if(isset($map_uniprot[$id]))
            {
                $pids = array_keys($map_uniprot[$id]);

                foreach($pids as $pid)
                {
                    list($pdb, $pch) = explode('_', $pid);
                    $ac = explode(',',$map_uniprot[$id][$pid]);

                    foreach($ac as $i=>$k)
                    {
                        $ano = array('-','-','-','-','-');
   
                        if(isset($map_uac_anno[$k]))
                        {
                            foreach(array_values($map_uac_anno[$k]) as $j => $v)
                                if(!empty($v)) $ano[$j] = $v;
                        }

                        $ano[0] = $k;
                        $o = array_merge(array($pdb, $pch), $ano);

                        echo ($i==0) ? '' : '@';
                        echo join("\t", $o)."\n";
                    }
                }
            }
            else
            {
                $ano = array('_','-','-','-','-','-');
                $o = array_merge(array($id), $ano);

                echo join("\t", $o)."\n";
            }

        }

        fclose($fp);
    }



    function readUNIPROT($fn)
    {
        global $map_uniprot, $map_uac_anno;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));

        fprintf(STDERR, " # Reading %s\n", basename($fn));

        $map_uniprot  = array();
        $map_uac_anno = array();

        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            if(empty($line) || $line[0] == '#' || $line[0] == 'P')
                continue;
            
            $d = explode("\t",$line);
            list($pdb, $pch, $uac) = $d;
            $pid = sprintf('%s_%s', $pdb, $pch);
            $key = sprintf('%s', $pdb);

            if(!isset($map_uniprot[$key]))
                $map_uniprot[$key] = array();

            if(isset($map_uniprot[$key][$pid]))
                $map_uniprot[$key][$pid] = sprintf('%s,%s',$map_uniprot[$key][$pid],$uac);
            else
                $map_uniprot[$key][$pid] = $uac;

            $map_uac_anno[$uac] = array();
        }
        
        fclose($fp);
    }



    function readTAXONOMY($fn)
    {
        global $map_taxonomy;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));

        fprintf(STDERR, " # Reading %s\n", basename($fn));

        $map_taxonomy  = array();
        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            if(empty($line) || $line[0] == '#' || $line[0] == 'P')
                continue;
            
            $d = explode("\t",$line);
            list($pdb, $pch, $tax, $spices) = $d;
            $pid = sprintf('%s_%s', $pdb, $pch);

            if($tax == 9606 && !isset($taxonomy[$pid]))
                $map_taxonomy[$pid] = 9606; //$spices;
        }
        
        fclose($fp);
    }
?>
