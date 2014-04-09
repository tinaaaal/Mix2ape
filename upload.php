<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);

$GLOBALS['player_template_file'] = 'player/player_standalone_minimized.html';
$GLOBALS['player_file'] = 'player/my_player.html';
$GLOBALS['file_directory'] = 'uploads/';
$GLOBALS['ffmpeg_output_directory'] = 'resample/';
$GLOBALS['output_directory'] = 'player/';
$GLOBALS['app_name'] = 'Mix2Tape';
$GLOBALS['allowedFileType'] = array('mp3', 'mp4', 'wav');
$GLOBALS['count'] = 0;
$GLOBALS['files'] = array();

$ffmpeg_bin = '/opt/local/bin/ffmpeg';
$ffmpeg_audio_opt = ' -acodec mp3 -ab 192 ';

session_start();
// if(!isset($_SESSION['n_songs']))
//     $_SESSION['n_songs'] = 0;
// else
//     $_SESSION['n_songs']++;

function convert($file){

}

function writeAudioData($file){
    $byte_array = file_get_contents($file);
    $audio = base64_encode($byte_array);

    // if($_SESSION['n_songs'] >= 1)
        // $player_file_content = file_get_contents($GLOBALS['player_file']);
    // else
        $player_file_content = file_get_contents($GLOBALS['player_template_file']);
        
    $doc = new DOMDocument();
    $doc->formatOutput = true;
    $doc->loadHTML($player_file_content);
    $playlist = $doc->getElementById('playlist');
    // echo '<pre>';
    // print_r($playlist);
    // echo '</pre>';
    // file_put_contents('php://stderr', print_r($playlist, TRUE));
    // file_put_contents('php://stderr', print_r($_SESSION['n_songs'], TRUE));

    $filename = pathinfo($_FILES['upl']['name'], PATHINFO_FILENAME);    
    $element = $doc->createElement('li', $filename);
    // echo '<pre>';
    // print_r($element);
    // echo '</pre>';
    // $audio_url = $doc->createAttribute('audiourl');
    // $audio_url->value = $audio;
    $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
    $audio_url = "data:audio/".$extension.";base64,".$audio;
    $element->setAttribute('audiourl', $audio_url);
    // $artist = $doc->createAttribute('artist');
    // $artist->value = '';
    $artist = '';
    $element->setAttribute('artist', $artist);
    // $element->appendChild($audio_url);
    // $element->appendChild($artist);

    $playlist->appendChild($element);

    file_put_contents('php://stderr', 'dom doc:');
    file_put_contents('php://stderr', print_r($doc, TRUE));
    $doc->save($GLOBALS['player_file'], LIBXML_NOEMPTYTAG);

    // $nbytes = $doc->saveHTMLFile($GLOBALS['player_file']);
    // file_put_contents('php://stderr', print_r($nbytes, TRUE));
}

function removeAudioFile($filepath){
    $audioFiles = glob($filepath);#.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}', GLOB_BRACE);
    foreach($audioFiles as $file){
        unlink($file);
        file_put_contents('php://stderr', $file.name);
    }
}
// function writeAllAudioDataInOrder($path, $order, $titles, $authors){

// function writeAllAudioDataInOrder($path, $data){
//     $order = $data['order'];
//     $titles = $data['songTitle'];
//     $authors = $data['songAuthor'];
//     $save_player_name = $GLOBALS['output_directory'].$data['player_name'].'.html';
//     file_put_contents('php://stderr', $save_player_name);

//     $audioFiles = glob($path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}', GLOB_BRACE);
//     // if($_SESSION['n_songs'] >= 1)
//         // $player_file_content = file_get_contents($GLOBALS['player_file']);
//     // else
//     $player_file_content = file_get_contents($GLOBALS['player_template_file']);
//     file_put_contents('php://stderr', $path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}');  
//     // file_put_contents('php://stderr', print_r($path, TRUE));
//     file_put_contents('php://stderr', print_r($audioFiles, TRUE));

//     $doc = new DOMDocument();
//     $doc->formatOutput = true;
//     $doc->loadHTML($player_file_content);
//     $playlist = $doc->getElementById('playlist');
            
//     // foreach($audioFiles as $file){
//     foreach($order as $index){
//         $file = $audioFiles[$index];
//         $title = $titles[$index];
//         file_put_contents('php://stderr', print_r($title, TRUE));
        
//         $byte_array = file_get_contents($file);
//         $audio = base64_encode($byte_array);
            
//         // echo '<pre>';
//         // print_r($playlist);
//         // echo '</pre>';
//         // file_put_contents('php://stderr', print_r($playlist, TRUE));
//         // file_put_contents('php://stderr', print_r($_SESSION['n_songs'], TRUE));

//         // $filename = pathinfo($file, PATHINFO_FILENAME);  
//         // $element = $doc->createElement('li', $filename);
//         $filename = pathinfo($title, PATHINFO_FILENAME);
//         $element = $doc -> createElement('li', $filename);

//         file_put_contents('php://stderr', print_r($title, TRUE));
//         // file_put_contents('php://stderr', print_r($element, TRUE));
//         // echo '<pre>';
//         // print_r($element);
//         // echo '</pre>';
//         $extension = pathinfo($file, PATHINFO_EXTENSION);
//         $audio_url = "data:audio/".$extension.";base64,".$audio;
//         $element->setAttribute('audiourl', $audio_url);
      
//         $artist = $authors[$index];
//         $element->setAttribute('artist', $artist);
     
//         $playlist->appendChild($element);

//     }
//     file_put_contents('php://stderr', print_r($playlist, TRUE));
        

//     // $doc->save($GLOBALS['player_file'], LIBXML_NOEMPTYTAG);
//     $doc->save($save_player_name, LIBXML_NOEMPTYTAG);
//     file_put_contents('php://stderr', "done!");
//     downloadFile($save_player_name);
// }

function writeAllAudioDataInOrderViaForm($path, $data){
    $order = json_decode($data['order']);
    $titles = json_decode($data['songTitle']);
    $authors = json_decode($data['songAuthor']);
    $save_player_name = $GLOBALS['output_directory'].$data['player_name'].'.html';
    // file_put_contents('php://stderr', $save_player_name);

    $audioFiles = glob($path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}', GLOB_BRACE|GLOB_NOSORT);
    usort($audioFiles, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
    file_put_contents('php://stderr', print_r($audioFiles, TRUE));
    // file_put_contents('php://stderr', var_dump($GLOBALS['files'], TRUE));
    // if($_SESSION['n_songs'] >= 1)
        // $player_file_content = file_get_contents($GLOBALS['player_file']);
    // else
    $player_file_content = file_get_contents($GLOBALS['player_template_file']);
    file_put_contents('php://stderr', $path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}');  
    // file_put_contents('php://stderr', print_r($path, TRUE));
    // file_put_contents('php://stderr', print_r($audioFiles, TRUE));

    $doc = new DOMDocument();
    $doc->formatOutput = true;
    $doc->loadHTML($player_file_content);
    $playlist = $doc->getElementById('playlist');
            
    // foreach($audioFiles as $file){
    // $ind = 0;
    // foreach($GLOBALS['files'] as $index => $f){
    foreach($order as $index => $value){
        file_put_contents('php://stderr', print_r($value, TRUE));
        // $file = $path.$f;
        $file = $audioFiles[$value];
        $title = $titles[$index];
        file_put_contents('php://stderr', print_r($file, TRUE));
        
        $byte_array = file_get_contents($file);
        $audio = base64_encode($byte_array);
            
        // echo '<pre>';
        // print_r($playlist);
        // echo '</pre>';
        // file_put_contents('php://stderr', print_r($playlist, TRUE));
        // file_put_contents('php://stderr', print_r($_SESSION['n_songs'], TRUE));

        // $filename = pathinfo($file, PATHINFO_FILENAME);  
        // $element = $doc->createElement('li', $filename);
        $filename = pathinfo($title, PATHINFO_FILENAME);
        $element = $doc -> createElement('li', $filename);

        file_put_contents('php://stderr', print_r($title, TRUE));
        // file_put_contents('php://stderr', print_r($element, TRUE));
        // echo '<pre>';
        // print_r($element);
        // echo '</pre>';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $audio_url = "data:audio/".$extension.";base64,".$audio;
        $element->setAttribute('audiourl', $audio_url);
      
        $artist = $authors[$index];
        $element->setAttribute('artist', $artist);
     
        $playlist->appendChild($element);
        // unlink($file);

    }
    file_put_contents('php://stderr', print_r($playlist, TRUE));
        

    // $doc->saveHTMLFile($save_player_name);
    // $doc->save($save_player_name, LIBXML_NOEMPTYTAG);
    $string = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
    $removed = str_replace("<![CDATA[", "", $string);
    $removed = str_replace("]]>", "", $removed);
    file_put_contents($save_player_name, $removed);
    file_put_contents('php://stderr', "done!");
    downloadFile($save_player_name);
}
function downloadFile($filepath){

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename='.basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        flush();
        readfile($filepath);
        file_put_contents('php://stderr', "sent!");
        unlink($filepath);

        // removeAudioFile($GLOBALS['ffmpeg_output_directory']);
        // removeAudioFile($GLOBALS['file_directory']);
        exit;
    }
    // $link = "http://$_SERVER[HTTP_HOST]/$GLOBALS[app_name]/$filepath";
    // pathinfo($_FILES['upl']['name'], PATHINFO_FILENAME); 
    // echo '{"download_url": "'.$link.'"}';
}
// A list of permitted file extensions
// echo 'hello!';
// var_dump($_FILES['upl']);

// echo '<pre>' . var_dump($_POST) . '</pre>';
// echo '<pre>' . var_dump($_REQUEST) . '</pre>';
// echo $_POST["email"];

if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

    // echo 'got it!';
    // echo $_FILES['upl']['name'];
	$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

	if(!in_array(strtolower($extension), $GLOBALS['allowedFileType'])){
		echo '{"status":"error"}';
        // echo 'not moved!';
		exit;
	}
    
    
    // file_put_contents('php://stderr', print_r($_POST['songId'], TRUE));
	if(move_uploaded_file($_FILES['upl']['tmp_name'], $GLOBALS['file_directory'].$_FILES['upl']['name'])){
		echo '{"status":"success"}';
// 
        // $GLOBALS['count']++;

        $GLOBALS['songs'][$_POST['songID']] = $_FILES['upl']['name'];
        // echo 'moved!';
        
        $file_i = $GLOBALS['file_directory'].$_FILES['upl']['name'];
        $file_o = $GLOBALS['ffmpeg_output_directory'].$_FILES['upl']['name'];
        $cmd = $ffmpeg_bin . ' -i ' . escapeshellarg($file_i) . $ffmpeg_audio_opt . escapeshellarg($file_o);
        // $cmd = "/usr/bin/ffmpeg -i $uploadFile -f flv -acodec mp3 -ab 64 -ac 1 -title \"Clip Title\" -author \"Clip Author\" -copyright \"Clip Copyright\" $finalFile";
        exec( $cmd );
        // file_put_contents('php://stderr', print_r($cmd, TRUE));
        $GLOBALS['files'][] = $_FILES['upl']['name'];
        // unlink($file_i);

        if(isset($_SESSION['n_songs']))
            $_SESSION['n_songs']++;
        else
            $_SESSION['n_songs'] = 1;
        
        // writeAudioData('uploads/'.$_FILES['upl']['name']);

		exit;
	}
}

if(isset($_POST['action'])){
    // if($_POST['action'] == 'WriteFile'){
    //     file_put_contents('php://stderr', print_r($_POST, TRUE));

    //     // writeAllAudioDataInOrder($GLOBALS['file_directory'], $_POST['order'], $_POST['songTitle'], $_POST['songAuthor']);
    //     // writeAllAudioDataInOrder($GLOBALS['file_directory'], $_POST);
    //     writeAllAudioDataInOrder($GLOBALS['ffmpeg_output_directory'], $_POST);
    // }
    if($_POST['action'] == 'WriteFileViaForm'){
        writeAllAudioDataInOrderViaForm($GLOBALS['ffmpeg_output_directory'], $_POST);
        
    }
}

// echo '{"status":"error"}';
exit;

?>