<?php

/**
 * Copyright (c) 2016, 2024, the Open Gallery's contributors
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither Open Gallery nor the names of its contributors 
 *       may be used to endorse or promote products derived from this software 
 *       without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * index.php
 * 
 * The index file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, the Open Gallery's contributors     
 * @license https://opensource.org/licenses/BSD-3-Clause 
 */

require "../Private/core/init.inc";


// FUNCTION AND VARIABLE DECLARATIONS

$scriptPath = APP_SCRIPT_PATH;

// PARAMETERS VALIDATION

$url = strtolower(rtrim(substr(filter_input(INPUT_GET, "url", FILTER_SANITIZE_STRING), 0, 300), "/"));

switch ($url) {
  case "":
  case "home":
    define("SCRIPT_NAME", "home");
    define("SCRIPT_FILENAME", "home.php");   
    break;   
  default:
    $scriptPath = APP_ERROR_PATH;
    define("SCRIPT_NAME", "err-404");
    define("SCRIPT_FILENAME", "err-404.php");  
}

if (SCRIPT_NAME==="err-404") {
  header("HTTP/1.1 404 Not Found");
}  

require $scriptPath . "/" . SCRIPT_FILENAME;
