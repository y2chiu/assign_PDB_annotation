#!/bin/bash

if [ $# -ne 1 ] ; then
	echo -e "\nERROR: Missing arguments!!"
	echo -e "USAGE: $0 PDB_CODE_LIST\n"
	exit 1
elif [ ! -f $1 ] ; then 
	echo -e "\nERROR: No such directory!!"
	echo -e "USAGE: $0 PDB_CODE_LIST\n"
	exit 1
else

    D_PROG=$(dirname $(readlink -f $0))
    D_WORK=$(dirname $D_PROG)
   
    D_LOG=${D_WORK}"/.log"
    if [ ! -d ${D_LOG} ]; then
        mkdir -p ${D_LOG}
    fi

    f_lst=$(readlink -f $1)
    f_pat=$(echo $f_lst | sed "s/\.txt//")
    f_str="$f_pat.str.txt"
    f_lig="$f_pat.ct.txt"
    f_cpx="$f_pat.complex.txt"

    dir_pdb="/data/public/pdb/pdb/"

    echo "#0. date: `date +%Y%m%d`"
    cd $D_WORK;
    #echo "#0. download required files"
    #sh $D_PROG/0_download_data.sh | tee $D_LOG/0_download.log 2>&1
    echo "#1. get UniProt annotation"
    php $D_PROG/1_assign_PDB_anno.php -l $f_lst >$f_str 2>$D_LOG/1_assign_PDB_anno.log

    echo "#2. get ligands from PDB CODE list"
    cat $f_str | cut -f 1 | xargs -I{} echo "$D_PROG/tools/getLigand -i $dir_pdb/pdb{}.ent" \
        >.torun_getligand.sh
    sh .torun_getligand.sh >$f_lig 2>$D_LOG/2_get_ligand.log

    echo "#3. assign PDB complexes"
    php $D_PROG/3_assign_LIG.php -l $f_str -c $f_lig >$f_cpx 2>$D_LOG/3_assign_complex.log

    n_str=$(cat $f_str | wc -l)
    n_cpx=$(cat $f_cpx | wc -l)

    echo "#4. done."
    echo "  $n_str structures"
    echo "  $n_cpx complexes"
    mv $f_str $f_pat.${n_str}str.txt
    mv $f_cpx $f_pat.${n_cpx}complex.txt


    # Save an empty file with update-date info as its name
    find ${D_WORK} -type f -name 'VERSION_*' -delete
    touch ${D_WORK}/VERSION_`date +%Y%m%d`
fi

