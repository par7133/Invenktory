<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Invenktory.
 *
 * Invenktory is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Invenktory is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Invenktory. If not, see <https://www.gnu.org/licenses/>.
 *
 * home.php
 * 
 * Invenktory home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode      
 */
 
 $cmdHistory = [];
 $cmd = PHP_STR;
 $opt = PHP_STR;
 $param1 = PHP_STR;
 $param2 = PHP_STR;
 $param3 = PHP_STR;
 
 $cmdRecallHistory = [];
  
 $editBoard = [];
  
 $editBoardParams = [];
  
 function showHistory() {
   global $cmdHistory;
   $i = 1;	 
   foreach($cmdHistory as $val) {
	 echo(str_replace("\n", "<br>", $val));
	 $i++;   
   }
 }
 
function updateHistory(&$update, $maxItems) {
   global $cmdHistory;
   // Making enough space in $cmdHistory for the update..
   $shift = (count($cmdHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $cmdHistory = array_slice($cmdHistory, $shift, $maxItems); 
   }		  
   // Adding $cmdHistory update..
   if (count($update) > $maxItems) {
      $beginUpd = count($update) - ($maxItems-1);
   } else {
	  $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	 $cmdHistory[] = $val;   
   }
   // Writing out $cmdHistory on disk..
   $filepath = dirname(__DIR__) . PHP_SLASH . "logs" . PHP_SLASH . ".INV_history";
   file_put_contents($filepath, implode('', $cmdHistory));	 
 }
 
 function loadRecallHistory() { 
	global $cmdRecallHistory; 
	$tmpcmdRecallHistory = file(dirname(__DIR__) . PHP_SLASH . "logs" . PHP_SLASH . ".INV_Recallhistory");
	foreach($tmpcmdRecallHistory as $val) {
	  $cmdRecallHistory[left($val, strlen($val)-1)]=$val;  	
    } 
 }	 
	  
 function updateRecallHistory($update, $maxItems) {
   global $cmdRecallHistory;
   
   if (!array_key_exists($update, $cmdRecallHistory)) {
	 // Making enough space in $cmdHistory for the update..
	 $shift = (count($cmdRecallHistory) + 1) - $maxItems;
	 if ($shift > 0) {
  	   $cmdRecallHistory = array_slice($cmdRecallHistory, $shift, $maxItems); 
	 }
	 
	 $cmdRecallHistory[$update] = $update . "\n";
   }
   		     
   // Writing out $cmdRecallHistory on disk..
   $filepath = dirname(__DIR__) . PHP_SLASH . "logs" . PHP_SLASH . ".INV_Recallhistory";
   file_put_contents($filepath, implode('', $cmdRecallHistory));	 
 }	 

 function updateHistoryWithErr(string $err, bool $withCommand = true) 
 {
   global $prompt;
   global $command;
   	 
   $output = [];  
   if ($withCommand) {
     $output[] = $prompt . " " . $command . "\n";
   }
   $output[] = "$err\n";
   updateHistory($output, HISTORY_MAX_ITEMS);  	 
 }	 	 
 
 function myExecCommand() {
   global $prompt;
   global $command;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($command, $output);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }
 
 function myExecCPCommand() {
   global $prompt;
   global $command;
 
   $realCommand = str_replace("cp", "cp -Rp", $command);
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($realCommand, $output);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }
 
 
 function myExecCopy() {
   global $prompt;
   global $command;
   global $param1;
   global $param2;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   copy($param1, $param2);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function myExecCDFolderCommand() {
   global $prompt;
   global $command;
   global $param1;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $newPath = $curPath . PHP_SLASH . $param1;
   chdir($newPath);

   $curPath = $newPath;
   $curDir = $param1;
 
   // Creating the Download folder if doesn't exist..
   $downloadPath = $curPath . PHP_SLASH . ".HCdownloads";
   if (!file_exists($downloadPath)) {
	 //copy(APP_INV_PATH . PHP_SLASH . ".HCsampledir", $downloadPath);  
     $mycmd = "cp -Rp " . APP_INV_PATH . PHP_SLASH . ".HCsampledir" . " " . $downloadPath;
     $myret = exec($mycmd);
   }
 
   $prompt = str_replace("$1", $curDir, APP_PROMPT);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }
 
 function myExecCDBackwCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $ipos = strripos($curPath, PHP_SLASH);
   $newPath = substr($curPath, 0, $ipos);
   chdir($newPath);

   $curPath = getcwd();
   $ipos = strripos($curPath, PHP_SLASH);
   $curDir = substr($curPath, $ipos);
 
   $prompt = str_replace("$1", $curDir, APP_PROMPT);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function myarray_filter_cb($val) {
   return isset($val);  
 }

 function myExecLSCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
   $downloadPath = $curPath . PHP_SLASH . ".HCdownloads";
 
   $realCommand = "ls -a";
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($realCommand, $output);
   
   // Creating the Download path for the current folder..
   /*
   if (!file_exists($downloadPath)) {
     //copy(APP_INV_PATH . PHP_SLASH . ".HCsampledir", $downloadPath);
     $mycmd = "cp -Rp " . APP_INV_PATH . PHP_SLASH . ".HCsampledir" . " " . $downloadPath;
     $myret=exec($mycmd);
   }
   
   // Cleaning the Download folder..
   if (file_exists($downloadPath)) {
	   $files1 = scandir($downloadPath);
	   foreach($files1 as $file) {
		   if (!is_dir($downloadPath . PHP_SLASH . $file) && $file !== "." && $file !== "..") {
		     unlink($downloadPath . PHP_SLASH . $file);
		   }	     
	   }
   }*/
   
   // Update history..
   foreach ($output as &$val) {
	   if ($val === $prompt . " " . $realCommand . "\n") {
     } else if ($val === "." || $val === "..") {
       $val = null; 
     } else {	   
       if (right($val,1)==="\n") {
         $val = left($val, strlen($val)-1);
       }  
       
       // Creating the tmp download for the file entry and generating the virtual path..
       /*
       $virtualPath = PHP_STR;
       if (file_exists($downloadPath)) {
         if (!is_dir($curPath . PHP_SLASH . $val) && filesize($curPath . PHP_SLASH . $val)<=651000) {
           $fileext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
           if ($fileext === "php" || $fileext === "inc") {
             copy($curPath . PHP_SLASH . $val, $downloadPath . PHP_SLASH . $val . ".hcd");  
             $virtualPath = getVirtualPath($downloadPath . PHP_SLASH . $val . ".hcd");			   	 
             } else {
             copy($curPath . PHP_SLASH . $val, $downloadPath . PHP_SLASH . $val);  
             $virtualPath = getVirtualPath($downloadPath . PHP_SLASH . $val);			   
           }	 
         }
       } else {
         $virtualPath=PHP_STR;
       }      
       if ($virtualPath!==PHP_STR) {
         $val = "<a href='$virtualPath'>" . $val . "</a>\n";   	     
       } else {
         $val = $val . "\n";
       }
       */
       
       $val = $val . "\n";
     }      	         
   }	 
   
   $output = array_filter($output, "myarray_filter_cb");
   
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function myExecHelpCommand() {
   global $prompt;
   global $command;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   //cd, cd.., cp, help, ls, ls -lsa, mv, pwd

   $output[] = "Copyright 2021, 2024 5 Mode" . "\n";
   $output[] = "Invenktory is licensed GNUv3" . "\n";
   $output[] = "" . "\n";
   $output[] = "Supported commands are:" . "\n";
   $output[] = "cd" . "\n";
   $output[] = "cd .." . "\n";
   $output[] = "cp" . "\n";
   $output[] = "edit" . " " . "[CTRL]+[X]=Close [CTRL]+[S]=Save" . "\n";
   $output[] = "help" . "\n";
   $output[] = "ls" . "\n";
   $output[] = "mv" . "\n";
   $output[] = "pwd" . "\n";
   $output[] = "show" . "\n";
   $output[] = "\n";
   $output[] = "Thx for using Invenktory! :)" . "\n";
   $output[] = "\n";
   
   // Update History
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function myExecPWDCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($command, $output);

   // Update history..
   foreach ($output as &$val) {
	 if (mb_stripos("~".$val,APP_INV_PATH)) {  
	   $val = str_replace(dirname(APP_INV_PATH), "~ ", $val) . "\n";
	 }  
   }	 
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function myExecShowCommand() {
   global $prompt;
   global $command;
   global $param1;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $path = $curPath . DIRECTORY_SEPARATOR . $param1; 
   $xml = simplexml_load_file($path);

   $output[] = "\n";

   $output[] = "Location: " . $xml->attributes()['location'] . "\n";
   $output[] = "\n";
   
   // Printing out the item descriptions
   foreach ($xml->children() as $child) {
     $text = $child->DESCRIPTION;
     
     $text = rtrim1($text, PHP_SPACE . chr(13) . chr(10) . chr(32) . "\n");
     $text = ltrim1($text, PHP_SPACE . chr(13) . chr(10) . chr(32) . "\n");
     
     //print_r("*" . right($text, 1) . "*");
     //print_r(ord(right($text, 1)));
     $output[] = $child->attributes()['type'] . " #" . $child->INDEX . ":\n" . $text . "\n\n";
   }
   
   // Update History
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   $str = trim($command);
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $cmd = $str;
	 return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, PHP_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param1 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param1 = $str;
	 return;
   }	     
  
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }
 
 function cdparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
    global $param2;
    global $param3;

    //opt==""
    if ($opt!=PHP_STR) {
	  updateHistoryWithErr("invalid options");	
      return false;
    }
    //param1==""
	if ($param1===PHP_STR) {
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	    	 
	//param1!="" and !isword
	if (($param1!==PHP_STR) && !is_word($param1)) {
	  updateHistoryWithErr("invalid dir");	
      return false;
    }
    //param2==""
	if ($param2!==PHP_STR) {
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	
    //param3==""
	if ($param3!=PHP_STR) {
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	
	//param1 exist and is_dir
	$path = $curPath . PHP_SLASH . $param1;
	if (!file_exists($path) || !is_dir($path)) {
	  updateHistoryWithErr("dir doesn't exist");	
	  return false;
	}  	
	return true;
 }	 
 
 function is_subfolderdest(string $path): bool 
 {
	global $curPath;
	
	$ret=false;
	
	if ($path === "../") {
	  return $ret;	
	}	
	
	if ($path!=PHP_STR) {
	  $folderName = left($path, strlen($path)-1);

      if (!is_word($folderName)) {
		return $ret;  
	  }	  

      if (is_dir($curPath . PHP_SLASH . $folderName) && (right($path,1)==="/")) {
	    $ret=true;	
	  }
    }
    return $ret;  
 }
 
 function cpparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;
	
	//ori opt!="" and opt!="-R" and opt!="-Rp"
  //ori if (($opt!==PHP_STR) && ($opt!=="-R") && ($opt!=="-Rp") && ($opt!=="-p")) {
  if ($opt!==PHP_STR) {
	  updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
    return false;
  }
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if (($param2===PHP_STR) || (!is_word($param2) && ($param2!="../") && !is_subfolderdest($param2))) {
    updateHistoryWithErr("invalid destination path");
    return false;
  }
  //param3==""
  if ($param3!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param1 != param2
  if ($param1 === $param2) {
    updateHistoryWithErr("source same as destination");
    return false;	  	
  }
	//param1 exist
	$path = $curPath . PHP_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("source must exists");	
	  return false;
	}  	
	//isword(param2) && doesn't exist 
	if (is_word($param2)) {
	  $path = $curPath . PHP_SLASH . $param2;
	  if (file_exists($path)) {
		updateHistoryWithErr("destination already exists");	
		return false;
	  }
	}    	
  // param2=="../" && is_root 
  // param2=="../" && dest exists	
  if ($param2==="../") {
	  if ($curPath === APP_INV_PATH) {	
	    updateHistoryWithErr("out of root boundary");
	    return false;
	  }  
	  $path = dirname($curPath) . PHP_SLASH . $param1;
    if (file_exists($path)) {
      updateHistoryWithErr("destination already exists");	
      return false;
    }	  
	}	
	return true;
 }

 function mvparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2;
	global $param3; 
	
	//opt!=""
  if ($opt!=PHP_STR)	{
	  updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword
	if (($param1===PHP_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
    return false;
  }	
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if (($param2===PHP_STR) || (!is_word($param2) && ($param2!="../") && !is_subfolderdest($param2))) {
    updateHistoryWithErr("invalid destination path");
    return false;
  }
  //param3!=""
  if ($param3!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param1 != param2
  if ($param1 === $param2) {
    updateHistoryWithErr("source same as destination");
    return false;	  	
  }
	//param1 exist
	$path = $curPath . PHP_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("source must exists");	
	  return false;
	}  	
	//isword(param2) && doesn't exist
	if (is_word($param2)) {
	  $path = $curPath . PHP_SLASH . $param2;
	  if (file_exists($path)) {
  		updateHistoryWithErr("destination already exists");	
	  	return false;
    }
  }  
  // param2=="../" && is_root 
  // param2=="../" && dest exists	
  if ($param2==="../") {
	  if ($curPath === APP_INV_PATH) {	
	    updateHistoryWithErr("out of root boundary");
	    return false;
	  }  
	  $path = dirname($curPath) . PHP_SLASH . $param1;
    if (file_exists($path)) {
      updateHistoryWithErr("destination already exists");	
      return false;
    }	  
	}	
	return true;
 }


function myExecEditCommand() {
   global $prompt;
   global $command;
   global $param1;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $path = $curPath . DIRECTORY_SEPARATOR . $param1; 
   loadEditBoard($path);

   // Update History
   updateRecallHistory($command, RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HISTORY_MAX_ITEMS);
 }

 function loadEditBoard($file) {
   
   global $editBoard;
   
   $xml = simplexml_load_file($file);
   
   $editBoard[] = "\n";
   $editBoard[] = "<div id='editBoard' style='display:none'>" . "\n";

   $location = $xml->attributes()['location'];

   $editBoard[] = "<label id='labLocation' for='txtLocation'>Location:&nbsp;</label><input id='txtLocation' name='txtLocation' class='standardfield' type='text' autocomplete='off' style='width:200px; height:22px; background-color: black; color:white;' value='" . $location . "'>" . "\n";

   $i=0;
   foreach ($xml->children() as $child) {
     
     $i++;
     
     $deviceType = $child->attributes()['type'];
     $description = ltrim($child->DESCRIPTION, "\n");
     $index = $child->INDEX;
     
     $editBoard[] = "<br>\n";
     $editBoard[] = "<label id='labDevice" . $i . "'>Device #" . $i . "&nbsp;</label>" . "\n";
     $editBoard[] = "<label id='labType" . $i . "' for='txtType" . $i . "'>Device type:&nbsp;</label><input id='txtType" . $i . "' name='txtType" . $i . "' class='standardfield' type='text' autocomplete='off' style='width:200px; height:22px; background-color: black; color:white;' value='" . $deviceType . "'>" . "\n";
     $editBoard[] = "<textarea id='txtDesc" . $i . "' name='txtDesc" . $i . "' class='standardfield' placeholder='description' style='position:relative; top:3px; width: 400px; height:120px; background-color: #EEEEEE; color:black; resize: none;'>";
     $editBoard[] = $description;
     $editBoard[] = "</textarea>" . "\n";

   }
   
   $i++;

   for ($y=$i;$y<=10;$y++) {
    
     $editBoard[] = "<br>\n";
     $editBoard[] = "<label id='labDevice" . $y . "'>Device #" . $y . "&nbsp;</label>" . "\n";
     $editBoard[] = "<label id='labType" . $y. "' for='txtType" . $y. "'>Device type:&nbsp;</label><input id='txtType" . $y. "' name='txtType" . $y. "' class='standardfield' type='text' autocomplete='off' placeholder='type' style='width:200px; height:22px; background-color: black; color:white;' value=''>" . "\n";
     $editBoard[] = "<textarea id='txtDesc" . $y. "' name='txtDesc" . $y. "' class='standardfield' placeholder='description' style='position:relative; top:3px; width: 400px; height:120px; background-color: #EEEEEE; color:black; border:0px; resize: none;'>";
     $editBoard[] = "</textarea>\n";
     
   }
   
   $editBoard[] = "<input type='hidden' name='editBoardDest' value='" . $file . "'>\n";
   $editBoard[] = "<br>\n";
   $editBoard[] = "</div>\n";		

 }  

 function showEditBoard() {
   global $editBoard;
   $i = 1;	 
   foreach($editBoard as $val) {
	 //echo(str_replace("\n", "<br>", $val));
   echo($val);
	 $i++;   
   }
 }
 
 function is_validxmlsynax(string $path) {
   
   $xml = rtrim1(ltrim1(file_get_contents($path), chr(10) . chr(13) . chr(32) . "\n"), chr(10) . chr(13) . chr(32) . "\n");
   
   if (left($xml, 5) !== "<?xml") {
     return false;
   }   

   if (!mb_stripos("~" . $xml, "<INVENTORY location=")) {
     return false;
   }   

   if (!mb_stripos("~" . $xml, "<ITEM type=")) {
     return false;
   }

   if (!mb_stripos("~" . $xml, "</ITEM>")) {
     return false;
   }

   if (!mb_stripos("~" . $xml, "</INVENTORY>")) {
     return false;
   }
   
   return true;
 }  
 
 function showparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;
	
	//opt!=""
  if ($opt!==PHP_STR) {
	  updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid inventory file");	
    return false;
  }
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if ($param2!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param3==""
  if ($param3!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
	//param1 exist
	$path = $curPath . PHP_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("file must exists");	
	  return false;
	}  	
	//param1 is_file
	if (!is_file($path)) {
	  updateHistoryWithErr("invalid inventory file");	
	  return false;
	}  	
  //param1 file extension == xml
  $fileExt = pathinfo($param1, PATHINFO_EXTENSION);
  if ($fileExt != "xml") {
	  updateHistoryWithErr("invalid inventory file");	
	  return false;
  }    
  //check file syntax
  if (!is_validxmlsynax($curPath . DIRECTORY_SEPARATOR . $param1)) { 
    updateHistoryWithErr("invalid file syntax");
    return false;
  }
  return true;
 } 

function editparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;
	
	//opt!=""
  if ($opt!==PHP_STR) {
	  updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid inventory file");	
    return false;
  }
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if ($param2!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param3==""
  if ($param3!=PHP_STR) {
    updateHistoryWithErr("invalid parameters");
    return false;
  }
	//param1 exist
	$path = $curPath . PHP_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("file must exists");	
	  return false;
	}  	
	//param1 is_file
	if (!is_file($path)) {
	  updateHistoryWithErr("invalid inventory file");	
	  return false;
	}  	
  //param1 file extension == xml
  $fileExt = pathinfo($param1, PATHINFO_EXTENSION);
  if ($fileExt != "xml") {
	  updateHistoryWithErr("invalid inventory file");	
	  return false;
  }    
  //check file syntax
  if (!is_validxmlsynax($curPath . DIRECTORY_SEPARATOR . $param1)) { 
    updateHistoryWithErr("invalid file syntax");
    return false;
  }
   
	return true;
 }

  
 function upload() {

   global $curPath;
   global $prompt;

   //if (!empty($_FILES['files'])) {
   if (!empty($_FILES['files']['tmp_name'][0])) {
	   
     // Updating history..
     $output = [];
     $output[] = $prompt . " " . "File upload" . "\n";   
     updateHistory($output, HISTORY_MAX_ITEMS);
	   	 
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     
     //no file uploaded
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       updateHistoryWithErr("No file uploaded.", false);
       return;
     } 
 
     foreach($uploads as &$upload) {
		
	   switch ($upload['error']) {
		 case PHP_UPLOAD_ERR_OK:
		   break;
		 case PHP_UPLOAD_ERR_NO_FILE:
		   updateHistoryWithErr("One or more uploaded files are missing.", false);
		   return;
		 case PHP_UPLOAD_ERR_INI_SIZE:
		   updateHistoryWithErr("File exceeded INI size limit.", false);
		   return;
		 case PHP_UPLOAD_ERR_FORM_SIZE:
		   updateHistoryWithErr("File exceeded form size limit.", false);
		   return;
		 case PHP_UPLOAD_ERR_PARTIAL:
		   updateHistoryWithErr("File only partially uploaded.", false);
		   return;
		 case PHP_UPLOAD_ERR_NO_TMP_DIR:
		   updateHistoryWithErr("TMP dir doesn't exist.", false);
		   return;
		 case PHP_UPLOAD_ERR_CANT_WRITE:
		   updateHistoryWithErr("Failed to write to the disk.", false);
		   return;
		 case PHP_UPLOAD_ERR_EXTENSION:
		   updateHistoryWithErr("A PHP extension stopped the file upload.", false);
		   return;
		 default:
		   updateHistoryWithErr("Unexpected error happened.", false);
		   return;
	   }
		
	   if (!is_uploaded_file($upload['tmp_name'])) {
		 updateHistoryWithErr("One or more file have not been uploaded.", false);
		 return;
	   }
		
	   // name	 
	   $name = (string)substr((string)filter_var($upload['name']), 0, 255);
	   if ($name == PHP_STR) {
         updateHistoryWithErr("Invalid file name: " . $name, false);
         return;
       } 
	   $upload['name'] = $name;
	   
	   // fileType
	   $fileType = substr((string)filter_var($upload['type']), 0, 30);
	   $upload['type'] = $fileType;	 
	   
	   // tmp_name
	   $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
	   if ($tmp_name == PHP_STR || !file_exists($tmp_name)) {
         updateHistoryWithErr("Invalid file temp path: " . $tmp_name, false);
         return;
       } 
	   $upload['tmp_name'] = $tmp_name;
	   
 	   //size
 	   $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
	   if ($size == "") {
		 updateHistoryWithErr("Invalid file size.", false);
		 return;
	   } 
	   $upload["size"] = $size;

	   $tmpFullPath = $upload["tmp_name"];
	   
	   $originalFilename = pathinfo($name, PATHINFO_FILENAME);
	   $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
	   $FileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	   
	   if ($originalFileExt!==PHP_STR) {
	     $destFileName = $originalFilename . "." . $originalFileExt;
	   } else {
		 $destFileName = $originalFilename;  
       }	   
       $destFullPath = $curPath . PHP_SLASH . $destFileName;
	   
	   if (file_exists($destFullPath)) {
		 updateHistoryWithErr("destination already exists", false);
		 return;
	   }	   
	    
	   copy($tmpFullPath, $destFullPath);

       // Updating history..
       $output = [];
       $output[] = $destFileName . " " . "uploaded" . "\n";   
       updateHistory($output, HISTORY_MAX_ITEMS);
  
	   // Cleaning up..
	  
	   // Delete the tmp file..
	   unlink($tmpFullPath); 
	    
	 }	 
 
   }
 }	  
  
 function saveEditBoard() {
    
    global $editBoardParams;
    
    if (!empty($editBoardParams) && $editBoardParams[0]['location']!=PHP_STR) {
    
      $xml = PHP_STR;
      $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
      $xml .= "<INVENTORY location='" . HTMLencode($editBoardParams[0]['location']) . "'>";
      
      for($i=1;$i<=10;$i++) {
        if ($editBoardParams[$i]['type']!=PHP_STR) {
          $xml .= "<ITEM type='" . HTMLencode($editBoardParams[$i]['type']) . "'>";
          $xml .= "<DESCRIPTION>";
          $xml .= "<![CDATA[";
          $xml .= HTMLencode($editBoardParams[$i]['desc']);
          $xml .= "]]>";
          $xml .= "</DESCRIPTION>";
          $xml .= "<INDEX>" . $i . "</INDEX>";
          $xml .= "</ITEM>";
        } else {
          break;
        }    
      }  
      
      $xml .= "</INVENTORY>";
      
      file_put_contents($editBoardParams[0]['file'], $xml);
    }
    
 }
  
  
 $password = filter_input(INPUT_POST, "Password");
 $command = filter_input(INPUT_POST, "CommandLine");
 
 $pwd = filter_input(INPUT_POST, "pwd"); 
 $hideSplash = filter_input(INPUT_POST, "hideSplash");
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash");

 //EditBoard
 if (filter_input(INPUT_POST, "txtLocation")!==PHP_STR) {
   $editBoardParams[0] = [
     'file' => filter_input(INPUT_POST, "editBoardDest"),
     'location' => filter_input(INPUT_POST, "txtLocation")
     ];
   for($i=1;$i<=10;$i++) {
     $editBoardParams[$i] = [ 
         'type' => filter_input(INPUT_POST, "txtType" . $i),
         'desc' => filter_input(INPUT_POST, "txtDesc" . $i)
       ];   
   }
 }  

 if ($password !== PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);

	if ($hash !== APP_HASH) {
	  $password=PHP_STR;	
    }	 
 } 
 
 $curPath = APP_INV_PATH;
 if ($pwd!==PHP_STR) {
   if (left($pwd, strlen(APP_INV_PATH)) === APP_INV_PATH) {
     $curPath = $pwd;
     chdir($curPath);	   
   }	    
 }	 
 $ipos = strripos($curPath, PHP_SLASH);
 $curDir = substr($curPath, $ipos);
 
 $prompt = str_replace("$1", $curDir, APP_PROMPT);
 
 if ($password !== PHP_STR) {
   
   loadRecallHistory();
   $cmdHistory = file(dirname(__DIR__) . PHP_SLASH . "logs" . PHP_SLASH . ".INV_history");
   
   parseCommand($command);
   //echo("cmd=" . $cmd . "<br>");
   //echo("opt=" . $opt . "<br>");
   //echo("param1=" . $param1 . "<br>");
   //echo("param2=" . $param2 . "<br>");
   
   upload();
   saveEditBoard();   
   
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     
     if ($command === "cd ..") {
	   
	     $ipos = strripos($curPath, PHP_SLASH);
	     $nextPath = substr($curPath, 0, $ipos);
	   
	     if (strlen(APP_INV_PATH) > strlen($nextPath)) {
         updateHistoryWithErr("out of root boundary");
       } else {
		     myExecCDBackwCommand();
	     }	

     } else if ($command === "help") {
   
       myExecHelpCommand();	 
 
     } else if ($command === "ls") {
   
       myExecLSCommand();	 
 
     } else if ($command === "pwd") { 
   
       myExecPWDCommand();
      
     } else {
       myExecCommand(); 
     }
 
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
       
     if ($cmd === "cd") {
       if (cdparamValidation()) {
         myExecCDFolderCommand();
       }	     
     } else if ($cmd === "cp") {
       if (cpparamValidation()) {
         myExecCPCommand();
       }	     
     } else if ($cmd === "mv") {
       if (mvparamValidation()) {
         myExecCommand();
       }	     
     } else if ($cmd === "show") {
       if (showparamValidation()) {
         myExecShowCommand();
       }	     
     } else if ($cmd === "edit") {
       if (editparamValidation()) {
         myExecEditCommand();
       }	     
     } 	   
  	   
       
   } else {
     
     // if I'm not saving data..
     if (empty($editBoardParams) || $editBoardParams[0]['location']===PHP_STR) {
       if (empty($_FILES['files']['tmp_name'][0])) {  
         updateHistoryWithErr("invalid command");
       }
     }      
   }
      
 } else {
   
   $cmdHistory = [];	 
 
 }
 
 ?>
 

<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
	
  <meta charset="UTF-8"/>
  <meta name="style" content="day1"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
<!--
    Copyright 2021, 2024 5 Mode

    This file is part of Invenktory.

    Invenktory is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Invenktory is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Invenktory. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
  <title>Invenktory: every device its place..</title>
	
  <link rel="shortcut icon" href="./res/favicon66.ico?v=<?php echo(time()); ?>" />
    
  <meta name="description" content="Welcome to <?php echo(APP_NAME); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="noindex"/>
  
  <script src="./js/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script src="./js/common.js" type="text/javascript"></script>
  <script src="./js/bootstrap.min.js" type="text/javascript"></script>
  <script src="./js/sha.js" type="text/javascript"></script>
  
  <script src="./js/home.js" type="text/javascript" defer></script>
  
  <link href="./css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="./css/style.css?v=<?php echo(time()); ?>" type="text/css" rel="stylesheet">
     
  <script>
	
	 $(document).ready(function() {
	  
		 $(document).on("keydown",function(e){
		   key = e.which;
		   if (key===88 && e.ctrlKey && ($("#editBoard").css("display")!="none")) {
			   // Closing..
         e.preventDefault();
         $("#editBoard").hide();
         $("#CommandL").show()
         document.getElementById("CommandLine").focus();
       } else if (key===83 && e.ctrlKey && ($("#editBoard").css("display")!="none")) {
         // Saving data
         if ($("#txtLocation").val()==="") {
           $("#txtLocation").addClass("editemptyfield");
           document.getElementById("txtLocation").focus();
           return;
         }  
         if ($("#txtType1").val()==="") {
           $("#txtType1").addClass("editemptyfield");
           document.getElementById("txtType1").focus();
           return;
         }  
         if ($("#txtDesc1").val()==="") {
           $("#txtDesc1").addClass("editemptyfield");
           document.getElementById("txtDesc1").focus();
           return;
         }  
         // 1 2 3 4 5 6 7 8 9 10
         for(i=2;i<=10;i++) {
           y=i-1;
           if ($("#txtType"+y).val()==="") { 
             if ($("#txtType"+i).val()!="") {
               $("#txtType"+y).addClass("editemptyfield");
               document.getElementById("txtType"+y).focus();
               return;
             }
           }   
           if ($("#txtDesc"+y).val()==="") { 
             if ($("#txtDesc"+i).val()!="") {
               $("#txtDesc"+y).addClass("editemptyfield");
               document.getElementById("txtDesc"+y).focus();
               return;
             }
           }   
         }  
         e.preventDefault();
         frmHC.submit();
		   } else { 
         //$("#Salt").val(key);
		   }
		 });

		 $("#CommandLine").on("keydown",function(e){
		   key = e.which;
		   //alert(key);
		   if (key===13) {
			 e.preventDefault();
			 frmHC.submit();
		   } else { 
			 //e.preventDefault();
		   }
		 });

   });
		  
   window.addEventListener("load", function() {		 
		 <?php if($password===PHP_STR):?>
		    $("#Password").addClass("emptyfield");
		 <?php endif; ?>
     maxY = document.getElementById("Console").scrollHeight;
     //alert(maxY);
     if (document.getElementById("editBoard")) {
		   document.getElementById("txtDesc1").focus();
     } else {
       //maxY = document.getElementById("Consolep").scrollHeight;
       document.getElementById("CommandLine").focus();
     }    
     //document.getElementById("Console").scrollTop=maxY;
	 }, true);

  function startApp() {
	  $("#HCsplash").hide();
	  $("#frmHC").show();
    <?php if (!empty($editBoard)): ?>
    $("#editBoard").show();
    $("#CommandL").hide();
    <?php endif; ?>
    if (document.getElementById("editBoard")) {
		   document.getElementById("txtDesc1").focus();
    }
	}			
  <?php if($hideHCSplash!=="1"): ?>
	window.addEventListener("load", function() {
	
	  $("#HCsplash").show();	  
	  setTimeout("startApp()", 5000);
	  
	}, true);
	<?php else: ?>
  window.addEventListener("load", function() {
		  
	  startApp();
	  
	});	
  <?php endif; ?>

  </script>    
    
</head>
<body>

<div id="HCsplash" style="padding-top: 200px; text-align:center;" style="display:none;">
   <img src="res/INVsplash.gif" style="width:310px;">
</div>

<form id="frmHC" method="POST" action="/" target="_self" enctype="multipart/form-data" style="display:<?php echo(($hideHCSplash==="1"?"inline":"none"));?>;">

<div class="header">
   <a href="http://invenktory.com" target="_blank" style="color:white; text-decoration: none;"><img src="res/INVlogo.png" style="width:48px;">&nbsp;Invenktory</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://github.com/par7133/Invenktory" style="color:#ffffff"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:info@invenktory.com" style="color:#ffffff"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="tel:+39-331-4029415" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:white;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a>
</div>
	
<div style="clear:both; float:left; padding:8px; width:15%; height:100%; text-align:center;">
	<div style="padding-left:12px;text-align: left;">
	  <!--&nbsp;Upload-->
	  &nbsp;<a href="#" id="upload" style="<?php echo(($password===PHP_STR?'text-decoration:none;color:gray;':'color:#ffffff;')); ?>" onclick="<?php echo(($password!==PHP_STR?'upload()':'')); ?>">Upload</a>
	  <input id="files" name="files[]" type="file" accept=".xml" style="visibility: hidden;">
	</div>
    <br><br>
    <img src="res/INVgenius.png" alt="HC Genius" title="HC Genius" style="position:relative; left:+6px; width:90%; border: 1px dashed #EEEEEE;">
    &nbsp;<br><br><br>
    &nbsp;<input type="text" id="Password" name="Password" placeholder="password" style="font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off"><br>
    &nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" autocomplete="off"><br>
    &nbsp;<a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#ffffff; font-size:12px;">Hash Me!</a>     
</div>

<div style="float:left; width:85%;height:100%; padding:8px; border-left: 1px solid #2c2f34;">
	
	<?php if (APP_SPLASH): ?>
	<?php if ($hideSplash !== PHP_STR): ?>
	<div id="splash" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeSplash();" style="position:relative; left:-10px;">
          <span aria-hidden="true">&times;</span>
       </button>
	
	   Hello and welcome to Invenktory!<br><br>
	   
	   Invenktory is a light and simple software on premise to track your devices.<br><br>
	   
	   Invenktory is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run Invenktory in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Check the permissions of your "Inventory" folder in your web app private path; and set its path in the config file.</li>
	   <li>In the Inventory path create a ".INVsampledir" folder and give to this folder the write permission. This folder will be the sample folder to copy from new folders inside the inventory path.</li>
	   <li>Likewise, in the Inventory path must exist ".INVsamplefile.xml" and give to this file the write permission. This file will be the sample file to copy from new inventory files.</li>
	   <li>Configure the max devices per xml file as required (default: 10).</li>	      
     <li>Configure the max history items and max recall history items as required (default: 50).</li>	      
	   </ol>
	   
	   <br>	
	   
	   Invenktory understands a limited set of commands with a far limited set of parameters:<br>
	   cd, cd.., cp, edit, help, ls, mv, pwd, show<br><br>	   
	   
     In edit mode press [CTRL]+[X] to exit or [CTRL]+[S] to save.<br><br> 
     
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@invenktory.com" style="color:#e6d236;">info@invenktory.com</a>
	   
	</div>	
	<?php endif; ?>
	<?php endif; ?>
	
	&nbsp;Console<br>
	<div id="Console" style="height:493px; overflow-y:auto; margin-top:10px;">
  <!--<div id="Console" style="height:493px; margin-top:10px;">-->
	<pre id="Consolep" style="margin-left:5px;padding-left:0px;border:0px;background-color: #000000; color: #ffffff;">
<?php showHistory($cmdHistory); ?>
<?php showEditBoard(); ?>
<div id="CommandL" style="position:relative;top:3px;"><label id="Prompt" for="CommandLine"><?php echo($prompt); ?></label>&nbsp;<input id="CommandLine" name="CommandLine" list="CommandList" type="text" autocomplete="off" style="width:80%; height:22px; background-color: black; color:white; border:0px; border-bottom: 1px dashed #EEEEEE;"></div>	
	</pre>	
	</div>
	
	<datalist id="CommandList">
	<?php foreach($cmdRecallHistory as &$val): ?>
	<?php $val = left($val, strlen($val)-1); ?>
	<?php echo("<option value='$val'>\n"); ?>
	<?php endforeach; ?>	
	</datalist>	
	
	<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project and <a href="http://wysiwyg.systems">WYSIWYG</a> system. Some rights reserved.</span></div>	
</div>

<input type="hidden" name="pwd" value="<?php echo($curPath); ?>" style="color:black">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">

</form>

</body>	 
</html>	 
