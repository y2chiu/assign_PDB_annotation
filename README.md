Assign Uniport annotations of PDBID list 
=============================================

##Steps

1. Download/update required annotation data
  ```
  sh prog/0_download_data.sh
  ```

2. Prepare the PDBID LIST file
  >INPUT FORMAT  
  >ID_CHAIN  
  ```
  4rpv_A
  3dog_A
  4r3c_A
  ```

4. Run COMMAND
  ```
  php prog/1_assign_PDB_anno.php -l [PDBID LIST]
  ```

  Example:
  ```
  php prog/1_assign_PDB_anno.php -l example/example.input.txt
  ```

  Output:
  ```
  4rpv_A  P11309  PIM1_HUMAN      Homo sapiens    PIM1    Serine/threonine-protein kinase pim-1
  3dog_A  P24941  CDK2_HUMAN      Homo sapiens    CDK2    Cyclin-dependent kinase 2
  4r3c_A  Q16539  MK14_HUMAN      Homo sapiens    MAPK14  Mitogen-activated protein kinase 14
  2vuw_A  -       -       -       -       -
  3cs9_A  P00519  ABL1_HUMAN      Homo sapiens    ABL1    Tyrosine-protein kinase ABL1
  1m17_A  P00533  EGFR_HUMAN      Homo sapiens    EGFR    Epidermal growth factor receptor
  2ity_A  P00533  EGFR_HUMAN      Homo sapiens    EGFR    Epidermal growth factor receptor
  2itx_A  P00533  EGFR_HUMAN      Homo sapiens    EGFR    Epidermal growth factor receptor
  2itw_A  P00533  EGFR_HUMAN      Homo sapiens    EGFR    Epidermal growth factor receptor
  3rcd_A  P04626  ERBB2_HUMAN     Homo sapiens    ERBB2   Receptor tyrosine-protein kinase erbB-2
  3pp0_A  P04626  ERBB2_HUMAN     Homo sapiens    ERBB2   Receptor tyrosine-protein kinase erbB-2
  4riw_A  P21860  ERBB3_HUMAN     Homo sapiens    ERBB3   Receptor tyrosine-protein kinase erbB-3
  3bbt_B  Q15303  ERBB4_HUMAN     Homo sapiens    ERBB4   Receptor tyrosine-protein kinase erbB-4
  2r4b_A  Q15303  ERBB4_HUMAN     Homo sapiens    ERBB4   Receptor tyrosine-protein kinase erbB-4
  ```
