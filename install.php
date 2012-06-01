<?php
function makeBar($type = "info", $content = NULL) {
	if ($type == "success") {
		$class = "alert alert-success";
		$iconClass = "icon-ok icon-white";
	} elseif ($type == "error") {
		$class = "alert alert-error";
		$iconClass = "icon-remove icon-white";
	} else {
		$class = "alert alert";
		$iconClass = "icon-ok icon-white";
	}
	
	$output  = "<div class=\"" . $class . "\">";
	$output .= "<i class=\"" . $iconClass . "\"></i>&nbsp;&nbsp;&nbsp;";
	$output .= $content;
	$output .= "</div>";
	
	return $output;
}
?>
<?php include_once ("views/html_head.php"); ?>
<div class="container">
	<div class="row-fluid">
		<div class="span12">
			<div class="page-header">
				<h1>Let's Get Installing</h1>
			</div>

			<?php
			// check for config
			$file1 = getcwd() . "/engine/config.php";
			
			if (file_exists($file1)) {
				echo makeBar("success", "config.php located");
				include_once($file1);
			} else {
				echo makeBar("error", "Can't find config.php (I tried, honestly!) at " . $_SERVER['DOCUMENT_ROOT'] . "/engine");
			}
			
			// check for files
			$file1 = SITE_LOCATION . "/nodes/admin_index.php";
			
			if (file_exists($file1)) {
				echo makeBar("success", "It seems the files/folders are all located in the root of " . SITE_LOCATION);
			} else {
				echo makeBar("error", "Files aren't located in " . SITE_LOCATION . " - or at least, I couldn't find: " . $file1 . ". Are you sure you've configured config.php?");
			}
			
			// check for uploads permissions
			$permissions = substr(sprintf('%o', fileperms('uploads')), -4);
			$permissions = substr($permissions, 1, 3);
			
			if ($permissions == "755") {
				echo makeBar("success", "Permissions on uploads set to <code>" . $permissions . "</code>");
			} elseif ($permissions == "777") {
				echo makeBar("alert", "Permissions on uploads set to <code>" . $permissions . "</code> which will work, but isn't advisable");
			} else {
				echo makeBar("error", "Permissions on uploads set to <code>" . $permissions . "</code> which isn't what I wanted! (755)");
			}
			
			// check for database connection
			require_once(SITE_LOCATION . "/engine/database.php");
			$connection = $database->query("SHOW TABLES");
			
			if (!$connection) {
				echo makeBar("error", "Database connection failed to " . DB_NAME . ". Are you sure the details are right in config.php?");
			} else {
				echo makeBar("success", "Database connection to " . DB_NAME . " successful");
				
			}
			
			if (isset($_POST['db_username'])) {
				$dbms_schema = 'engine/tableInstaller.sql';
				
				$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('problem ');
				$sql_query = remove_remarks($sql_query);
				$sql_query = split_sql_file($sql_query, ';');
				
				$host = DB_SERVER;
				$user = $_POST['db_username'];
				$pass = $_POST['db_password'];
				$db = DB_NAME;
				
				mysql_connect($host,$user,$pass) or die('error connection');
				mysql_select_db($db) or die('error database selection');
				
				$i=1;
				
				foreach($sql_query as $sql) {
					echo $i++;
					echo " ";
					mysql_query($sql) or die('error in query');
				}
			}
			
			if (mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table . "'"))) {
				echo makeBar("success", "I've taken a peak inside the database, and it all looks ok.  Great job!");
			} else {
				$formOutput  = "<form method=\"post\" class=\"well form-inline\">";
				$formOutput .= "<input type=\"\" name=\"db_username\" class=\"input-small\" placeholder=\"DB Username\" value=\"breakspeara\">";
				$formOutput .= "<input type=\"password\" name=\"db_password\" class=\"input-small\" placeholder=\"Password\">";
				$formOutput .= "<button type=\"submit\" class=\"btn btn-warning\">Create Tables?</button>";
				$formOutput .= "</form>";
														
				echo makeBar("error", "Tables not setup in database yet." . $formOutput);
			}
			?>
			
			<a class="btn btn-large btn-success" href="index.php"><i class="icon-ok icon-white"></i>&nbsp;&nbsp;You're Ready To Go!</a>
		</div>
	</div>
</div>
</body>
</html>



<?php
ini_set('memory_limit', '5120M');
set_time_limit ( 0 );
/***************************************************************************
*                             sql_parse.php
*                              -------------------
*     begin                : Thu May 31, 2001
*     copyright            : (C) 2001 The phpBB Group
*     email                : support@phpbb.com
*
*     $Id: sql_parse.php,v 1.8 2002/03/18 23:53:12 psotfx Exp $
*
****************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

/***************************************************************************
*
*   These functions are mainly for use in the db_utilities under the admin
*   however in order to make these functions available elsewhere, specifically
*   in the installation phase of phpBB I have seperated out a couple of
*   functions into this file.  JLH
*
\***************************************************************************/

//
// remove_comments will strip the sql comment lines out of an uploaded sql file
// specifically for mssql and postgres type files in the install....
//
function remove_comments(&$output)
{
   $lines = explode("\n", $output);
   $output = "";

   // try to keep mem. use down
   $linecount = count($lines);

   $in_comment = false;
   for($i = 0; $i < $linecount; $i++)
   {
      if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
      {
         $in_comment = true;
      }

      if( !$in_comment )
      {
         $output .= $lines[$i] . "\n";
      }

      if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
      {
         $in_comment = false;
      }
   }

   unset($lines);
   return $output;
}

//
// remove_remarks will strip the sql comment lines out of an uploaded sql file
//
function remove_remarks($sql)
{
   $lines = explode("\n", $sql);

   // try to keep mem. use down
   $sql = "";

   $linecount = count($lines);
   $output = "";

   for ($i = 0; $i < $linecount; $i++)
   {
      if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
      {
         if (isset($lines[$i][0]) && $lines[$i][0] != "#")
         {
            $output .= $lines[$i] . "\n";
         }
         else
         {
            $output .= "\n";
         }
         // Trading a bit of speed for lower mem. use here.
         $lines[$i] = "";
      }
   }

   return $output;

}

//
// split_sql_file will split an uploaded sql file into single sql statements.
// Note: expects trim() to have already been run on $sql.
//
function split_sql_file($sql, $delimiter)
{
   // Split up our string into "possible" SQL statements.
   $tokens = explode($delimiter, $sql);

   // try to save mem.
   $sql = "";
   $output = array();

   // we don't actually care about the matches preg gives us.
   $matches = array();

   // this is faster than calling count($oktens) every time thru the loop.
   $token_count = count($tokens);
   for ($i = 0; $i < $token_count; $i++)
   {
      // Don't wanna add an empty string as the last thing in the array.
      if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
      {
         // This is the total number of single quotes in the token.
         $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
         // Counts single quotes that are preceded by an odd number of backslashes,
         // which means they're escaped quotes.
         $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

         $unescaped_quotes = $total_quotes - $escaped_quotes;

         // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
         if (($unescaped_quotes % 2) == 0)
         {
            // It's a complete sql statement.
            $output[] = $tokens[$i];
            // save memory.
            $tokens[$i] = "";
         }
         else
         {
            // incomplete sql statement. keep adding tokens until we have a complete one.
            // $temp will hold what we have so far.
            $temp = $tokens[$i] . $delimiter;
            // save memory..
            $tokens[$i] = "";

            // Do we have a complete statement yet?
            $complete_stmt = false;

            for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
            {
               // This is the total number of single quotes in the token.
               $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
               // Counts single quotes that are preceded by an odd number of backslashes,
               // which means they're escaped quotes.
               $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

               $unescaped_quotes = $total_quotes - $escaped_quotes;

               if (($unescaped_quotes % 2) == 1)
               {
                  // odd number of unescaped quotes. In combination with the previous incomplete
                  // statement(s), we now have a complete statement. (2 odds always make an even)
                  $output[] = $temp . $tokens[$j];

                  // save memory.
                  $tokens[$j] = "";
                  $temp = "";

                  // exit the loop.
                  $complete_stmt = true;
                  // make sure the outer loop continues at the right point.
                  $i = $j;
               }
               else
               {
                  // even number of unescaped quotes. We still don't have a complete statement.
                  // (1 odd and 1 even always make an odd)
                  $temp .= $tokens[$j] . $delimiter;
                  // save memory.
                  $tokens[$j] = "";
               }

            } // for..
         } // else
      }
   }

   return $output;
}
?>