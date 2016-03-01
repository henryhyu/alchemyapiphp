<?php

require_once ('alchemyapi.php');
$alchemyapi = new AlchemyAPI();

//Use image url to detect face
if (!empty($_POST['imageurl'])) {
    $url = $_POST['imageurl'];
    $response = $alchemyapi->face_detection('url', $_POST['imageurl'], null);
    printResponse($response);
}
//Upload an image to detect face
else if (!empty($_FILES["upfile"])) {
    if ($_FILES["upfile"]["size"] < 512 * 1024) {
        if ($_FILES["upfile"]["error"] > 0) {
            echo "Fail to upload the file: " . $_FILES["upfile"]["error"] . "<br />";
        } else {
            $imageFile = fopen($_FILES["upfile"]["tmp_name"], "r") or die("Unable to open file!");
            $imageData = fread($imageFile, $_FILES["upfile"]["size"]);
            fclose($imageFile);
            //var_dump($imageFile);
            //var_dump($_FILES);
            //Do not forget to set imagePostMode option to raw
            $response = $alchemyapi->face_detection('image', $imageData, array(
                'imagePostMode' => 'raw'
            ));
            printResponse($response);
        }
    } else {
        echo "Error: only support image file with size < 512 KBytes";
    }
} else {
    echo "Error: no image found";
}

/*
 * This method prints out the response
*/
function printResponse($response) {
    $status = $response['status'];
    if ($status == 'OK') {
        $fcount = count($response['imageFaces']);
        if ($fcount > 0) {
            //header('Content-type: image/png text');
            //echo file_get_contents($_POST['imageurl']);
            echo "<img src='".$_POST['imageurl']."' alt=\"Smiley face\" width=\"250\">";
            echo "Let me say ...";
            echo ("</br>");
            $fitem = $response['imageFaces'][0];
            echo "Gender: <b>";
            echo $fitem['gender']['gender'];
            echo "</b> (Confidence: ";
            echo number_format($fitem['gender']['score'], 2);
            echo ")";
            echo ("</br>");
            echo "Age: <b>";
            echo $fitem['age']['ageRange'];
            echo "</b> (Confidence: ";
            echo number_format($fitem['age']['score'], 2);
            echo ")";
            echo ("</br>");
            echo ("</br>");
            if (array_key_exists('identity', $fitem)) {
                echo "Emmm, we guess you are <b>";
                echo $fitem['identity']['name'];
                echo "</b> (Confidence: ";
                echo number_format($fitem['identity']['score'], 2);
                echo ")";
            } else {
                echo "However, we do not know who you are exactly...";
            }
        }
        echo ("</br>");
    } else {
        echo "Sorry, we are unable to guess who you are, as: ";
        echo ("</br>");
        echo $response['statusInfo'];
        echo ("</br>");
    }
}
?>
