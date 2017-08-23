<!DOCTYPE html>
<html>
<head>
	<title>AmbiLamp - Home</title>
  <!-- changed the href and src to the file paths on my Desktop -->
	<link rel="stylesheet" type="text/css" href="C:\xampp2\htdocs\assets\css\index.css">
	<script src="C:\xampp2\htdocs\assets\js\jscolor.js"></script>
</head>
<body>

<?php
  include "GPIO.php";
  include "header.php";

  /* BEGIN COLOR */
  $default_color = "EFFFC9"; //Initialize default color

  $db = connectMongo();
  $color_data = $db->color;
  $colorCursor = $color_data->find()->sort(array('entry' => -1))->limit(1); //Create query

  foreach($cursorColor as $doc) {
    $default_color = $doc['data']; //Change initialized default color to the one specified in the database. May need to add initial default color to database
  }

  $color = $default_color;  //change color at the time of the new session to the default color
  if (isset($_POST['set_color'])) { 
    $color = $_POST['color'];
  }

  // This should send the current color to the database when "set_default" is set
  if (isset($_POST['set_default'])) {
    $color_dict = array(
      'color' => $_POST['color']
      );
      $color_data->insert( $_POST[ $colorCursor, $color_dict ]); //Send dictionaries, not variables. See Slack and PHP documentation
  }
   /* END COLOR */
	
  /* BEGIN LED CODE */
	
	/********************************************************
	 * Use the LED schematic in Challenge 2, LED Circuit
	 * to complete these constructor lines.
	 *********************************************************/
	$red = new GPIO(22, "out",4);
	$green = new GPIO(27, "out",3);
	$blue = new GPIO(17, "out",1);

	$colorArray = $color.str_split();

	/*********************************************************
	 * Our colors are in hexadecimal - that is, come in the 
	 * form #------ where each dash is a character in the set
	 * {0 1 2 3 4 5 6 7 8 9 a b c d e f}, which is the number
	 * system in base 16. The RGB LED accepts values 0-255 for 
	 * each of the three colors. Conveniently, 255 is the
	 * largest decimal value of two hexademical digits. That
	 * is, #FF = (15 * 16^1) + (15 * 16^0) = 255. Thus, in a
	 * hex color such as #BAD94D, the red PWM value is
	 * respresented by #BA, green by #D9, and blue by #4D.
	 * The str_split() function above turns our color string
	 * into an array of characters (e.g. [B, A, D, 9, 4, D])
	 * and we pwm_write() red with the decimal value of #BA in
	 * the line below. Follow this reasoning to complete the
	 * pwm_write()inputs for green and blue.
	 *********************************************************/
	$red->pwm_write(hexdec($colorArray[0].$colorArray[1]));
	$green->pwm_write(hexdec($colorArray[2].$colorArray[3]));
	$blue->pwm_write(hexdec($colorArray[4].$colorArray[5]));

  /* END LED CODE */

  $db = connectMongo();
  $sounds = $db->sound;
  $temperatures = $db->temp;

  $soundCursor = $sounds->find()->sort(array('entry' => -1))->limit(672);
  $temperatureCursor = $temperatures->find()->sort(array('entry' => -1))->limit(672);

  /* BEGIN SOUND DATA PARSING */
  $hourSums = array_fill(0, 24, 0);
  $hourCounts = array_fill(0, 24, 0);
  foreach ($soundCursor as $doc) {
    $time = split('[- :]', $doc['time'])[3]; // get the hour of the date in 24-hour

    $hourCounts[$time] = $hourCounts[$time] + 1;
    $hourSums[$time] = $hourSums[$time] + $doc['audio'];
  }

  $soundData = '[';
  for ($i = 0; $i < 24; $i = $i + 1) {
    $hourSums[$i] = $hourSums[$i]/$hourCounts[$i];
    $soundData = $soundData . (float)$hourSums[$i] . ",";
  }
  $soundData = trim($soundData, ",");
  $soundData = $soundData . "]";
  /* END SOUND DATA PARSING */


  /* BEGIN TEMPERATURE DATA PARSING */
  $hourSums = array_fill(0, 24, 0);
  $hourCounts = array_fill(0, 24, 0);
  foreach ($temperatureCursor as $doc) {
    $time = split('[- :]', $doc['time'])[3]; // get the hour of the date in 24-hour

    $hourCounts[$time] = $hourCounts[$time] + 1;
    $hourSums[$time] = $hourSums[$time] + $doc['val'];
  }

  $temperatureData = '[';
  for ($i = 0; $i < 24; $i = $i + 1) {
    $hourSums[$i] = $hourSums[$i]/$hourCounts[$i];
    $temperatureData = $temperatureData . (float)$hourSums[$i] . ",";
  }

  $temperatureData = trim($temperatureData, ",");
  $temperatureData = $temperatureData . "]";

  /* END TEMPERATURE DATA PARSING */

  echo "<script>";
  echo "var soundData = " . $soundData . ";";
  echo "var temperatureData = " . $temperatureData . ";";
  echo "</script>";
?>

<!-- JSCOLOR PICKER -->
<input type="button" class="jscolor" id="picker" onchange="update(this.jscolor)" onfocusout="apply()" value=<?php echo "'" . $color . "'"; ?> >

<!-- FORM -->
<form method="POST">
	<input type="text" id="color" name="color">
  <input type="submit" id="smt" name="set_color" hidden>
  <!-- Added onchange to update default_color when 'Set as Default' is clicked on -->
	<input type="submit" value="Set as Default" id="set_default">
  <!-- onchange="updateDefault(this.jscolor, color)">  -->
</form>

<!-- CHARTS -->
<div id="charts-container">
	<canvas id="temp-chart" class="chart" width="550" height="350"></canvas>
	<canvas id="sound-chart" class="chart" width="550" height="350"></canvas>
</div>

<!-- ABOUT -->
<div id="about">
	<h1>About</h1>

	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam accumsan libero nec lacinia vulputate. Aliquam dignissim ultrices pharetra. Sed blandit cursus purus ut porta. Aliquam erat volutpat. Phasellus eleifend porta leo. Nulla id ex enim. Morbi sed enim tempus sem consectetur sagittis. Integer eu gravida erat. Phasellus sed massa neque. Nunc efficitur vehicula dolor, sit amet laoreet nisi pharetra nec. Cras neque justo, lacinia at nibh nec, bibendum ornare mi.</p>

	<p>Ut et porttitor odio. Morbi sollicitudin ultricies mi dictum sollicitudin. Nam placerat ex at tortor venenatis volutpat quis eu elit. Nunc fringilla tempor vestibulum. Nam mattis finibus justo ut cursus. Sed at felis a massa pellentesque consequat rutrum ut dolor. Mauris euismod elit arcu, vel tempor orci varius a. Etiam nibh dolor, pharetra eget lorem sagittis, elementum convallis diam. Aenean suscipit eros eu metus aliquam, vel commodo ante cursus. Morbi ultrices velit ut lectus mollis ultrices. Morbi nulla ligula, euismod vitae lectus vitae, efficitur tincidunt ligula. Vivamus id arcu vel leo auctor fringilla vitae eget orci. Donec at orci quis eros bibendum pharetra. Quisque molestie feugiat lobortis.</p>

	<p>Aliquam enim nisl, faucibus sit amet vulputate ac, vestibulum ut diam. Donec eu lectus diam. Morbi finibus, mauris ut mattis consectetur, sem lorem tristique mauris, a dignissim lectus felis ut leo. Fusce gravida urna in ultrices blandit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam ex diam, sollicitudin ac scelerisque sed, consequat eget mi. Fusce facilisis justo massa. In eros erat, ullamcorper ac magna ac, tincidunt lobortis sem. Praesent ultrices facilisis libero in bibendum. Praesent porta venenatis velit a aliquet. Pellentesque fermentum aliquet neque, a euismod quam pretium sed. Cras vestibulum consectetur urna, sit amet fermentum justo hendrerit non. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent sed vulputate tortor, vel pulvinar eros.</p>

	<p>Fusce venenatis orci dapibus magna ultricies ultricies. Vestibulum at turpis imperdiet, faucibus purus ac, rutrum justo. Suspendisse lobortis pharetra tortor, fermentum venenatis magna hendrerit nec. Nunc nec lectus nibh. Donec pellentesque turpis at mi scelerisque, sodales dictum nulla malesuada. Maecenas fermentum et lacus id tempus. Praesent lobortis, nibh et tristique pellentesque, justo justo fringilla purus, sollicitudin porttitor metus justo sit amet massa. Sed aliquet egestas nisl. Proin dapibus tincidunt libero, et vehicula ex laoreet ornare. Sed convallis ante orci, accumsan egestas nisl elementum sed.</p>

	<p>Nullam quis convallis augue, eget convallis quam. Nam et viverra nisi. In vel placerat purus, sed tempus lacus. Vestibulum erat magna, elementum quis felis vel, tempus porta lacus. Nunc porttitor molestie dapibus. Fusce ac felis non augue rhoncus pharetra. Aliquam nisi est, molestie quis quam sed, porttitor laoreet felis. Suspendisse pellentesque aliquam ex in interdum. Fusce nunc sem, molestie laoreet ipsum in, suscipit tempor nulla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin a nulla convallis, sollicitudin nisl vel, placerat lorem.</p>
</div>

<script type="text/javascript" src="assets/js/index.js"></script>
</body>
</html>
