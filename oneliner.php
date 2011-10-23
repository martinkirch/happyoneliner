<?php

$limit = 5;
$maxsize = 5242880; // 5*1024*1024 - attachment max in byets
$messageMaxLength = 2000;
$flash_message = '';
$dataFilename = 'oneliner.csv';

$data = array();
if (($handle = fopen($dataFilename, 'r')) !== FALSE) {
    $lineLength = $messageMaxLength + 100;
    while (($line = fgetcsv($handle, $lineLength, ',')) !== FALSE) {
      $data[] = $line;
    }
    fclose($handle);
}

function get_unique_filename_for($attachment) {
    $extension = substr($attachment['name'],-4);
    $name = 'oneliner_' . md5($attachment['tmp_name']) . $extension;
    while (file_exists($name)) {
        $name = 'oneliner_' . md5($name) . $extension;
    }
    return $name;
}

if (array_key_exists('submit', $_POST)) {
	$pseudo  = substr(stripslashes($_POST['pseudo']),0,16);
	$message = substr(stripslashes($_POST['message']),0,$messageMaxLength);
	$time    = time();
	$file    = "";
	
	if (array_key_exists('attachment', $_FILES)) {
	    $attachment = $_FILES['attachment'];
	    if ($attachment['error'] == 0 && $attachment['size'] < $maxsize) {
	        $file = get_unique_filename_for($attachment);
	        if(!move_uploaded_file($attachment['tmp_name'], $file)) {
    	        $flash_message = 'Ooops. File upload failed.';
    	    }
	    }
	}
	array_unshift($data, array($time, $pseudo, $message, $file));
	
	while (count($data) > $limit) {
	    $last = array_pop($data);
	    if (!empty($last[3]))
	        unlink($last[3]);
	}
	
    $fp = fopen($dataFilename, 'w');
    foreach ($data as $line)
    {
        fputcsv($fp, $line);
    }
    fclose($fp);
}

?>
<html>
<head>
    <title>Oneliner</title>
    <link rel="stylesheet" href="oneliner.css" type="text/css" media="screen" charset="utf-8">
</head>
<body>
<div id="container">    
    <?php if (!empty($flash_message)) echo "<p>$flash_message</p>\n" ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" class="text" maxlength="<?php echo $messageMaxLength ?>" id="message" name="message" value="Message" onclick="if(this.value=='Message') this.value=''" />
        <input type="text" class="text" size="10" maxlength="16" name="pseudo" value="Anonymous" onclick="if(this.value=='Anonymous') this.value=''" />
        <input type="submit" name="submit" value="Write" />
        <span id="attachmentForm">
            <label for="attachment">Attach file :</label>
            <input type="file" name="attachment" id="attachment" />
        </span>
    </form>
    
    <ul>
<?php
$displayed = 0;
foreach ($data as $line) {
    $time = date('d M Y H:i', $line[0]);
    
    $img = "";
    if (!empty($line[3])) {
        $img = "<img src='{$line[3]}' class='attachment' />";
    }
    
    echo <<<END
        <li> $img
            <span class="time">$time</span>
            <span class="pseudobracket">[</span>
            <span class="pseudo">{$line[1]}</span>
            <span class="pseudobracket">]</span>
            <span class="message">{$line[2]}</span>
        </li>
END;
}
?>
    </ul>
</div>
</body>
</html>