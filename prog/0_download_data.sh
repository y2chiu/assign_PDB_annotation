#!/bin/bash

D_PROG=$(dirname $(readlink -f $0))
D_WORK=$(dirname $D_PROG)
D_DATA=$(dirname $D_PROG)"/0_download"

mkdir -p $D_DATA

wget ftp://ftp.uniprot.org/pub/databases/uniprot/current_release/knowledgebase/complete/uniprot_sprot.fasta.gz -O $D_DATA/uniprot_sprot.fasta.gz
gunzip -df $D_DATA/uniprot_sprot.fasta.gz
wget ftp://ftp.ebi.ac.uk/pub/databases/msd/sifts/text/pdb_chain_uniprot.lst -O $D_DATA/pdb_chain_uniprot.lst
#wget ftp://ftp.ebi.ac.uk/pub/databases/msd/sifts/text/pdb_chain_taxonomy.lst -O $D_DATA/pdb_chain_taxonomy.lst

fn_USEQ=$D_DATA/uniprot_sprot.fasta
fn_UTMP=$D_DATA/.uniprot_sprot.anno_tmp
fn_UANO=$D_DATA/uniprot_sprot.anno

wget ftp://ftp.wwpdb.org/pub/pdb/derived_data/pdb_seqres.txt -O $D_DATA/pdb_seqres.txt

lst_PDB=${D_DATA}'/pdb.id'
lst_PIDA=${D_DATA}'/pdb.idch.all'
lst_PIDP=${D_DATA}'/pdb.idch.pro'
lst_PIDN=${D_DATA}'/pdb.idch.na'
fn_PSEQ=${D_DATA}'/pdb.seqres'


if [ -f $fn_USEQ ]; then
    php $D_PROG/9_getUAC_anno.php $fn_USEQ $fn_UTMP > $fn_UANO
fi

if [ -f $fn_PSEQ ]; then
    # update pdb id with chain list
    cat ${fn_PSEQ} | grep ^">" | cut -d ' ' -f 1 | sed 's#[>_]##g' | sort > ${lst_PIDA}
    cat ${fn_PSEQ} | grep ^">" | grep "mol:protein"    | cut -d ' ' -f 1 | sed 's#[>_]##g' | sort > ${lst_PIDP}
    cat ${fn_PSEQ} | grep ^">" | grep "mol:protein" -v | cut -d ' ' -f 1 | sed 's#[>_]##g' | sort > ${lst_PIDN}
    cat ${lst_PIDA} | cut -b 1-4 | sort -u > ${lst_PDB}
fi


    # Save an empty file with update-date info as its name
    find $D_DATA -type f -name 'VERSION_*' -delete
    touch $D_DATA/VERSION_`date +%Y%m%d`
