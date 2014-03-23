<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);

$GLOBALS['player_template_file'] = 'player/player_standalone.html';
$GLOBALS['player_file'] = 'player/my_player.html';
$GLOBALS['file_directory'] = 'uploads/';
$GLOBALS['allowedFileType'] = array('mp3', 'mp4', 'wav');

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

function writeAllAudioData($path){
    $audioFiles = glob($path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}', GLOB_BRACE);
    // if($_SESSION['n_songs'] >= 1)
        // $player_file_content = file_get_contents($GLOBALS['player_file']);
    // else
    $player_file_content = file_get_contents($GLOBALS['player_template_file']);
    file_put_contents('php://stderr', $path.'*.{'.implode(',', $GLOBALS['allowedFileType']).'}');  
    // file_put_contents('php://stderr', print_r($path, TRUE));
    file_put_contents('php://stderr', print_r($audioFiles, TRUE));

    $doc = new DOMDocument();
    $doc->formatOutput = true;
    $doc->loadHTML($player_file_content);
    $playlist = $doc->getElementById('playlist');
            
    foreach($audioFiles as $file){
        $byte_array = file_get_contents($file);
        $audio = base64_encode($byte_array);
            
        // echo '<pre>';
        // print_r($playlist);
        // echo '</pre>';
        // file_put_contents('php://stderr', print_r($playlist, TRUE));
        // file_put_contents('php://stderr', print_r($_SESSION['n_songs'], TRUE));

        // $filename = pathinfo($_FILES['upl']['name'], PATHINFO_FILENAME);  
        $filename = pathinfo($file, PATHINFO_FILENAME);  
        $element = $doc->createElement('li', $filename);

        file_put_contents('php://stderr', print_r($element, TRUE));
        // echo '<pre>';
        // print_r($element);
        // echo '</pre>';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $audio_url = "data:audio/".$extension.";base64,".$audio;
        $element->setAttribute('audiourl', $audio_url);
      
        $artist = '';
        $element->setAttribute('artist', $artist);
        // $element->appendChild($audio_url);
        // $element->appendChild($artist);

        $playlist->appendChild($element);

        // file_put_contents($player_file, $doc);
    }
    file_put_contents('php://stderr', print_r($playlist, TRUE));
        

    //$doc->saveHTMLFile($GLOBALS['player_file']);
    // file_put_contents($GLOBALS['player_file'], $doc->saveHTML());
    $doc->save($GLOBALS['player_file'], LIBXML_NOEMPTYTAG);
    file_put_contents('php://stderr', "done!");
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

	if(move_uploaded_file($_FILES['upl']['tmp_name'], $GLOBALS['file_directory'].$_FILES['upl']['name'])){
		echo '{"status":"success"}';
        // echo 'moved!';
        if(isset($_SESSION['n_songs']))
            $_SESSION['n_songs']++;
        else
            $_SESSION['n_songs'] = 1;
        
        // writeAudioData('uploads/'.$_FILES['upl']['name']);

		exit;
	}
}

if(isset($_POST['action'])){
    if($_POST['action'] == 'WriteFile'){
        writeAllAudioData($GLOBALS['file_directory']);
    }
}

echo '{"status":"error"}';
exit;

?>