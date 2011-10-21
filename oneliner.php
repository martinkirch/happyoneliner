<?php

$limit = 100;
$maxsize = 5242880; // 5*1024*1024
$flash_message = "";

function read_data () {
  $res = array();
  if (($handle = fopen('oneliner.csv', 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
      $res []= $data;
    }
    fclose($handle);
  }
  return $res;
}

function write_data($data) {
  $fp = fopen('oneliner.csv', 'a+');
  fputcsv($fp, $data);
  fclose($fp);
}

function get_unique_filename_for($attachment) {
    $extension = substr($attachment['name'],-4);
    $name = md5($attachment['tmp_name']) . $extension;
    while (file_exists($name)) {
        $name = md5($name) . $extension;
    }
    return $name;
}

if (array_key_exists('submit', $_POST)) {
	$pseudo  = stripslashes($_POST['pseudo']);
	$message = stripslashes($_POST['message']);
	$time    = time();
	$file    = "";
	if (array_key_exists('attachment', $_FILES)) {
	    $attachment = $_FILES['attachment'];
	    if ($attachment['error'] == 0 || $attachment['size'] > $maxsize) {
	        $file = get_unique_filename_for($attachment);
	        if(!move_uploaded_file($attachment['tmp_name'], $file)) {
    	        $flash_message = "Ooops. file upload failed.";
    	    }
	    } else {
	        $flash_message = "Ooops. file upload failed or file is too big.";
	    }
	}
	write_data(array($time, $pseudo, $message, $file));
}

$d = read_data();
$rev_d = array_reverse ($d);
?>
<html>
<head>
    <title>Oneliner</title>
    <link rel="stylesheet" href="oneliner.css" type="text/css" media="screen" charset="utf-8">
</head>
<body>

    <img src="oneliner.jpg" />
    
    <?php if (!empty($flash_message)) echo "<p>$message</p>\n" ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" class="text" size="25" id="message" name="message" value="Message" onclick="if(this.value=='Message') this.value=''" />
        <span id="attachmentForm">
            <label for="attachment">Attach file :</label>
            <input type="file" name="attachment" id="attachment" />
        </span>
        <input type="text" class="text" size="10" maxlength="16" name="pseudo" value="Anonymous" onclick="if(this.value=='Anonymous') this.value=''" />
        <input type="submit" name="submit" value="Write" />
    </form>
    
    <ul>
<?php
$displayed = 0;
foreach ($rev_d as $line) {
    if ($limit >= 0) {
        $displayed++;
        if ($displayed > $limit) {
            break;
        }
    }
    
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
</body>
</html>