<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * Description of Installer
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Installer {
    
    public function upgrade_version_sort($a, $b) {
        return version_compare($a['version'], $b['version']);
    }

    /**
     * 
     * @param type $wpdb
     * @param WC_Eabi_Postoffice $model
     * @param type $filePath
     * @param type $configPrefix
     * @throws Exception
     */
    public function install($wpdb, $model, $filePath, $configPrefix) {
        

        if (version_compare($model->getVersion(), get_option($configPrefix . 'db_version')) > 0) {
            //perform the install or upgrade
            $oldVersion = get_option($configPrefix . 'db_version');
            
            //gather list of files from specific directory
            $sqlFilesDirectory = $filePath . '/includes/sql';
            $list = array();
            
            if (file_exists($sqlFilesDirectory) && ($files = scandir($sqlFilesDirectory))) {
                foreach ($files as $file) {
                if (!in_array($file, array('.', '..', '.svn', 'index.php')) && preg_match('/\.php$/', $file)) {
                    
                    $tab = explode('-', $file);
                    
                    if (!isset($tab[1])) {
                        continue;
                    }
                    
                    $file_version = basename($tab[1], '.php');
                    
                    
                        if (count($tab) == 2 
                                && version_compare($file_version, $model->getVersion(), '<=') 
                                && version_compare($file_version, $oldVersion, '>')) {
                            $list[] = array(
                                'file' => $sqlFilesDirectory . '/' . $file,
                                'version' => $file_version,
                            );
                        }
                    }
                }

                //sort the list
                usort($list, array($this, 'upgrade_version_sort'));
                
                $upgradedVersion = false;

                //now we have a list
                
                foreach ($list as $item) {
                    $this->_doUpgrade($item['file'], $wpdb, $model);

                    update_option($configPrefix . 'db_version', $item['version']);
                    $upgradedVersion = $item['version'];
                }
                if ($upgradedVersion) {
                    //no notices available
//                    $model->add_notice(sprintf(__('Upgraded to version %s'), $upgradedVersion));
                }
            }
        } else {
            

            
        }
        
        
    }
        protected function _doUpgrade($file, $wpdb, $model) {
           
           require $file;
        }
    
    
    
    
}
