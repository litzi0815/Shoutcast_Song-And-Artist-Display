<?php
// Connect to db
mysql_connect('localhost','local','local');
mysql_select_db('artist_info');

// Read info from shoutcast server
$buffer=file_get_contents('http://127.0.0.1:8000/index.html?sid=1');
$last=file_get_contents('/var/tmp/nowplaying_last.dat'); // read last update time
if(!is_numeric($last))
{
	$last=0;
}

if($last > time()-10 && $last!=0) // get data from buffer if it's not older than 10 seconds
{
	$playing=file_get_contents('/var/tmp/nowplaying_data.dat');
} else {
	// update time
	$File = "/var/tmp/nowplaying_last.dat";
	$Handle = fopen($File, 'w');
	$wData = time();
	fwrite($Handle, $wData);
	fclose($Handle);

	// parse data to get title
	$buffer1=explode('<a href="currentsong?sid=1">',$buffer);
	$buffer2=explode('</a>',$buffer1[1]);
	$playing=$buffer2[0];
	
	// update buffer
	$File = "/var/tmp/nowplaying_data.dat";
	$Handle = fopen($File, 'w');
	$wData = $buffer2[0];
	fwrite($Handle, $wData);
	fclose($Handle);
}
echo '<u>'.$playing.'</u>'; // echo artist and song

// get additional information about this artist from db (if available)
$buffer=explode(' - ',$playing);
$artist=str_replace('Music by: ','',$buffer[0]); // This is just a modification i needed. You can delete that line

$sql='SELECT artist_info FROM artist_info WHERE artist_name="'.$artist.'"';
$sql_q=mysql_query($sql);
$row=mysql_fetch_array($sql_q);
if(isset($row['artist_info']))
{
	echo '<br>'.$row['artist_info'];
}
?>