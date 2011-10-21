<?php

$limit = 100;

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

if (array_key_exists('submit', $_POST)) {
	$pseudo  = stripslashes($_POST['pseudo']);
	$message = stripslashes($_POST['message']);
	$time    = time();
	write_data(array($time, $pseudo, $message));
}

$d = read_data();
$rev_d = array_reverse ($d);
?>
<html>
<head>
    <title>Oneliner</title>

    <style type="text/css">
        body { 
            background-color: #fffebe;
            font-family: "Comic Sans MS", "Comic Sans", cursive, Verdana, Arial, Helvetica, sans-serif;
            color: black;
            text-decoration: none;
        }
        
        form {
            border : 1px dotted black;
            background-color: #efeeae;
            border-radius: 10px;
            margin:2%;
        }
        
        input {
            font-size: 120%;
            color: #000000;
            padding: 0.5%;
            margin: 1%;
            border-radius: 5px;
		}
			
		input.text {
            text-indent: 0.5em;
            border: 1px solid black;
            background-color: #fffebe;
        }

		#message {
			width:90%;
			display: block;
		}

        .pseudo {
            color: #FF6600
        }

        .pseudobracket {
            color: #7DBFF1
        }
        
        li {
            list-style : none;
            margin-top 1em;
        }
    </style>
</head>
<body>

    <img src="oneliner.jpg" />

    <form method="post">
        <input type="text" class="text" size="25" id="message" name="message" value="Message" onclick="if(this.value=='Message') this.value=''" />
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
    
    echo <<<END
        <li>
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
