<?
    $DIR_PROG = dirname(realpath(__FILE__));
    include($DIR_PROG.'/libcom.php');
    ini_set('memory_limit', '2048M');

    $opt= getOption('l:c:', array('do:'));
    foreach(array('l','c') as $k)
    {
        if(!isset($opt[$k]))
            die(sprintf("\n !! %s -l QUERY_ANNO -c FN_CONTACT\n\n", $argv[0]));
    }

    $FN_QUERY = getRealPath($opt['l']);
    $FN_CT    = getRealPath($opt['c']);

    $contact = array();
    readCT($FN_CT);

    assignLIG($FN_QUERY);

    exit;


    function readCT($fn)
    {
        global $contact;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));
        
        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            if(empty($line))
                continue;

            //pdb100d  A   21 SPM   14 atoms contact   0 residue    0 atom  | EA:    0.000 | A:SPM:21
            //pdb101d  B   25 NT    31 atoms contact   2 residues  12 atoms | EA:    2.155 | B:NT:25
            $d = preg_split('/\s+/', $line);
            list($pdb, $pch, $lrn, $lig, $n_atm,,, $n_ct_res,, $n_ct_atm,,,, $ea) = $d;

            $pdb = substr($pdb, 3, 4);
            $pid = sprintf("%s_%s", $pdb, $pch);

            if(!isset($contact[$pid]))
                $contact[$pid] = array();
            $contact[$pid][] = array($lig, $lrn, $n_ct_res, $ea);
        }
    }


    function assignLIG($fn)
    {
        global $contact;

        if(!file_exists($fn) || ($fp = fopen($fn,'r')) === false)
            die(sprintf(" !! Read file error ($fn).\n\n"));
      
        //$filter = array('GOL','HEM','PEG','BOG');

        $h = array( '#PDB','Chain','Ligand','Uniprot','GN','Protein','Species'
                    ,'Ligand','Ligand','Lig_ResNum','#CT_Res','EA');
        echo join("\t", $h)."\n";

        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            if(empty($line))
                continue;

            //4rpv A P11309 PIM1_HUMAN Homo sapiens PIM1 Serine/threonine-protein kinase pim-1
            $d = explode("\t", $line);
            list($pdb, $pch, $ac, $gid, $os, $gn, $de) = $d;
            $pid = sprintf("%s_%s", $pdb, $pch);

            if(isset($contact[$pid]))
            {
                $out = array($pdb, $pch, $ac, $gn, $de, $os);
                $het = array();
                foreach($contact[$pid] as $h)
                {
                    // TODO: require sorting first

                    list($lig, $lrn, $n_ct_res, $ea) = $h;
                    $lig = trim($lig);
                    $o = array();
                    
                    if(in_array($lig, $filter))
                        continue;

                    if(!isset($het[$lig]))
                    {
                        $o = array_merge($out, $h);
                        array_splice($o, 2, 0, array($lig));
                        echo join("\t", $o)."\n";
                        $het[$lig] = 1;
                    }

                    /*
                    if($pch == $lch && !isset($het[$lig]))
                    {
                        $o = array_merge($out, array($pch, $lig, $lrn));
                        echo join("\t", $o)."\n";
                        $het[$lig] = 1;
                    }
                    else if(!isset($het[$lig]))
                    {
                        $o = array_merge($out, array($lch, $lig, $lrn));
                        echo join("\t", $o)."\n";
                        $het[$lig] = 1;
                    }
                    */
                }

                $k = array_keys($het);
                if(count($k) == 0)
                {
                    $out = array($pdb, $pch, '_NA', $ac, $gn, $de, $os, '_NA','_','_','_');
                    echo join("\t", $out)."\n";
                }

                unset($out, $o);
            }
            else
            {
                    $out = array($pdb, $pch, '_NA', $ac, $gn, $de, $os, '_NA','_','_','_');
                    echo join("\t", $out)."\n";
            }
        }
    }

?>
