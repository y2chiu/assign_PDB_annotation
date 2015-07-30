Assign Uniport annotations of PDBID list 
=============================================

##Reqiured
getLigand: https://github.com/y2chiu/getLigand

##Steps

1. Download/update required annotation data
  ```
  sh prog/0_download_data.sh
  ```

2. Prepare the PDB CODE LIST file
  >INPUT FORMAT  
  >ID 
  ```
  4rpv
  3dog
  4r3c
  ```

4. Run COMMAND
  ```
  sh prog/update_pro-lig-complex.sh [PDBID LIST]
  ```

  Example:
  ```
  sh prog/update_pro-lig-complex.sh example/example.input.txt
  ```

  Output:
  ```
  example/
  |-- example.input.29str.txt     // including PDB, UniProt  
  |-- example.input.32complex.txt // including PDB, Ligand, UniProt  
  |-- example.input.ct.txt        // including PDB, Ligand  
  `-- example.input.txt           // input list  
  ```
