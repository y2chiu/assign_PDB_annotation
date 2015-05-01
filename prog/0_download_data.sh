
DIR_PROG=$(dirname $(readlink -f $0))
DIR_DATA=$(dirname $DIR_PROG)"/0_download"

mkdir -p $DIR_DATA

wget ftp://ftp.uniprot.org/pub/databases/uniprot/current_release/knowledgebase/complete/uniprot_sprot.fasta.gz -O $DIR_DATA/uniprot_sprot.fasta.gz
gunzip -df $DIR_DATA/uniprot_sprot.fasta.gz
wget ftp://ftp.ebi.ac.uk/pub/databases/msd/sifts/text/pdb_chain_uniprot.lst -O $DIR_DATA/pdb_chain_uniprot.lst
#wget ftp://ftp.ebi.ac.uk/pub/databases/msd/sifts/text/pdb_chain_taxonomy.lst -O $DIR_DATA/pdb_chain_taxonomy.lst

fin=$DIR_DATA/uniprot_sprot.fasta
fo1=$DIR_DATA/.uniprot_sprot.anno_tmp
fo2=$DIR_DATA/uniprot_sprot.anno

if [ -f $fin ]; then
    php $DIR_PROG/9_getUAC_anno.php $fin $fo1 > $fo2
fi
