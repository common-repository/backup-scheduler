<?php
/*
Core SedLex Plugin
VersionInclude : 3.0
*/ 

/** =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
* This PHP class creates an export of the database
*/
if (!class_exists("SL_Database")) {
	class SL_Database {
		
		/** ====================================================================================================================================================
		* Constructor
		* 
		* @param string $filter the beginning of the table name for instance "wp3_" for the tables of the blog n°3. If not provided, it will take all tables
		*/

		function __construct($filter="", $sepBlog = "") {
			$this->starttime = microtime(true) ; 
			$this->filter = $filter ; 
			$this->sepBlog = $sepBlog ; 
		}
		
		/** ====================================================================================================================================================
		* Return the progression ratio
		* 
		* @param string $file the sql file that is being created
		* @return string the progress nb_table_extracted/nb_table
		*/
		
		static function progress($file) {
			
			if (is_file($file.".sql.tmp")) {
				// We retrieve the process
				$content = @file_get_contents($file.".sql.tmp") ; 
				list($list_table, $current_index, $current_offset, $nb_entry_total, $nb_entry_current, $sqlfilename, $extension, $all_path_files, $timedebut) = unserialize($content) ;
				return $nb_entry_current."/".$nb_entry_total ; 
			} 
			
			return "" ; 
		}	

		/** ====================================================================================================================================================
		* Reset the process
		* 
		* @param string $path the path in which the database sql file should be created
		* @return void
		*/
		
		static function reset($path) {
			if (is_file($path."/sql_in_progress")) {
				unlink($path."/sql_in_progress") ; 
			}
			$files = scandir($path) ; 
			foreach ($files as $f) {
				// Fichier tmp
				if (preg_match("/^(.*)\.sql\.tmp$/", $f, $match)) {
					unlink($path."/".$f) ; 
					$i = 0 ; 
					while (true) {
						if (is_file($path."/".$match[1]."sql".$i)) {
							unlink($path."/".$match[1]."sql".$i) ; 
							$i ++ ; 
						} else {
							break ; 
						}
					}
				}
			}
		}
		
		/** ====================================================================================================================================================
		* Create the sql file
		* 
		* @param string $sqlfilename the path of the sql file to create
		* @param integer $maxExecutionTime the maximum execution time (in second)
		* @param integer $maxAllocatedMemory the maximum memory allocated by the process (in bytes)
		* @return array with the name of the file (or 'finished' => false and if an error occured see 'error' for the error message)
		*/
		
		function createSQL($sqlfilename, $maxExecutionTime=20, $maxAllocatedMemory=4000000) {
			global $wpdb ; 
			
			$sepBlog = $this->sepBlog ; 

			$extension = array() ; 
			$timedebut=time() ; 
			$all_path_files = array() ; 
			
			$path = dirname($sqlfilename) ; 
			
			// We check that no process is running
			if (is_file($path."/sql_in_progress")) {
				$timestart = @file_get_contents($path."/sql_in_progress")  ;
				if ($timestart===FALSE) {
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/sql_in_progress</code>")) ; 
				}
				$timeprocess = time() - (int)$timestart ; 
				// We ensure that the process has not been started a too long time ago
				return array('finished'=>false, 'error' => sprintf(__("An other process is still running (it runs for %s seconds)", "SL_framework"), $timeprocess), 'for'=>$timeprocess) ; 
			}
			
			// We create a file with the time inside to indicate that this process is doing something
			$r = @file_put_contents(dirname($sqlfilename)."/sql_in_progress", time()) ; 
			if ($r===FALSE) {
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/sql_in_progress</code>")) ; 
			}
			
			// Default value
			$current_index = 0 ; 
			$current_offset = 0 ; 
			$max_size = 10 ; 
			$contentOfTable = "" ; 
				
			// We look if the .sql.tmp file exists, if so, it means that we have to restart the zip process where it stopped
			if (is_file($sqlfilename.".sql.tmp")) {
				// We retrieve the process
				$content = @file_get_contents($sqlfilename.".sql.tmp") ; 
				if ($content===FALSE) {
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$sqlfilename.".sql.tmp</code>")) ; 
				}
				list($list_table, $current_index, $current_offset, $nb_entry_total, $nb_entry_current, $sqlfilename, $extension, $all_path_files, $timedebut) = unserialize($content) ; 
			} else {	
				$list_table = $wpdb->get_results("show tables LIKE '".$this->filter."%'", ARRAY_N);
				$nb_entry_total = 0 ; 
				$nb_entry_current = 0 ; 
				foreach ($list_table as $table) {
					$nb_entry_total += $wpdb->get_var("SELECT COUNT(*) FROM ".$table[0]);
				}
			}
				
			// We create the sql file
			for($i=$current_index ; $i<count($list_table) ; $i++) {
				$table = $list_table[$i] ; 
				
				$nb_response = $max_size ;
				
				$ext_blog = "" ; 
				if (!isset($extension[$ext_blog])) {
					$extension[$ext_blog] = 1 ; 
					$all_path_files[$sqlfilename.$ext_blog.".sql1"] = time() ; 
				}	
				
				while ($nb_response==$max_size) { // We check whether there is no more entry in that table
				
					// Now we retrieve the content.
					if ($current_offset==0) {
					
						// On doit separer les différents blogs
						if ($sepBlog!="") {
							if (preg_match("/^".$sepBlog."([0-9]*)_/",$table[0], $match)) {
								if ($ext_blog != "_blog".$match[1]) {
									$ext_blog = "_blog".$match[1] ; 
									if (!isset($extension[$ext_blog])) {
										$extension[$ext_blog] = 1 ; 
									}	
									if (!is_file($sqlfilename.$ext_blog.".sql".$extension[$ext_blog])) {
										$all_path_files[$sqlfilename.$ext_blog.".sql".$extension[$ext_blog]] = time() ; 
									}
								} 
							} 
						}
						
						$contentOfTable .= "\n\n";
						$contentOfTable .= "-- ===============================================================\n";
						$contentOfTable .= "\n";
						$contentOfTable .= "-- DROP and CREATE ".$table[0]."\n"; 
						$contentOfTable .= "-- -----------------------------\n";
						$contentOfTable .= "DROP TABLE IF EXISTS ".$table[0].";\n";
						$contentOfTable .= $wpdb->get_var("show create table ".$table[0], 1).";";

						$contentOfTable .= "\n\n";
						$contentOfTable .= "-- INSERT INTO ".$table[0]."\n";
						$contentOfTable .= "-- -----------------------------\n\n";
					}
					$lignes = $wpdb->get_results("SELECT * FROM ".$table[0]." LIMIT ".$current_offset.",".$max_size, ARRAY_N);
					
					$nb_response = count($lignes) ;  
										
					foreach ( $lignes as $ligne ) {
						$current_offset ++ ; 
						$nb_entry_current ++ ; 
						$contentOfTable .= "INSERT INTO ".$table[0]." VALUES(";
						for($ii=0; $ii < count($ligne); $ii++) {
							if($ii != 0) 
								$contentOfTable .=  ", ";
							//DATE, TIMESTAMP, TIMESTAMP
							$delimit = "'" ; 
							if ( ($wpdb->get_col_info('type', $ii) == "string") || ($wpdb->get_col_info('type', $ii) == "blob") || ($wpdb->get_col_info('type', $ii) == "datetime") || ($wpdb->get_col_info('type', $ii) == "date") || ($wpdb->get_col_info('type', $ii) == "timestamp") || ($wpdb->get_col_info('type', $ii) == "time") || ($wpdb->get_col_info('type', $ii) == "year") ) {
								if ($ligne[$ii]==NULL) {
									$ligne[$ii]="" ; 
								}
							} else {
								if ($ligne[$ii]==NULL) {
									$ligne[$ii]="NULL" ; 
									$delimit = "" ; 
								}
							}
							$contentOfTable .= $delimit.addslashes($ligne[$ii]).$delimit;
						}
						$contentOfTable .=  ");\n";
						
						// WE SAVE THE DATA in THE FILE
						// We check if the file is too big
						clearstatcache() ; 
						$filesizeSQL = 0 ; 
						if (is_file($sqlfilename.$ext_blog.".sql".$extension[$ext_blog])) {
							$filesizeSQL = filesize($sqlfilename.$ext_blog.".sql".$extension[$ext_blog]) ; 
						}
						if ($maxAllocatedMemory*2/3<strlen($contentOfTable)+$filesizeSQL) {
							$extension[$ext_blog]++ ; 
							$all_path_files[$sqlfilename.$ext_blog.".sql".$extension[$ext_blog]] = time(); 
						}
						// We save the content on the disk and we restart
						$r = @file_put_contents($sqlfilename.$ext_blog.".sql".$extension[$ext_blog], $contentOfTable, FILE_APPEND) ; 
						$contentOfTable = "" ;  
						if ($r===FALSE) {
							return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$sqlfilename.$ext_blog.".sql".$extension[$ext_blog]."</code>")) ; 
						}
						
						// We check that the time is not exceeded
						$nowtime = microtime(true) ; 
						if ($maxExecutionTime!=0) {
							if ($nowtime - $this->starttime > $maxExecutionTime){
								$r = @file_put_contents($sqlfilename.".sql.tmp" ,serialize(array($list_table, $i, $current_offset, $nb_entry_total, $nb_entry_current, $sqlfilename, $extension, $all_path_files, $timedebut))) ; 
								if ($r===FALSE) {
									return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$sqlfilename.".sql.tmp</code>")) ; 
								}
								// we inform that the process is finished
								if (!SLFramework_Utils::rm_rec($path."/sql_in_progress")) {
									return array('finished'=>false, "step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/sql_in_progress"."</code>")) ; 
								}
								return  array('finished'=>false, 'nb_to_finished' => $nb_entry_total-$nb_entry_current, 'nb_finished' => $nb_entry_current, 'info' => $table[0], 'start'=>$timedebut) ; 
							}
						}
					}
				}
				$current_offset=0 ; 
			}
			$filesizeSQL = 0 ; 
			if (is_file($sqlfilename.$ext_blog.".sql".$extension[$ext_blog])) {
				$filesizeSQL = filesize($sqlfilename.$ext_blog.".sql".$extension[$ext_blog]) ; 
			}
			if ($maxAllocatedMemory<strlen($contentOfTable)+$filesizeSQL) {
				$extension[$ext_blog]++ ; 
				$all_path_files[$sqlfilename.$ext_blog.".sql".$extension[$ext_blog]] = time() ; 
			}
			// We complete the tmp files with current content
			$r = @file_put_contents($sqlfilename.$ext_blog.".sql".$extension[$ext_blog] ,$contentOfTable, FILE_APPEND) ; 
			if ($r===FALSE) {
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$sqlfilename.".sql.tmp</code>")) ; 
			}
			// we inform that the process is finished
			if (!SLFramework_Utils::rm_rec($path."/sql_in_progress")) {
				return array('finished'=>false, "step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/sql_in_progress"."</code>")) ; 
			}
			if (!SLFramework_Utils::rm_rec($sqlfilename.".sql.tmp")) {
				return array('finished'=>false, "step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$sqlfilename.".sql.tmp"."</code>")) ; 
			}
			
			return array('finished'=>true, 'nb_to_finished' => 0, 'nb_finished' => $nb_entry_current, 'path' =>  $all_path_files, 'start'=>$timedebut) ; 
			
		}
	} 
}


?>