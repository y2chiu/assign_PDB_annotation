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
    $FN_TAXONOMY = sprintf('%s/pdb_chain_taxonomy.lst', $DIR_DATA);
   
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

            $pid  = preg_split('/\s+/',$line);
            $pid  = array_shift($pid);

            if(preg_match("/[0-9]\w{3}_\w/",$pid) != 1)
            {
                fprintf(STDERR, " # Error, input LIST format error (%s)\n", $pid);
                exit;
            }

            $o = array();
            if(isset($map_uniprot[$pid]))
            {
                $ac = $map_uniprot[$pid];
                $ac = explode(',',$ac);

                foreach($ac as $i=>$k)
                {
                    $ano = array('-','-','-','-','-');

                    if(isset($map_uac_anno[$k]))
                        $ano = array_values($map_uac_anno[$k]);

                    $ano[0] = $k;
                    $o = array_merge(array($pid), $ano);

                    echo ($i==0) ? '' : '@';
                    echo join("\t", $o)."\n";
                }
            }
            else
            {
                $ano = array('-','-','-','-','-');
                $o = array_merge(array($pid), $ano);

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

            if(isset($map_uniprot[$pid]))
                $map_uniprot[$pid] = sprintf('%s,%s',$map_uniprot[$pid],$uac);
            else
                $map_uniprot[$pid] = $uac;

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
